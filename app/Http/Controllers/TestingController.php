<?php

namespace App\Http\Controllers;

use App\Enums\FieldEnum;
use App\Enums\ProductTypeEnum;
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


//        $response = Http::get(config('nelo.nelo_api_url')."/orders/extended/120089");
//        BoatSyncJob::dispatch(Boat::find(12), $response->object());


/*        $pc = new ProductController();
        $pc->updateFromAPI(Product::find(45));*/

        $boat = Boat::find(15);



        $response = Http::get(config('nelo.nelo_api_url')."/orders/fittings/{$boat->external_id}");
        if($response->ok()) {
            Log::info('Adding fittings to boat');
            foreach ($response->json() as $jsonProduct){

                $pFitting = ProductController::getWithSync($jsonProduct['product']['id']);
                if($pFitting == null){
                    continue;
                }

                if($jsonProduct['attribute']==null)
                    $attr = null;
                else{
                    $attribute = Attribute::firstWhere('external_id',$jsonProduct['attribute']['id']);
                    $attr = $attribute->id;
                }

                if($pFitting->product_type_id == ProductTypeEnum::Footrest)
                    $boat->products()->attach($pFitting->id, ['attribute_id' => $attr]);

            }
        }



        dump('ok');

    }
}
