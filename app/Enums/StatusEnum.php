<?php

namespace App\Enums;
enum StatusEnum:string{
    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case COMPLETE = 'complete';
    case CANCELED = 'canceled';

    public function cssClass():string{
        return match($this){
            StatusEnum::PENDING => 'badge-warning',
        };
    }

    public function toString():string{
        return ucfirst($this->value);
    }
}
