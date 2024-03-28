<?php

namespace App\Http\Controllers;

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

    public function getWithSync(int $externalID):Product|null
    {
        return Product::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $response = Http::get(config('nelo.nelo_api_url')."/product/$externalID");
            if($response->ok()){
                $product = $response->object();

                Log::info("Creating product with external id {$product->P_ID}");

                return Product::create([
                    'external_id' => $product->P_ID,
                    'name' => $product->name,
                    'image' => $product->P_IMAGEM,
                ]);
            }
            else
                return null;
        });
    }
}
