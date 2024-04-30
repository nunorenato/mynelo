<?php

namespace App\Models;

use App\Enums\DisciplineEnum;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    protected $fillable = [
        'name',
        'image',
        'external_id',
        'product_type_id',
        'discipline_id',
        'attributes',
        'attributes->hex',
        'description',
    ];

    protected $casts = [
        'attributes' => 'json',
        'external_id' => 'int'
    ];


    public function type():BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function discipline():BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function image():BelongsTo{
        return $this->belongsTo(Image::class);
    }

    public function options():BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_options','main_product_id', 'sub_product_id')
            ->withPivot(['attribute_id', 'standard']);
    }

    public static function getWithSync(int $externalID):Product|null
    {
        return Product::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $response = Http::get(config('nelo.nelo_api_url')."/product/v2/$externalID");
            if($response->ok()){
                return self::createFromJSON($response->object());
            }
            else{
                Log::error('Get product API Error', ['P_ID' => $externalID]);
                return null;
            }
        });
    }

    public static function createFromJSON($product){

        Log::info("Creating product with external id {$product->id}");

        $type = null;
        foreach ($product->genealogy as $ancestor){
            $type = ProductTypeEnum::fromAPI($ancestor);
            if($type != null)
                break;
        }
        if($type == null){
            Log::error('Product Type not mapped: '.$product->type, $product);
            return null;
        }

        $p = Product::create([
            'external_id' => $product->id,
            'name' => $product->name,
            'product_type_id' => $type,
            'discipline_id' => $product->discipline==null?null:DisciplineEnum::fromAPI($product->discipline),
        ]);

        if(!empty($product->image)){
            $p->image()->associate(ImageController::fromURL($product->image, $product->name, 'products'))->save();
        }

        if($type == ProductTypeEnum::Boat){
            $p->getOptionsFromAPI();
        }

        return $p;
    }

    public function updateFromAPI():void
    {
        $response = Http::get(config('nelo.nelo_api_url')."/product/v2/{$this->external_id}");
        if($response->ok()) {
            $extProduct = $response->object();

            Log::info("Updating product from API with external id {$extProduct->id}");

            $this->name = $extProduct->name;
            $this->description = empty($extProduct->description)?null:$extProduct->description;
            if(!empty($extProduct->color))
                $this->attributes = ['hex' => $extProduct->color];

            if(empty($this->image) && !empty($extProduct->image)){
                $this->image()->associate(ImageController::fromURL($extProduct->image, $this->name, 'products'))->save();
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

        $response = Http::get(config('nelo.nelo_api_url')."/product/options/{$this->external_id}");
        if($response->ok()) {
            $options = $response->json();
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

    }
}
