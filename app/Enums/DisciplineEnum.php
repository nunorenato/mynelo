<?php
namespace App\Enums;

Enum DisciplineEnum:int{
    case Sprint = 1;
    case Marathon = 2;
    case Downriver = 3;
    case Slalom = 4;
    case Ocean = 5;
    case Expedition = 6;
    case Fitness = 7;
    case Touring = 8;
    case Fishing = 9;

    public static function fromAPI(int $neloId){
        return match($neloId){
            149, 210 => static::Sprint,
            151, 278 => static::Ocean,
            152 => static::Slalom,
            153,388 => static::Touring,
            //162 => rowing
            170 => static::Expedition,
            241 => static::Marathon,
            242 => static::Fitness,
        };
    }
}
