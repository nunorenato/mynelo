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

    public function searchCouponByCode(string $code):object|null
    {
        $response = $this->get('/coupons/search', [
            'searchCriteria[filterGroups][0][filters][0][field]' => 'code',
            'searchCriteria[filterGroups][0][filters][0][value]' => $code
        ]);
        if($response->ok()){
            $obj = $response->object();
            if(count($obj->items) > 0)
                return $obj->items[0];
            else
                return null;
        }
        else
            return null;
    }

    public function getProduct(string $sku):object|null{
        $response = $this->get('/products/' . $sku, [
            'editMode' => false,
            'storeId' => 6,
            'forceReload' => false,
        ]);
        if($response->ok()){
            $obj = $response->object();
            foreach ($obj->media_gallery_entries as $media) {
                if($media->media_type == 'image'){
                    $media->file = 'https://paddle-lab.com/pub/media/catalog/product'.$media->file;
                }
            }
            return $obj;
        }
        else
            return null;
    }

}
