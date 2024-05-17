<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;

class MagentoApiClient extends ApiClient
{
    public function __construct(){
        parent::__construct(config('nelo.magento.api.url'));
        $this->setAuth(AuthTypeEnum::BearerToken, config('nelo.magento.api.token'));
    }

    public function generateCoupon():string|null{

        $response = $this->post('/coupons/generate', [
            'couponSpec' => [
                'rule_id' => config('nelo.magento.coupon_rule'),
                'quantity' => 1,
                'length' => 12,
                'delimiter_at_every' => 4,
            ]
        ]);

        if($response->ok()){
            $coupons = $response->json();
            if(is_array($coupons) && count($coupons) > 0)
                return $coupons[0];
            else
                return null;
        }
        return null;
    }

}
