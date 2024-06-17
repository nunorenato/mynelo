<?php

namespace App\Http\Resources;

use App\Enums\GenderEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class NeloOwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'dob' => $this->date_of_birth,
            'height' => $this->height,
            'weight' => $this->weight,
            'gender' => match ($this->gender) {
                GenderEnum::Male => 'Masculino',
                GenderEnum::Female => 'Feminino',
                default => null
            } ,
            'racing' => $this->competition,
            'club' => $this->club,
            'trainings' => $this->weekly_trainings,
            'time_500' => $this->time_500,
            'time_1000' => $this->time_1000,
            'country' => $this->country?->name,
            'preference' => $this->discipline?->name,
        ];
    }
}
