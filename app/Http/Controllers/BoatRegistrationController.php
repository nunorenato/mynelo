<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Models\BoatRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class BoatRegistrationController extends Controller
{
    public function index()
    {
        return BoatRegistration::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'boat_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'seat_id' => ['nullable', 'integer'],
            'seat_position' => ['nullable', 'integer'],
            'seat_height' => ['nullable', 'integer'],
            'footrest_id' => ['nullable', 'integer'],
            'rudder_id' => ['nullable', 'integer'],
            'paddle' => ['nullable'],
            'paddle_length' => ['nullable'],
        ]);

        return BoatRegistration::create($data);
    }

    public function show(BoatRegistration $boatRegistration)
    {
        return $boatRegistration;
    }

    public function update(Request $request, BoatRegistration $boatRegistration)
    {
        $data = $request->validate([
            'boat_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'seat_id' => ['nullable', 'integer'],
            'seat_position' => ['nullable', 'integer'],
            'seat_height' => ['nullable', 'integer'],
            'footrest_id' => ['nullable', 'integer'],
            'rudder_id' => ['nullable', 'integer'],
            'paddle' => ['nullable'],
            'paddle_length' => ['nullable'],
        ]);

        $boatRegistration->update($data);

        return $boatRegistration;
    }

    public function destroy(BoatRegistration $boatRegistration)
    {
        $boatRegistration->delete();

        return response()->json();
    }

    public function validateRegistration(BoatRegistration $boatregistration, $hash):View
    {
        return $this->processValidation($boatregistration, $hash,StatusEnum::VALIDATED );
    }
    public function cancelRegistration(BoatRegistration $boatregistration, $hash):View
    {
        return $this->processValidation($boatregistration, $hash,StatusEnum::CANCELED );
    }

    private function processValidation(BoatRegistration $boatRegistration, string $hash, StatusEnum $newStatus):View
    {
        if(hash('murmur3a', $boatRegistration->boat->external_id) != $hash){
            return view('livewire.boats.register-approval', ['error' => 'Invalid link']);
        }
        if($boatRegistration->status != StatusEnum::PENDING){
            return view('livewire.boats.register-approval', ['error' => 'Registration was not in the correct status. Already validated ou canceled?']);
        }

        $boatRegistration->status = $newStatus;
        $boatRegistration->save();

        return view('livewire.boats.register-approval', ['validated' => $newStatus == StatusEnum::VALIDATED]);
    }

}
