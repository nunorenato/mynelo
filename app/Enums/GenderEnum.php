<?php

namespace App\Enums;

use Kongulov\Traits\InteractWithEnum;

enum GenderEnum: string {
    use InteractWithEnum;
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';
}
