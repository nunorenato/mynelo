<?php

namespace App\Http\Controllers;

use App\Filament\Resources\BoatResource;
use App\Http\Resources\CoachBoatResource;
use App\Models\BoatRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CoachController extends Controller
{
    public function authenticate(Request $request)
    {
        Log::info('Athlete authenticate for email: ' . $request->email);
        $debug = $request->toArray();
        if(!empty($debug['password'])){
            $debug['password'] = "(hidden)";
        }
        Log::debug('Login from api', $debug);
        Log::debug('request: ', $request->headers->all());

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);


        if (Auth::attempt($credentials)) {
            Log::info('Athlete authenticated');

            activity()
                ->on(Auth::user())
                ->event('login')
                ->log('Coach login');


            return [['id' => Auth::user()->id]];
        }

        return ['id' => -1];
    }

    public function getBoats(User $user){

        return CoachBoatResource::collection($user->boats);
    }

    public function getBoatsPost(Request $request){

        $validated = $request->validate([
            'id' => ['required', 'exists:users,id'],
        ]);

        return $this->getBoats(User::findOrFail($validated['id']));
    }
}
