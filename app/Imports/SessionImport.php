<?php

namespace App\Imports;

use App\Models\Coach\Session;
use App\Models\Coach\SessionData;
use Maatwebsite\Excel\Concerns\ToModel;


class SessionImport implements ToModel
{

    const SPEED = 0; // troquei de 0 para 8 para ser o raw e não a velocidade suavizada
    const SPM = 1;
    const TIMESTAMP = 2;
    const LAT = 3;
    const LNG = 4;
    const ALT = 5;
    const HEADING = 6;
    const ACCU = 7;
    const RAW = 8;
    const ACCELX = 9;
    const ACCELY = 10;
    const ACCELZ = 11;
    const ROLL = 12;
    const PITCH = 13;
    const HEAD2 = 14;
    const LACCELX = 15;
    const LACCELY = 16;
    const LACCELZ = 17;
    const HEART = 18;
    const EXT_SENSOR = 19;

    /*
define('SPEED', 0); // troquei de 0 para 8 para ser o raw e não a velocidade suavizada
define('SPM', 1);
define('TIMESTAMP', 2);
define('LAT', 3);
define('LNG', 4);
define('ALT', 5);
define('HEADING', 6);
define('ACCU', 7);
define('RAW', 8);
define('ACCELX', 9);
define('ACCELY', 10);
define('ACCELZ', 11);
define('ROLL', 12);
define('PITCH', 13);
define('HEAD2', 14);
define('LACCELX', 15);
define('LACCELY', 16);
define('LACCELZ', 17);
define('HEART', 18);
define('EXT_SENSOR', 19); // vaaka
    */

    public function __construct(private readonly Session $session)
    {

    }

    public function model(array $row):SessionData
    {

        $seconds = $row[self::TIMESTAMP] / 1000;
        $milliseconds = fmod($row[self::TIMESTAMP], 1000) / 100;
        if ($milliseconds > 9)
            $milliseconds = 9;

        if (!empty($row[self::EXT_SENSOR]) && is_numeric($row[self::EXT_SENSOR]) && $row[self::EXT_SENSOR] > 0) {
            $spm = $spm2 = $row[self::EXT_SENSOR]; // External sensor
        } else {
            $spm = $row[self::SPM];
            $spm2 = $row[self::EXT_SENSOR];
        }

        $dps = $spm < 40 ? 0 : $row[self::SPEED] * 60 / $spm; // SPM tem de ser maior que 40

        $accelx = $row[self::LACCELX] == -1 ? $row[self::ACCELX] : $row[self::LACCELX];
        $accely = $row[self::LACCELY] == -1 ? $row[self::ACCELY] : $row[self::LACCELY];
        $accelz = $row[self::LACCELZ] == -1 ? $row[self::ACCELZ] : $row[self::LACCELZ];

        $heart = !empty($row[self::HEART]) && is_numeric($row[self::HEART]) ? $row[self::HEART] : 0;

//dd($row);
        return new SessionData([
            'trainid' => $this->session->id,
            'tagtime' => date("Y-m-d H:i:s", $seconds),
            'decima_segundo' => $milliseconds,
            'speed' => $row[self::SPEED],
            'spm' => $spm,
            'pitch' => $row[self::PITCH],
            'roll' => $row[self::ROLL],
            'heading' => $row[self::HEADING],
            'gpsx' => $row[self::LAT],
            'gpxy' => $row[self::LNG],
            'gpsz' => $row[self::ALT],
            'dps' => $dps,
            'accelX' => $accelx,
            'accelY' => $accely,
            'accelZ' => $accelz,
            'raw' => $row[self::RAW],
            'accu' => $row[self::ACCU],
            'head2' => $row[self::HEAD2],
            'heart' => $heart,
            'app_spm' => $spm2,
        ]);
    }

}
