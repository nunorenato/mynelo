<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/** @mixin \App\Models\Coach\Session */
class CoachSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'athleteid' => $this->athleteid,
            'boatid' => $this->boatid,
            'date' => $this->createdon,
            'milis' => $this->gpslng
        ];
    }
}
