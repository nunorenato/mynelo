<?php

namespace Database\Seeders;

use App\Models\Coach\Athlete;
use App\Models\Coach\Session;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CoachSeeder extends Seeder
{
    public function run(): void
    {
        /*User::whereNull('athlete_id')->get()->each(function ($user) {
           if($athlete = Athlete::where('email', 'LIKE', $user->email)->first()){
               dump('Found: ' . $user->email);
               $user->athlete_id = $athlete->id;
               $user->save();
           }
        });*/

        Session::all()->each(function (Session $session) {
            if($boat = DB::connection('coach')->table("sierraw_boat")->find($session->boatid)){
               dump("Found boat for session {$session->id}");
               $session->details = Str::limit($session->details, 200, ' (...)') . " [Imported from Nelo Coach. Boat {$boat->serialnumber} {$boat->name}]";
            }
            $session->boatid = null;
            $session->save();
        });
    }
}
