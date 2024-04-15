<?php

namespace App\Http\Controllers;

use App\Jobs\BoatSyncJob;
use App\Models\Boat;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BoatController extends Controller
{
    public function index()
    {
        return Boat::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'model' => ['required'],
            'finished_at' => ['nullable', 'date'],
            'finished_weight' => ['nullable', 'numeric'],
            'model_id' => ['required', 'integer'],
            'ideal_weight' => ['required', 'numeric'],
        ]);

        return Boat::create($data);
    }

    public function show(Boat $boat)
    {
        return $boat;
    }

    public function update(Request $request, Boat $boat)
    {
        $data = $request->validate([
            'model' => ['required'],
            'finished_at' => ['nullable', 'date'],
            'finished_weight' => ['nullable', 'numeric'],
            'model_id' => ['required', 'integer'],
            'ideal_weight' => ['required', 'numeric'],
        ]);

        $boat->update($data);

        return $boat;
    }

    public function destroy(Boat $boat)
    {
        $boat->delete();

        return response()->json();
    }

    public function getWithSync(int $externalID):Boat|null
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
                    'finished_weight' => $boat->final_weight,
                    'finished_at' => $boat->finish_date
                ]);

                Log::info('Queue boat for full sync');
                BoatSyncJob::dispatch($newBoat, $boat);

                return $newBoat;
            }else{
                return null;
            }
        });
    }
}
