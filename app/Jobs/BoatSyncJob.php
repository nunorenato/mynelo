<?php

namespace App\Jobs;

use App\Enums\ProductTypeEnum;
use App\Models\Attribute;
use App\Models\Boat;
use App\Models\Product;
use App\Models\Worker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class BoatSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Boat $boat, private object|int $extendedJson)
    {
    }

    public function handle(): void
    {

        if(is_int($this->extendedJson)){
            $response = Http::get(config('nelo.nelo_api_url')."/orders/extended/{$this->extendedJson}");
            if($response->ok()) {
                $this->extendedJson = $response->object();
            }
            else{
                return;
            }
        }

        Log::info("Processing boat sync {$this->boat->external_id}");

        /**
         * Detalhes do barco
         */
        $this->boat->fill([
            'finished_at' => $this->extendedJson->finish_date??null,
            'finished_weight' => $this->extendedJson->final_weight??null,
            'voucher_used' => $this->extendedJson->voucher_used,
            'remarks' => $this->extendedJson->remarks,
            'reference' => $this->extendedJson->reference,
        ]);

        /**
         * Modelo do barco
         */
        if(empty($this->boat->product_id)){
            $product = Product::getWithSync($this->extendedJson->model_id);
            if($product != null){
                Log::debug('Got product', $product->toArray());
                $this->boat->product()->associate($product);
            }
            else{
                Log::error("Error getting product for boat OF {$this->boat->external_id}");
            }
        }

        /**
         * Operadores do barco
         */
        if(empty($this->boat->painter_id) && !empty($this->extendedJson->pintor)){
            Log::info("Painter {$this->extendedJson->pintor->name}");
            $this->boat->painter()->associate(Worker::getWithSync($this->extendedJson->pintor->id))->save();
        }
        if(empty($this->boat->layuper_id) && !empty($this->extendedJson->laminador)){
            Log::info("Layup {$this->extendedJson->laminador->name}");
            $this->boat->layuper()->associate(Worker::getWithSync($this->extendedJson->laminador->id))->save();
        }
        if(empty($this->boat->evaluator_id) && !empty($this->extendedJson->avaliador)){
            Log::info("Finish {$this->extendedJson->avaliador->name}");
            $this->boat->evaluator()->associate(Worker::getWithSync($this->extendedJson->avaliador->id))->save();
        }
        if(empty($this->boat->montador_id) && !empty($this->extendedJson->montador)){
            Log::info("Assembly {$this->extendedJson->montador->name}");
            $this->boat->assembler()->associate(Worker::getWithSync($this->extendedJson->montador->id))->save();
        }

        /**
         * Imagens
         */
        $response = Http::get(config('nelo.nelo_api_url')."/orders/images/{$this->boat->external_id}");
        if($response->ok()){
            Log::info('Adding images to boat');

            $currentImages = $this->boat->getMedia('*');

            $images = $response->json();
            foreach ($images as $image){
                if($currentImages->doesntContain('file_name', basename($image['url']))){
                    try{
                        $this->boat->addMediaFromUrl($image['url'])->toMediaCollection($image['type']==14?'pool':'boats');
                    }
                    catch(FileCannotBeAdded $fcbae){
                        Log::error("File could not be added {$image['url']}", [$fcbae->getMessage()]);
                    }
                }
            }
        }

        $this->boat->syncColors();

        $this->boat->syncFittings();

        $this->boat->syncComponents();

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

        $this->boat->synced = true;
        $this->boat->save();
    }

    /*public function middleware():array{
        return [new WithoutOverlapping()];
    }*/
}
