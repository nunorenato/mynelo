<?php

namespace App\Helpers;

/**
 * Represents a Nelo Coach session, lap or session data point
 */
interface SessionValues
{
    public function getSpeed();
    public function getAvgSpeed();
    public function getMaxSpeed();
    public function getDistance();
    public function getDuration();
    public function getAvgSpm();
    public function getMaxSpm();
    public function getAvgHeart();
    public function getMaxHeart();
    public function getDps();
    public function getAvgDps();
    public function getMaxDps();
}
