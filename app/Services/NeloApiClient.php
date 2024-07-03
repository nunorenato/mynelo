<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;
use App\Http\Resources\NeloBoatRegistrationResource;
use App\Http\Resources\NeloOwnerResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;


class NeloApiClient extends ApiClient
{

    public function __construct(){
        parent::__construct(config('nelo.nelo_api_url'));
       // $this->setAuth(AuthTypeEnum::BearerToken, config('nelo.magento.api.token'));
    }


    public function getProduct(string $id):object|null{
        $response = $this->get("/product/v2/$id");
        if($response->ok()){
            return $response->object();
        }
        else
            return null;
    }

    public function getProductOptions(string $id):array
    {
        $response = $this->get("/product/options/$id");
        if($response->ok()){
            return $response->json();
        }
        else
            return [];
    }

    public function getBoatComponents(string $id):array
    {
        $response = $this->get("/orders/components/$id");
        if($response->ok()){
            return $response->json();
        }
        else
            return [];
    }

    public function getBoatFittings(string $id):array
    {
        $response = $this->get("/orders/fittings/$id");
        if($response->ok()){
            return $response->json();
        }
        else
            return [];
    }

    public function getBoatColors(string $id):array
    {
        $response = $this->get("/orders/colors/$id");
        if($response->ok()){
            return $response->json();
        }
        else
            return [];
    }

    public function storeOwner(User $user):void{
        $resource = new NeloOwnerResource($user);
        $response = $this->post('/proprietario', $resource);
        if($response->ok()){
            $user->external_id = ($response->object())->id;
            $user->save();
        }
        else{
            Log::error('Error storing new owner', $resource->toArray(request()));
        }
    }

    public function updateOwner(User $user):void{

        if(empty($user->external_id)){
            self::storeOwner($user);
            return;
        }

        $resource = new NeloOwnerResource($user);
        $response = $this->patch("/proprietario/{$user->external_id}", $resource);
        if(!$response->ok()){
            Log::error('Error updating owner', $resource->toArray(request()));
        }
    }

    public function storeRegistration(\App\Models\BoatRegistration $boatRegistration):void{
        $resource = new NeloBoatRegistrationResource($boatRegistration);
        $response = $this->post("/proprietario/{$boatRegistration->user->external_id}/boats", $resource);
        if(!$response->ok()){
            Log::error('Error storing new boat registration', $resource->toArray(request()));
        }
    }

    public function updateRegistration(\App\Models\BoatRegistration $boatRegistration):void{
        $resource = new NeloBoatRegistrationResource($boatRegistration);
        $response = $this->patch("/proprietario/{$boatRegistration->user->external_id}/boats/{$boatRegistration->boat->external_id}", $resource);
        if(!$response->ok()){
            Log::error($response->body());
            Log::error('Error updating boat registration', $resource->toArray(request()));
        }
    }

    public function getBoatChooserFrom():string{
        $response = $this->get('/web/boatchooser');
        if($response->ok()){
            return $response->body();
        }
        else{
            return '';
        }
    }

    public function getBoatChooserQuestions():array{
        $response = $this->get('/boatchooser/questions/grouped');
        if($response->ok()){
            return $response->json();
        }
        else{
            return [];
        }
    }

    public function chooseBoat(array $answers):array{
        $response = $this->post('/boatchooser/choose', $answers);
        if($response->ok()){
            return $response->json();
        }
        else{
            Log::error('Error choosing boat', $answers);
            return [];
        }
    }

}
