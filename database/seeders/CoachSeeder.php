<?php

namespace Database\Seeders;

use App\Models\Coach\Athlete;
use App\Models\User;
use Illuminate\Database\Seeder;

class CoachSeeder extends Seeder
{
    public function run(): void
    {
        User::whereNull('athlete_id')->get()->each(function ($user) {
           if($athlete = Athlete::where('email', 'LIKE', $user->email)->first()){
               dump('Found: ' . $user->email);
               $user->athlete_id = $athlete->id;
               $user->save();
           }
        });
    }
}
