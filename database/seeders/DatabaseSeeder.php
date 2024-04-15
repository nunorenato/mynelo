<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Attribute;
use App\Models\PersonType;
use Illuminate\Database\Seeder;
use function Laravel\Prompts\table;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);



        PersonType::create([
            'id' => 1,
            'name' => 'worker',
        ]);
        $this->call([
           // CountriesTableSeeder::class,
            //ProductTypeSeeder::class,
            //DisciplineSeeder::class,
            //GoalSeeder::class,
            AttributeSeeder::class,
        ]);


    }
}
