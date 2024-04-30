<?php

namespace App\Models;

use App\Enums\PersonTypeEnum;
use App\Http\Controllers\ImageController;
use App\Models\Person;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Worker extends Person implements HasMedia
{
    use InteractsWithMedia;

    protected $attributes = [
        'person_type_id' => PersonTypeEnum::Worker,
    ];
    public function newQuery(){
        return parent::newQuery()->where('person_type_id', '=', PersonTypeEnum::Worker);
    }

    public static function getWithSync(int $externalID):Worker|null
    {
        return Worker::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $response = Http::get(config('nelo.nelo_api_url')."/worker/$externalID");
            if($response->ok()){
                $worker = $response->object();

                Log::debug("Got funcionario ", ['json' => $worker]);
                Log::info("Creating worker with external id {$worker->id}");

                $w = new Worker();
                $w->external_id = $worker->id;
                $w->name = $worker->name;
                $w->save();

                if(!empty($worker->photo)){
                    try{
                        $w->addMediaFromUrl($worker->photo)->toMediaCollection('people');
                    }
                    catch(FileCannotBeAdded $fcbae){
                        Log::error("File could not be added {$worker->photo}", [$fcbae->getMessage()]);
                    }
                }

                return $w;
            }
            else{
                Log::error('Get product API Error', ['E_ID' => $externalID]);
                return null;
            }
        });
    }

}
