<?php

namespace App\Http\Controllers;

use App\Enums\ProductTypeEnum;
use App\Jobs\BoatSyncJob;
use App\Mail\PreRegistrationMail;
use App\Models\Attribute;
use App\Models\Boat;
use App\Models\BoatRegistration;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PhpParser\JsonDecoder;

class TestingController extends Controller
{
    public function index()
    {
        /*$wc = new WorkerController();
        $worker = $wc->getWithSync(20994);
        dump($worker);*/

       // $boat = Boat::find(8);

        //$boat->products()->attach(10, ['attribute_id' => \App\Models\Attribute::firstWhere('external_id',1)->id]);

//       dump(ProductController::getWithSync(42411));


  /*      $response = Http::get(config('nelo.nelo_api_url')."/orders/extended/136278");
        BoatSyncJob::dispatch(Boat::find(10), $response->object());*/

        Mail::to(config('nelo.emails.admins'))
            ->send(new PreRegistrationMail(BoatRegistration::find(2)));

        dump('ok');

    }
}
