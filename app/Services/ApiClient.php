<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class ApiClient
{
    private AuthTypeEnum $authType = AuthTypeEnum::None;
    private string $bearerToken;

    public function __construct(protected string $baseURL){}

    public function setAuth(AuthTypeEnum $authType, array|string $tokens = null):void{
        $this->authType = $authType;

        if(is_string($tokens))
            $this->bearerToken = $tokens;
        elseif (is_array($tokens)){
            //
        }

    }
    protected function get(string $endpoint, array $params = []):Response{

        $fullURL = $this->baseURL.$endpoint;

        if($this->authType == AuthTypeEnum::None){
            return Http::get($fullURL, $params);
        }

        if($this->authType == AuthTypeEnum::BearerToken){
            return Http::withToken($this->bearerToken)->get($fullURL, $params);
        }

        return Http::get($fullURL, $params);
    }
    protected function post($endpoint, $payload):Response{

        $fullURL = $this->baseURL.$endpoint;

        if($this->authType == AuthTypeEnum::None){
            return Http::post($fullURL, $payload);
        }

        if($this->authType == AuthTypeEnum::BearerToken){
            return Http::withToken($this->bearerToken)->post($fullURL, $payload);
        }

        return Http::post($fullURL, $payload);
    }
}