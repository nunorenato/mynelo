<?php

namespace App\Http\Controllers;

use App\Enums\FieldEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\StatusEnum;
use App\Jobs\BoatSyncJob;
use App\Jobs\MagentoCouponJob;
use App\Mail\PreRegistrationMail;
use App\Mail\RegistrationResultMail;
use App\Models\Attribute;
use App\Models\Boat;
use App\Models\BoatRegistration;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use App\Models\Worker;
use App\Services\MagentoApiClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\JsonDecoder;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use function Laravel\Prompts\error;

class TestingController extends Controller
{
    public function index()
    {

       // MagentoCouponJob::dispatch(User::find(1));
        $boat = Boat::firstWhere('external_id', 134600);
        //$boat->syncComponents();
        BoatSyncJob::dispatch($boat, $boat->external_id);

        dump('ok');

    }
}
