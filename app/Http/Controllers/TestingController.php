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
use App\Models\Worker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PhpParser\JsonDecoder;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use function Laravel\Prompts\error;

class TestingController extends Controller
{
    public function index()
    {

        $boats = [136392,
            135334,
            135381,
            135386,
            135256,
            136430,
            134956,
            135498,
            135169,
            135209,
            135336,
            135317,
            135732,
            135389,
            136517,
            135257,
            135392,
            135380,
            135439,
            135390,
            135211,
            135043,
            136225,
            135339,
            135335,
            128276,
        ];

        foreach ($boats as $external_id) {
            $boat = Boat::where('external_id', $external_id)->first();
            BoatSyncJob::dispatch($boat, $external_id);
        }

        dump('ok');

    }
}
