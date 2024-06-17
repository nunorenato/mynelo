<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BoatRegistration */
class NeloBoatRegistrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'seat_position' => $this->seat_position,
            'seat_height' => $this->seat_height,
            'footrest_position' => $this->footrest_position,
            'paddle' => $this->paddle,
            'paddle_length' => $this->paddle_length,

            'boat_id' => $this->boat->external_id,
            'seat_id' => $this->seat?->external_id,
            'footrest_id' => $this->footrest?->external_id,
            'rudder_id' => $this->rudder?->external_id,

//            'user' => new NeloOwnerResource($this->whenLoaded('user')),
        ];
    }
}
