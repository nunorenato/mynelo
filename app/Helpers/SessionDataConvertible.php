<?php

namespace App\Helpers;

use App\Enums\UnitsEnum;
use App\Models\Coach\Session;
use App\Models\Coach\SessionData;
use App\Models\Coach\SessionLap;
use Illuminate\Support\Number;
use UnitConverter\Calculator\SimpleCalculator;
use UnitConverter\Registry\UnitRegistry;
use UnitConverter\Unit\Length\Kilometre;
use UnitConverter\Unit\Length\LengthUnit;
use UnitConverter\Unit\Length\Metre;
use UnitConverter\Unit\Length\Mile;
use UnitConverter\Unit\Length\Yard;
use UnitConverter\Unit\Speed\KilometrePerHour;
use UnitConverter\Unit\Speed\MetrePerSecond;
use UnitConverter\Unit\Speed\MilePerHour;
use UnitConverter\Unit\Speed\SpeedUnit;
use UnitConverter\UnitConverter;

class SessionDataConvertible implements SessionValues
{

    private UnitConverter $converter;
    private SpeedUnit $speedUnit;
    private LengthUnit $distanceUnit;
    public function __construct(private readonly SessionValues $sessionData, private readonly UnitsEnum $unit){

        $registry = new UnitRegistry([
            new MetrePerSecond,
            new KilometrePerHour,
            new MilePerHour,
            new Metre,
            new Kilometre,
            new Mile,
        ]);
        $this->converter = new UnitConverter($registry, new SimpleCalculator);

        list($this->speedUnit, $this->distanceUnit) = array_values($this->unit->toUnitConverter());
    }

    public function getAvgSpeed():float
    {
        return $this->convertSpeed($this->sessionData->getAvgSpeed());
    }

    public function getAvgSpeedWithUnits():string
    {
        return Number::format($this->getAvgSpeed(), 2). ' ' . $this->speedUnit->getScientificSymbol();
    }

    public function getSpeedWithUnits():string
    {
        return Number::format($this->getSpeed(),2). ' ' . $this->speedUnit->getScientificSymbol();
    }

    public function getSpeed():float
    {
        return $this->convertSpeed($this->sessionData->getSpeed());
    }

    public function getMaxSpeed():float
    {
        return $this->convertSpeed($this->sessionData->getMaxSpeed());
    }

    public function getMaxSpeedWithUnits():string
    {
        return Number::format($this->getMaxSpeed(),2). ' ' . $this->speedUnit->getScientificSymbol();
    }

    public function getDistance():float
    {
        return $this->convertDistance($this->sessionData->getDistance());
    }

    public function getDistanceWithUnits():string
    {
        return Number::format($this->getDistance(), $this->unit == UnitsEnum::Meters?0:2). ' ' . $this->distanceUnit->getScientificSymbol();
    }

    private function convertSpeed(int|float $speed):float
    {
        if($this->unit == UnitsEnum::Meters){
            return $speed;
        }
        return $this->converter->convert($speed)->from('mps')->to($this->speedUnit->getSymbol());

    }
    private function convertDistance(int|float $distance):float
    {
        if($this->unit == UnitsEnum::Meters){
            return $distance;
        }
        return $this->converter->convert($distance)->from('m')->to($this->distanceUnit->getSymbol());
    }

    private function convertDistancePerStroke(int|float $distance):float
    {
        if($this->unit != UnitsEnum::Miles){
            return $distance;
        }
        return $this->converter->convert($distance)->from('m')->to((new Yard())->getScientificSymbol());
    }

    public function getDuration(){
        return \Illuminate\Support\Carbon::createFromTimestamp($this->sessionData->getDuration())->format('H:i:s');
    }

    public function getAvgSpm(){
        return Number::format($this->sessionData->getAvgSpm(), 0);
    }

    public function getMaxSpm(){
        return Number::format($this->sessionData->getMaxSpm(), 0);
    }

    public function getAvgHeart(){
        return Number::format($this->sessionData->getAvgHeart(), 0);
    }


    public function getMaxHeart(){
        return Number::format($this->sessionData->getMaxHeart(), 0);
    }

    public function getDps()
    {
        return $this->convertDistancePerStroke($this->sessionData->getDps());
    }

    public function getAvgDps()
    {
        return $this->convertDistancePerStroke($this->sessionData->getAvgDps());
    }

    public function getMaxDps()
    {
        return $this->convertDistancePerStroke($this->sessionData->getMaxDps());
    }

    public function getDpsWithUnits()
    {
        return Number::format($this->getDps(), 1). ' ' . ($this->distanceUnit != UnitsEnum::Miles?(new Metre)->getScientificSymbol():(new Yard)->getScientificSymbol());
    }

    public function getAvgDpsWithUnits()
    {
        return Number::format($this->getAvgDps(), 1). ' ' . ($this->distanceUnit != UnitsEnum::Miles?(new Metre)->getScientificSymbol():(new Yard)->getScientificSymbol());
    }

    public function getMaxDpsWithUnits()
    {
        return Number::format($this->getMaxDps(), 1). ' ' . ($this->distanceUnit != UnitsEnum::Miles?(new Metre)->getScientificSymbol():(new Yard)->getScientificSymbol());
    }
}
