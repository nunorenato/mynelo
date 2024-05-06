<?php

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Enums\StatusEnum;
use App\Mail\OldOwnerMail;
use App\Models\Boat;
use App\Models\BoatRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        $csv = array_map('str_getcsv', file(Storage::path('import-tmp/owners.csv')));
        array_shift($csv);
       // dump($csv);

        foreach ($csv as $owner){
            if(User::where('email', 'like', $owner[2])->get()->isEmpty()){
                $password = Str::password(length: 12, symbols: false);
                //dd($password);
                $user = User::create([
                    'name' => Str::of($owner[1])->trim()->lower(),
                    'email' => Str::of($owner[2])->trim()->lower(),
                    'password' => $password,
                    'date_of_birth' => Str::substr($owner[3], 0, 10),
                    'height' => $owner[4]==0?null:$owner[4],
                    'weight' => $owner[5]==0?null:$owner[5],
                    'gender' => match ($owner[6][0]){'M' => GenderEnum::Male, 'F' => GenderEnum::Female, default => GenderEnum::Other},
                    'club' => $owner[7],
                    'competition' => $owner[8]==1,
                ]);
                Mail::to(config('nelo.emails.admins'))->send(new OldOwnerMail($user, $password));
            }
            else{
                dump($owner[1] . ' already exists');
            }
        }

        $csv = array_map('str_getcsv', file(Storage::path('import-tmp/registrations.csv')));
        array_shift($csv);
        // dump($csv);

        foreach ($csv as $registration){
            $user = User::where('email', 'like', $registration[1])->first();
            if(!empty($user)){
                $boat = Boat::getWithSync($registration[0]);
                $boatRegistration = BoatRegistration::create([
                    'boat_id' => $boat->id,
                    'user_id' => $user->id,
                    'seller' => 'N/A (imported)',
                    'status' => StatusEnum::VALIDATED,
                ]);
            }
        }
    }
}
