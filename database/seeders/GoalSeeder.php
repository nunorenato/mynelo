<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Loose weight','Increase speed', 'Improve technique', 
                    'Improve aerobic capacity',	'Finish a long distance event',	
                    'Improve mobility','Improve strength levels'] as $goal){
            Goal::create([
                'name' => $goal,
            ]);
        }
    }
}
