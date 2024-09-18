<?php

namespace App\Services;

use App\Enums\AuthTypeEnum;
use Illuminate\Support\Facades\Log;

class MagentoApiClient extends ApiClient
{

    public function __construct(){
        parent::__construct(config('nelo.magento.api.url'));
        $this->setAuth(AuthTypeEnum::BearerToken, config('nelo.magento.api.token'));
    }

    public function generateCoupon(int $rule):string|null{

        Log::info("Generating Magento coupon for rule $rule");

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
        $obj = $this->getObject('/coupons/search', [
            'searchCriteria[filterGroups][0][filters][0][field]' => 'code',
            'searchCriteria[filterGroups][0][filters][0][value]' => $code
        ]);
        if($obj != null){
            if(count($obj->items) > 0)
                return $obj->items[0];
            else
                return null;
        }
        else
            return null;
    }

    public function deleteCoupon(int $couponId):void{
        $response = $this->delete("/coupons/$couponId");

        if(!$response->ok()){
            Log::error('Error deleting coupon');
        }
    }

    public function getProduct(string $sku):object|null{
        $obj = $this->getObject('/products/' . $sku, [
            'editMode' => false,
            'storeId' => 6,
            'forceReload' => false,
        ]);
        if($obj != null){
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
