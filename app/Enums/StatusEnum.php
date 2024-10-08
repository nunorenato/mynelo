<?php

namespace App\Enums;
use Kongulov\Traits\InteractWithEnum;

enum StatusEnum:string{
    use InteractWithEnum;

    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case COMPLETE = 'complete';
    case CANCELED = 'canceled';

    public function cssClass():string{
        return match($this){
            StatusEnum::PENDING => 'badge-warning',
            StatusEnum::VALIDATED => 'badge-info',
            StatusEnum::CANCELED => 'badge-error',
            StatusEnum::COMPLETE => 'badge-success'
        };
    }

    public function toString():string{
        return ucfirst($this->value);
    }
}
