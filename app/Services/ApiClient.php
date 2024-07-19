<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class ApiClient
{
    private AuthTypeEnum $authType = AuthTypeEnum::None;
    private string $bearerToken;
    private string $apiKey;
    private string $apiKeyName;
    protected bool $useCache = false;
    protected int $cacheTTL = 3600*24; // 1 dia

    public function __construct(protected string $baseURL){}

    /**
     * Sets auth type
     *
     * If API Key, tokens should be an array with 2 pairs:
     * name => parameter name
     * key => the api key
     *
     * @param AuthTypeEnum $authType
     * @param array|string|null $tokens
     * @return void
     */
    public function setAuth(AuthTypeEnum $authType, array|string $tokens = null):void{
        $this->authType = $authType;

        switch ($authType){
            case AuthTypeEnum::BearerToken:
                if(is_string($tokens))
                    $this->bearerToken = $tokens;
                elseif (is_array($tokens)){
                    //
                }
                break;
            case AuthTypeEnum::Key:
                $this->apiKey = $tokens['key'];
                $this->apiKeyName = $tokens['name'];
                // TODO: exception if token empty
                break;
        }

    }

    private function buildCacheKey(string $endpoint, array $params):string{

        $reflect = new \ReflectionClass($this);

        return 'apiclient:'.$reflect->getShortName().':'.Str::replaceStart('/', '', $endpoint).':'.Arr::query($params);
    }

    protected function get(string $endpoint, array $params = []):Response{

        $fullURL = $this->baseURL.$endpoint;

        if($this->authType == AuthTypeEnum::None){
            return Http::get($fullURL, $params);
        }
        elseif($this->authType == AuthTypeEnum::BearerToken){
            return Http::withToken($this->bearerToken)->get($fullURL, $params);
        }
        elseif($this->authType == AuthTypeEnum::Key){
            $params[$this->apiKeyName] = $this->apiKey;
            Http::get($fullURL, $params);
        }

        return Http::get($fullURL, $params);
    }

    protected function getArray(string $endpoint, array $params = []):array{

        if($this->useCache){
            Log::debug('Trying from cache');

            $key = $this->buildCacheKey($endpoint, $params);

            return unserialize(Cache::remember($key, $this->cacheTTL, function() use ($endpoint, $params){
                $response = $this->get($endpoint, $params);

                if($response->ok()){
                    return serialize($response->json());
                }
                else
                    return serialize([]);

            }));
        }else{
            $response = $this->get($endpoint, $params);
            if($response->ok()){
                return $response->json();
            }
            else
                return [];
        }

    }

    protected function getObject(string $endpoint, array $params = []):object|null{

        if($this->useCache){
            Log::debug('Trying from cache');

            $key = $this->buildCacheKey($endpoint, $params);
            //dd($key);

            return unserialize(Cache::remember($key, $this->cacheTTL, function() use ($endpoint, $params){
                $response = $this->get($endpoint, $params);

                if($response->ok()){
                    return serialize($response->object());
                }
                else
                    return serialize(null);

            }));
        }else{
            $response = $this->get($endpoint, $params);
            if($response->ok()){
                return $response->object();
            }
            else
                return null;
        }

    }

    protected function post($endpoint, $payload):Response{

        Log::debug("API Client posting: $endpoint");

        $fullURL = $this->baseURL.$endpoint;

        if($this->authType == AuthTypeEnum::None){
            return Http::post($fullURL, $payload);
        }

        if($this->authType == AuthTypeEnum::BearerToken){
            return Http::withToken($this->bearerToken)->post($fullURL, $payload);
        }

        return Http::post($fullURL, $payload);
    }

    protected function patch($endpoint, $payload):Response{

        Log::debug("API Client patching: $endpoint");


        $fullURL = $this->baseURL.$endpoint;

        if($this->authType == AuthTypeEnum::None){
            return Http::patch($fullURL, $payload);
        }

        if($this->authType == AuthTypeEnum::BearerToken){
            return Http::withToken($this->bearerToken)->patch($fullURL, $payload);
        }

        return Http::patch($fullURL, $payload);
    }
}
