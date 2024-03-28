<?php

namespace App\Http\Controllers;

use App\Models\BoatRegistration;
use Illuminate\Http\Request;

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
}
