<?php

namespace App\Http\Controllers;

use App\Enums\DisciplineEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'image' => ['nullable'],
            'external_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
        ]);

        return Product::create($data);
    }

    public function show(Product $product)
    {
        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['required'],
            'image' => ['nullable'],
            'external_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
        ]);

        $product->update($data);

        return $product;
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json();
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
            Log::error('Product Type not mapped: '.$product->type);
            return null;
        }

        $p = Product::create([
            'external_id' => $product->id,
            'name' => $product->name,
            'product_type_id' => $type,
            'discipline_id' => $product->discipline==null?null:DisciplineEnum::fromAPI($product->discipline),
        ]);

        if(!empty($product->image)){
            $ic = new ImageController();
            $p->image()->associate(ImageController::fromURL($product->image, $product->name, 'products'))->save();
        }

        if($type == ProductTypeEnum::Boat){
            self::getOptionsFromAPI($p);
        }

        return $p;
    }

    public function updateFromAPI(Product $product)
    {
        $response = Http::get(config('nelo.nelo_api_url')."/product/v2/{$product->external_id}");
        if($response->ok()) {
            $extProduct = $response->object();

            Log::info("Updating product from API with external id {$extProduct->id}");

            $product->name = $extProduct->name;
            $product->description = empty($extProduct->description)?null:$extProduct->description;
            if(!empty($extProduct->color))
                $product->attributes = ['hex' => $extProduct->color];

            if(empty($product->image) && !empty($extProduct->image)){
                $ic = new ImageController();
                $product->image()->associate(ImageController::fromURL($extProduct->image, $product->name, 'products'))->save();
            }
            $product->save();
        }
    }

    public static function updateAll(){
        $pc = new ProductController();

        foreach (Product::all() as $produto){
            $pc->updateFromAPI($produto);
        }
    }

    public static function getOptionsFromAPI(Product $product):void{

        $response = Http::get(config('nelo.nelo_api_url')."/product/options/{$product->external_id}");
        if($response->ok()) {
            $options = $response->json();
            $product->options()->detach();
            foreach ($options as $option){
                $subProduct = Product::where('external_id', $option['product']['id'])->get()->first();
                //dd($subProduct);
                if($subProduct != null){
                    $attribute = is_numeric($option['attribute']['id'])?Attribute::where('external_id', $option['attribute']['id'])->value('id'):null;
                    $product->options()->attach($subProduct->id, [
                        'attribute_id' => $attribute,
                        'standard' => $option['standard'],
                    ]);
                }

            }
        }

    }
}
