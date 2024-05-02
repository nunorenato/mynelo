<?php

namespace App\Http\Controllers;

use App\Enums\FieldEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\StatusEnum;
use App\Jobs\BoatSyncJob;
use App\Mail\PreRegistrationMail;
use App\Mail\RegistrationResultMail;
use App\Models\Attribute;
use App\Models\Boat;
use App\Models\BoatRegistration;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpParser\JsonDecoder;
use function Laravel\Prompts\error;

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


        //$response = Http::get(config('nelo.nelo_api_url')."/product/v2/34254");
       // BoatSyncJob::dispatch(Boat::find(17), $response->object());
        //Product::createFromJSON($response->object());


/*        $pc = new ProductController();
        $pc->updateFromAPI(Product::find(45));*/

     //   Log::error('teste');

        $registration = BoatRegistration::find(2);
        $other = $registration->boat->registrations()->where('id', '<>', $registration->id)->whereNotIn('status', [StatusEnum::CANCELED])->get();

        dump($other);

        dump('ok');

    }
}
