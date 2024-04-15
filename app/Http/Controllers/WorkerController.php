<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkerController extends Controller
{
    public function getWithSync(int $externalID):Worker|null
    {
        return Worker::where('external_id', '=', $externalID)->firstOr(function() use ($externalID){
            $response = Http::get(config('nelo.nelo_api_url')."/worker/$externalID");
            if($response->ok()){
                $worker = $response->object();

                Log::debug("Got funcionario ", ['json' => $worker]);
                Log::info("Creating worker with external id {$worker->id}");

                $ic = new ImageController();
                $photo = ImageController::fromURL($worker->photo, $worker->name, 'people');

                $w = new Worker();
                $w->external_id = $worker->id;
                $w->name = $worker->name;
                $w->photo()->associate($photo);
                $w->save();

                return $w;
            }
            else{
                Log::error('Get product API Error', ['E_ID' => $externalID]);
                return null;
            }
        });
    }
}
