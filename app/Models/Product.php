<?php

namespace App\Models;

use App\Enums\DisciplineEnum;
use App\Enums\ProductTypeEnum;
use App\Services\MagentoApiClient;
use App\Services\NeloApiClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'image',
        'external_id',
        'product_type_id',
        'discipline_id',
        'attributes',
        'attributes->hex',
        'attributes->magento_url',
        'description',
        'retail_price',
    ];

    protected $casts = [
        'attributes' => 'json',
        'external_id' => 'int',
    ];


    public function type():BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function discipline():BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function __get($key){
        if($key == 'image'){
            $media = $this->getFirstMediaUrl('magento');
            if(empty($media))
                return $this->getFirstMediaUrl('*');

            return $media;
        }
        else
            return parent::__get($key);
    }

    public function options():BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_options','main_product_id', 'sub_product_id')
            ->withPivot(['attribute_id', 'standard']);
    }

    public static function getWithSync(int $externalID):Product|null
    {
        return Product::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $neloAPI = new NeloApiClient();
            $apiProduct = $neloAPI->getProduct($externalID);
            if(!empty($apiProduct)){
                return self::createFromJSON($apiProduct);
            }
            else{
                Log::error('Get product API Error', ['P_ID' => $externalID]);
                return null;
            }
        });
    }

    public static function createFromJSON(object $product):Product|null{

        ///dd($product);

        Log::info("Creating product with external id {$product->id}");

        $type = null;
        foreach ($product->genealogy as $ancestor){

            if(ProductTypeEnum::isIgnorable($ancestor))
                return null;

            $type = ProductTypeEnum::fromAPI($ancestor);
            if($type != null)
                break;
        }
        if($type == null){
            Log::error('Product Type not mapped: '.$product->type, (array) $product);
            return null;
        }

        $p = Product::create([
            'external_id' => $product->id,
            'name' => $product->name,
            'product_type_id' => $type,
            'discipline_id' => $product->discipline==null?null:DisciplineEnum::fromAPI($product->discipline),
        ]);

        if(!empty($product->image)){
            try{
                $p->addMediaFromUrl($product->image)->toMediaCollection('products');
            }
            catch(FileCannotBeAdded $fcbae){
                Log::error("File could not be added {$product->image}", [$fcbae->getMessage()]);
            }
        }

        if($type == ProductTypeEnum::Boat){
            $p->getOptionsFromAPI();
        }

        if($type != ProductTypeEnum::Boat && $type != ProductTypeEnum::Color)
            $p->updateFromMagento();

        return $p;
    }

    public function updateFromAPI():void
    {
        $neloAPI = new NeloApiClient();
        $extProduct = $neloAPI->getProduct($this->external_id);
        if(!empty($extProduct)) {

            Log::info("Updating product from API with external id {$extProduct->id}");

            $this->name = $extProduct->name;
            $this->description = empty($extProduct->description)?null:$extProduct->description;
            $this->retail_price = $extProduct->retail_price;
            if(!empty($extProduct->color)) {
                $this->fill([
                    'attributes->hex' => $extProduct->color
                ]);
            }

            if(!$this->hasMedia('products') && !empty($extProduct->image)){
                try{
                    $this->addMediaFromUrl($extProduct->image)->toMediaCollection('products');
                }
                catch(FileCannotBeAdded $fcbae){
                    Log::error("File could not be added {$extProduct->image}", [$fcbae->getMessage()]);
                }
            }
            $this->save();
        }
    }

    public static function updateAll():void{
        foreach (Product::all() as $produto){
            $produto->updateFromAPI();
        }
    }

    public function getOptionsFromAPI():void{

        $neloAPI = new NeloApiClient();
        $options = $neloAPI->getProductOptions($this->external_id);

        if(empty($options) || count($options) == 0)
            return;

        $this->options()->detach();
        foreach ($options as $option){
            $subProduct = Product::where('external_id', $option['product']['id'])->get()->first();
            //dd($subProduct);
            if($subProduct != null){
                $attribute = is_numeric($option['attribute']['id'])?Attribute::where('external_id', $option['attribute']['id'])->value('id'):null;
                $this->options()->attach($subProduct->id, [
                    'attribute_id' => $attribute,
                    'standard' => $option['standard'],
                ]);
            }

        }

    }

    public function updateFromMagento():void{
        $magento = new MagentoApiClient();

        $product = $magento->getProduct($this->external_id);

        if(empty($product)){
            return;
        }

        foreach ($product->custom_attributes as $attribute){
            if($attribute->attribute_code == 'url_key'){
                $this->fill(['attributes->magento_url' => $attribute->value]);
                $this->save();
                break;
            }
        }

        if(!$this->hasMedia('magento')){
            foreach ($product->media_gallery_entries as $media) {
                if($media->media_type == 'image' && !$media->disabled){
                    try{
                        $localMedia = $this->addMediaFromUrl($media->file)->toMediaCollection('magento');
                        $localMedia->order_column = $media->position;
                        $localMedia->save();
                    }
                    catch(FileCannotBeAdded $fcbae){
                        Log::error("File could not be added {$media->file}", [$fcbae->getMessage()]);
                    }
                }
            }

        }

        //dump($product);
    }
}
