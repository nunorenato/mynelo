<?php

namespace App\Enums;
use Kongulov\Traits\InteractWithEnum;

enum MagentoStatusEnum:string{
    use InteractWithEnum;

    case PROCESSING = 'processing';
    case PENDING = 'pending';
    case CLOSED = 'closed';
    case COMPLETE = 'complete';
    case CANCELED = 'canceled';
    case PENDING_PAYMENT = 'pending_payment';

    public function cssClass():string{
        return match($this){
            MagentoStatusEnum::PENDING,MagentoStatusEnum::PENDING_PAYMENT => 'badge-warning',
            MagentoStatusEnum::PROCESSING => 'badge-info',
            MagentoStatusEnum::CANCELED => 'badge-error',
            MagentoStatusEnum::COMPLETE => 'badge-success',
            MagentoStatusEnum::CLOSED => 'badge-neutral',
        };
    }

    public function toString():string{
        return ucfirst($this->value);
    }
}
