<?php
namespace App\Enums;

use UnitConverter\Unit\Length\Kilometre;
use UnitConverter\Unit\Length\Metre;
use UnitConverter\Unit\Length\Mile;
use UnitConverter\Unit\Speed\KilometrePerHour;
use UnitConverter\Unit\Speed\MetrePerSecond;
use UnitConverter\Unit\Speed\MilePerHour;

Enum UnitsEnum:string
{
    case Kilometers = 'km';
    case Meters = 'm';
    Case Miles = 'ml';

    public function printableUnits():array{
        return match($this){
            UnitsEnum::Kilometers => ['speed' => (new KilometrePerHour)->getScientificSymbol(), 'distance' => (new Kilometre)->getScientificSymbol()],
            UnitsEnum::Meters => ['speed' => (new MetrePerSecond)->getScientificSymbol(), 'distance' => (new Metre)->getScientificSymbol()],
            UnitsEnum::Miles => ['speed' => (new MilePerHour)->getScientificSymbol(), 'distance' => (new Mile)->getScientificSymbol()],
        };
    }

    public function toUnitConverter():array{
        return match($this){
            UnitsEnum::Kilometers => ['speed' => new KilometrePerHour, 'distance' => new Kilometre],
            UnitsEnum::Meters => ['speed' => new MetrePerSecond, 'distance' => new Metre],
            UnitsEnum::Miles => ['speed' => new MilePerHour, 'distance' => new Mile],
        };
    }

}
