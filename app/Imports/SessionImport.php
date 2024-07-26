<?php

namespace App\Imports;

use App\Models\Coach\Session;
use App\Models\Coach\SessionData;
use Maatwebsite\Excel\Concerns\ToModel;


class SessionImport implements ToModel
{

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

    public function model(array $row)
    {

        $seconds = $row[2] / 1000;
        $miliseconds = fmod($row[2],1000)/100;
        if($miliseconds>9)
            $miliseconds=9;

        if(!empty($row[19]) && is_numeric($row[19]) && $row[19] > 0){
            $spm = $spm2 = $row[19]; // External sensor
        }
        else{
            $spm = $row[0];
            $spm2 = $row[19];
        }

        $dps = $row[1]<40?0:$row[0]*60/$row[1]; // SPM tem de ser maior que 40

        $accelx = $row[15]==-1?$row[9]:$row[15];
        $accely = $row[16]==-1?$row[10]:$row[16];
        $accelz = $row[17]==-1?$row[11]:$row[17];

        $heart = !empty($row[18])&&is_numeric($row[18])?$row[18]:0;

        //dd($row);
        return new SessionData([
            'trainid' => $this->session->id,
            'tagtime' => date("Y-m-d H:i:s",$seconds),
            'decima_segundo' => $miliseconds,
            'speed' => $row[0],
            'spm' => $row[0],
            'pitch' => $row[13],
            'roll' => $row[13],
            'heading' => $row[6],
            'gpsx' => $row[3],
            'gpxy' => $row[4],
            'gpsz' => $row[5],
            'dps' => $dps,
            'accelX' => $accelx,
            'accelY' => $accely,
            'accelZ' => $accelz,
            'raw' => $row[8],
            'accu' => $row[7],
            'head2' => $row[14],
            'heart' => $heart,
            'app_spm' => $spm2,
        ]);
    }

    public function upsertColumns()
    {
        return ['CORRF_GUIA', 'CORRF_VALOR_SEM_IVA'];
    }
}
