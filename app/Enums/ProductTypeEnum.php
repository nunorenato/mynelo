<?php
namespace App\Enums;

use Kongulov\Traits\InteractWithEnum;

Enum ProductTypeEnum:int{
    use InteractWithEnum;

    case Boat = 1;
    case Seat = 2;
    case Footrest = 3;
    case Color = 4;
    case Rudder = 5;
    case Cover = 6;
    case NumberHolder = 7;
    case Strap = 8;
    case Platform = 9;
    case Rail = 10;
    case RudderSleeve = 11;
    case CableGuide = 12;
    case Component = 13;
    case Screws = 14;
    case Cable = 15;
    case Weight = 16;
    case Hatch = 17;
    case Fin = 18;
    case Misc = 19;
    case Tiderace = 20;
    case TBar = 21;
    case EvolutionCanoe = 22;
    case SeatRail = 23;
    case Sticker = 24;


    public static function fromAPI(int $neloId)
    {
        return match($neloId){
          10 => self::Seat,
          11, 168, 169, 62, 431, 432, 433 => self::Footrest,
          14 => self::Rudder,
          74 => self::Cover,
          65 => self::NumberHolder,
            1 => self::Boat,
            12, 63 => self::Strap,
            64 => self::Platform,
            69 => self::Rail,
            71 => self::RudderSleeve,
            72 => self::CableGuide,
            78, 79, 97, 104, 123, 136, 154, 155, 156, 165, 389, 13 => self::Component,
            80 => self::Screws,
            88 => self::Cable,
            101 => self::Weight,
            103, 135 => self::Hatch,
            105 => self::Fin,
            106 => self::Misc,
            188 => self::Tiderace,
            205 => self::TBar,
            289 => self::EvolutionCanoe,
            70 => self::SeatRail,
            137 => self::Sticker,

            default => null,
        };
    }

    public static function isIgnorable(int $neloId){
        $ignorables = [134];

        return in_array($neloId, $ignorables);
    }
}
