<?php

namespace Database\Seeders;

use App\Enums\DisciplineEnum;
use App\Enums\FieldEnum;
use App\Models\Discipline;
use App\Models\Field;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        //$sprint = Discipline::find(DisciplineEnum::Sprint);
        foreach (FieldEnum::cases() as $fieldEnum){
            $field  = Field::create([
                'id' => $fieldEnum->value,
                'name' => implode(' ', Str::ucsplit($fieldEnum->name)),
                'column' => Str::snake($fieldEnum->name, '_'),
            ]);
            foreach (Discipline::all() as $discipline){
                $discipline->fields()->attach($field, ['required' => false]);
            }
        }

        Field::find(FieldEnum::Seat)->fill(['column' => 'seat_id']);
        Field::find(FieldEnum::Footrest)->fill(['column' => 'footrest_id']);
        Field::find(FieldEnum::Rudder)->fill(['column' => 'rudder_id']);

        $disc = Discipline::find(DisciplineEnum::Ocean);
        $disc->fields()->detach([FieldEnum::Seat, FieldEnum::SeatPosition, FieldEnum::SeatHeight]);

        $disc = Discipline::find(DisciplineEnum::Touring);
        $disc->fields()->detach([FieldEnum::Seat, FieldEnum::SeatPosition, FieldEnum::SeatHeight]);

        $disc = Discipline::find(DisciplineEnum::Expedition);
        $disc->fields()->detach([FieldEnum::Seat, FieldEnum::SeatPosition, FieldEnum::SeatHeight]);

        $disc = Discipline::find(DisciplineEnum::Slalom);
        $disc->fields()->detach([FieldEnum::SeatHeight, FieldEnum::Rudder, FieldEnum::Paddle, FieldEnum::PaddleLength]);


    }

}
