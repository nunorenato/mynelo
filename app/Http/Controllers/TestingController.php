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

        dump(config('nelo.emails.admins'));

        dump('ok');

    }
}
