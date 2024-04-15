<?php

namespace Database\Seeders;

use App\Enums\DisciplineEnum;
use App\Models\Discipline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineSeeder extends Seeder
{
    public function run(): void
    {

        Discipline::create(['id' => DisciplineEnum::Sprint, 'name' => 'Sprint']);
        Discipline::create(['id' => DisciplineEnum::Marathon, 'name' => 'Marathon']);
        Discipline::create(['id' => DisciplineEnum::Downriver, 'name' => 'Downriver']);
        Discipline::create(['id' => DisciplineEnum::Slalom, 'name' => 'Slalom']);
        Discipline::create(['id' => DisciplineEnum::Ocean, 'name' => 'Ocean']);
        Discipline::create(['id' => DisciplineEnum::Expedition, 'name' => 'Expedition']);
        Discipline::create(['id' => DisciplineEnum::Fitness, 'name' => 'Fitness']);
        Discipline::create(['id' => DisciplineEnum::Touring, 'name' => 'Touring']);
        Discipline::create(['id' => DisciplineEnum::Fishing, 'name' => 'Fishing']);

    }
}
