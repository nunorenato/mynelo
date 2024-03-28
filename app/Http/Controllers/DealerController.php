<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DealerController extends Controller
{
    public function index()
    {
        return Dealer::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'external_id' => ['required', 'integer'],
        ]);

        return Dealer::create($data);
    }

    public function show(Dealer $dealer)
    {
        return $dealer;
    }

    public function update(Request $request, Dealer $dealer)
    {
        $data = $request->validate([
            'name' => ['required'],
            'external_id' => ['required', 'integer'],
        ]);

        $dealer->update($data);

        return $dealer;
    }

    public function destroy(Dealer $dealer)
    {
        $dealer->delete();

        return response()->json();
    }

    public function sync(){
        $response = Http::get(config('nelo.nelo_api_url').'/dealer');

        if($response->ok()){
            $dealers = $response->object();

            foreach ($dealers as $dealer){
                Dealer::updateOrCreate(
                    ['external_id' => $dealer->E_ID],
                    ['name' => $dealer->E_NOME]
                );
            }

        }
        else{
            Log::warning('Error syncing with Nelo API Dealers');
        }

    }
}
