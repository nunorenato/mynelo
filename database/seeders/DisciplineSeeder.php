<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DisciplineSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Sprint', 'Marathon', 'Downriver', 'Slalom', 'Ocean', 'Expedition', 'Fitness', 'Touring', 'Fishing'] as $discipline){
            Discipline::create([
                'name' => $discipline,
            ]);
        }
    }
}
