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
        return $this->getObject("/product/v2/$id");
    }

    public function getProductOptions(string $id):array
    {
        return $this->getArray("/product/options/$id");
    }

    public function getBoatComponents(string $id):array
    {
        return $this->getArray("/orders/components/$id");
    }

    public function getBoatFittings(string $id):array
    {
        return $this->getArray("/orders/fittings/$id");
    }

    public function getBoatColors(string $id):array
    {
        return $this->getArray("/orders/colors/$id");
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

    public function getBoatChooserQuestions($quizId):array{
        return $this->getArray("/boatchooser/quiz/$quizId");
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

    public function setDiscount(User $user, float $discount){
        $response = $this->patch("/entidade/{$user->external_id}",[
            'discount' => $discount
        ]);
        if(!$response->ok()){
            Log::error('Error updating discount', [
                'e_id' => $user->external_id,
                'discount' => $discount,
            ]);
        }
    }

}
