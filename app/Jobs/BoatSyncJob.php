<?php

namespace App\Jobs;

use App\Enums\ProductTypeEnum;
use App\Http\Controllers\ImageController;
use App\Models\Attribute;
use App\Models\Boat;
use App\Models\Image;
use App\Models\Product;
use App\Models\Worker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BoatSyncJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Boat $boat, private readonly object $extendedJson)
    {
    }

    public function handle(): void
    {
        Log::info("Processing boat sync {$this->boat->external_id}");

        /**
         * Modelo do barco
         */
        $product = Product::getWithSync($this->extendedJson->model_id);
        if($product != null){
            Log::debug('Got product', $product->toArray());
            $this->boat->product()->associate($product);
        }
        else{
            Log::error("Error getting product for boat OF {$this->boat->external_id}");
        }

        /**
         * Operadores do barco
         */
        if(!empty($this->extendedJson->pintor)){
            Log::info("Painter {$this->extendedJson->pintor->name}");
            $this->boat->painter()->associate(Worker::getWithSync($this->extendedJson->pintor->id))->save();
        }
        if(!empty($this->extendedJson->laminador)){
            Log::info("Layup {$this->extendedJson->laminador->name}");
            $this->boat->layuper()->associate(Worker::getWithSync($this->extendedJson->laminador->id))->save();
        }
        if(!empty($this->extendedJson->avaliador)){
            Log::info("Finish {$this->extendedJson->avaliador->name}");
            $this->boat->evaluator()->associate(Worker::getWithSync($this->extendedJson->avaliador->id))->save();
        }

        /**
         * Imagens
         */
        $response = Http::get(config('nelo.nelo_api_url')."/orders/images/{$this->boat->external_id}");
        if($response->ok()){
            Log::info('Adding images to boat');
            $images = $response->json();
            foreach ($images as $image){
                $this->boat->images()->attach(ImageController::fromURL($image['url'], $image['name'], 'boats'));
            }
        }

        /**
         * Colors
         */
        $response = Http::get(config('nelo.nelo_api_url')."/orders/colors/{$this->boat->external_id}");
        if($response->ok()) {
            Log::info('Adding colors to boat');
            foreach ($response->json() as $jsonColor){
                $pColor = Product::firstOrCreate([
                    'external_id' => $jsonColor['color']['id'],
                ],[
                    'name' => $jsonColor['color']['name'],
                    'external_id' => $jsonColor['color']['id'],
                    'attributes' => ['hex' => $jsonColor['color']['id']],
                    'product_type_id' => ProductTypeEnum::Color
                ]);
                $this->boat->products()->attach($pColor->id, ['attribute_id' => Attribute::firstWhere('external_id', $jsonColor['id'])->id]);
            }
        }

        /**
         * Fittings
         */
        $response = Http::get(config('nelo.nelo_api_url')."/orders/fittings/{$this->boat->external_id}");
        if($response->ok()) {
            Log::info('Adding fittings to boat');
            foreach ($response->json() as $jsonProduct){

                $pFitting = Product::getWithSync($jsonProduct['product']['id']);
                if($pFitting == null)
                    continue;

                if($jsonProduct['attribute']==null)
                    $attr = null;
                else{
                    $attribute = Attribute::firstWhere('external_id',$jsonProduct['attribute']['id']);
                    $attr = $attribute->id;
                }


                $this->boat->products()->attach($pFitting->id, ['attribute_id' => $attr]);
            }
        }

        /**
         * CO 2
         */
        $response = Http::get(config('nelo.nelo_api_url')."/orders/co2/{$this->boat->external_id}");
        if($response->ok()) {
            $co2 = $response->json();
            if(is_numeric($co2)){
                $this->boat->co2 = $co2;
            }
        }

        $this->boat->save();
    }
}
