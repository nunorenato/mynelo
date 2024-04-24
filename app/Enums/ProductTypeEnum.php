<?php
namespace App\Enums;

Enum ProductTypeEnum:int{
    case Boat = 1;
    case Seat = 2;
    case Footrest = 3;
    case Color = 4;
    case Rudder = 5;
    case Cover = 6;
    case NumberHolder = 7;

    public static function fromAPI(int $neloId)
    {
        return match($neloId){
          10 => static::Seat,
          11, 168, 169, 62, 431, 432, 433 => static::Footrest,
          14 => static::Rudder,
          74 => static::Cover,
          65 => static::NumberHolder,
            1 => static::Boat,
            default => null,
        };
    }
}
