<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Enums\StatusEnum;
use App\Jobs\BoatSyncJob;
use App\Services\NeloApiClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Boat extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'model',
        'finished_at',
        'finished_weight',
        'product_id',
        'ideal_weight',
        'external_id',
        'seller',
        'co2',
        'voucher_used',
        'synced',
    ];

    protected $casts = [
        'finished_at' => 'date',
        'vouched_used' => 'boolean',
        'synced' => 'boolean',
    ];

    public function product():BelongsTo{
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function painter():BelongsTo{
        return $this->belongsTo(Worker::class, 'painter_id');
    }

    public function layuper():BelongsTo{
        return $this->belongsTo(Worker::class, 'layuper_id');
    }

    public function evaluator():BelongsTo{
        return $this->belongsTo(Worker::class, 'evaluator_id');
    }

    public function assembler():BelongsTo{
        return $this->belongsTo(Worker::class, 'montador_id');
    }

    public function products():BelongsToMany{
        return $this->belongsToMany(Product::class)->withPivot('attribute_id');
    }

    public function discipline():BelongsTo{
        return $this->belongsTo(Discipline::class);
    }

    public function registrations():HasMany{
        return $this->hasMany(BoatRegistration::class);
    }

    public static function getWithSync(int $externalID):Boat|null
    {
        return Boat::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $response = Http::get(config('nelo.nelo_api_url')."/orders/extended/$externalID");
            if($response->ok()){

                $boat = $response->object();

                Log::info("Creating boat with external id {$boat->id}");

                $newBoat = Boat::create([
                    'external_id' => $boat->id,
                    'model' => $boat->model,
                    'ideal_weight' => $boat->ideal_weight,
                    'finished_weight' => $boat->final_weight??null,
                    'finished_at' => $boat->finish_date,
                    'synced' => false,
                ]);

                Log::info('Queue boat for full sync');
                BoatSyncJob::dispatch($newBoat, $boat);

                return $newBoat;
            }else{
                return null;
            }
        });
    }

    public function deletePreviousOwners(int $newRegistrationId){
        $this->registrations()
            ->where('id', '<>', $newRegistrationId)
            ->delete();
    }

    /**
     * Market price from the current price list, depreciated by year
     *
     * @param $price
     * @return int|null
     */
    public function marketValue():int|null
    {
        if(empty($this->finished_at) || empty($this->product->retail_price))
            return null;

        $age = now()->diffInYears($this->finished_at)+1;
        //$price = 2500;

        /**
         * newPrice = price - price * 1yearDepreciation - price * 2yearDepreciation - ....
         * or
         * newPrice = price * (1 - 1year - 2year ---)
         */
        $depreciation = 1;
        for($i = 1; $i <= $age; $i++){
            $depreciation -= match($i){
                1 => 0.25,
                2 => 0.15,
                3 => 0.1,
                4 => 0.05,
                default => 0.01,
            };
        }
        return intval($this->product->retail_price * $depreciation);
    }

    public function syncComponents():void{
        // apagamos todos os fittings
        $this->products()->detach($this->products()
            ->where('product_type_id', '<>', ProductTypeEnum::Color->value)
            ->whereRelation('type', 'fitting', false)
            ->get()
            ->pluck('id')
        );

        $neloApi = new NeloApiClient();
        $components = $neloApi->getBoatComponents($this->external_id);
        foreach ($components as $component){

            $this->addProductFromAPI($component['product']['id'], empty($component['attribute'])?null:$component['attribute']['id']);

        }
    }
    public function syncFittings():void{
        // apagamos todos os fittings
        $this->products()->detach($this->products()
            ->where('product_type_id', '<>', ProductTypeEnum::Color->value)
            ->whereRelation('type', 'fitting', true)
            ->get()
            ->pluck('id')
        );

        $neloApi = new NeloApiClient();
        $components = $neloApi->getBoatFittings($this->external_id);
        foreach ($components as $component){

            $this->addProductFromAPI($component['product']['id'], empty($component['attribute'])?null:$component['attribute']['id']);

        }
    }

    public function syncColors():void{

        $neloApi = new NeloApiClient();
        $colors = $neloApi->getBoatColors($this->external_id);

        Log::info('Adding colors to boat');

        // apagamos sempre as cores
        $this->products()->detach($this->products()->where('product_type_id', '=', ProductTypeEnum::Color->value)->get()->pluck('id'));

        foreach ($colors as $jsonColor){
            $pColor = Product::firstOrCreate([
                'external_id' => $jsonColor['color']['id'],
            ],[
                'name' => $jsonColor['color']['name'],
                'external_id' => $jsonColor['color']['id'],
                'attributes' => ['hex' => $jsonColor['color']['hex']],
                'product_type_id' => ProductTypeEnum::Color
            ]);
            Log::debug('Color', $pColor->toArray());
            $this->products()->attach($pColor->id, ['attribute_id' => Attribute::firstWhere('external_id', $jsonColor['id'])->id]);
        }
    }

    public function addProductFromAPI($externalProductId, $externalAttributeId = null):void{

        $product = Product::getWithSync($externalProductId);
        if($product == null)
            return;

        if($externalAttributeId != null){
            $attribute = Attribute::firstWhere('external_id',$externalAttributeId);
            $attr = $attribute->id;
        }
        else
            $attr = null;


        $this->products()->attach($product->id, ['attribute_id' => $attr]);
    }
}
