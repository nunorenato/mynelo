<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;


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

}
