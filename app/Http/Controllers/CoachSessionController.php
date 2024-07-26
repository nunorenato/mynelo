<?php

namespace App\Http\Controllers;

use App\Http\Resources\CoachSessionResource;
use App\Jobs\CoachSessionUploadJob;
use App\Jobs\CoachSessionUploadLapJob;
use App\Jobs\CoachSessionUploadStatsJob;
use App\Models\Coach\Session;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CoachSessionController extends Controller
{

    public function store(Request $request)
    {

        Log::info('Create training session');
        Log::debug('Create training session', $request->toArray());

        $validated = $request->validate([
            'id_user' => 'required', // todo: exists:
            'id_boat' => 'required',
            'date' => 'required|numeric',
            'timezone' => 'sometimes',
        ]);

        if(!empty($validated['timezone'])){
            if(preg_match("/([+\-])?(\d\d):(\d\d)/", $validated['timezone'], $fuso)){
                $s = ($fuso[2]*3600 + $fuso[3]*60)*1000;
                if($fuso[1]=='-')
                    $validated['date'] -= $s;
                else
                    $validated['date'] += $s;
            }
        }

        $time = Carbon::createFromTimestampMs($validated['date']);

        $session = Session::firstOrCreate([
            'athleteid' => $validated['id_user'],
            'boatid' => $validated['id_boat'],
            'createdon' => $time->toDateTimeString(),
            'gpslng' => $time->milli,
            'gpslat' => 0,
        ]);

        return CoachSessionResource::collection([$session]); // the client expects an array...
    }

    public function upload(Request $request)
    {

        Log::info('Uploading session file');

        $jobs = [];
        $jobsLaps = [];
        $sessions = new Collection();

        //fazer array das sessions para fazer os stats no fim
        // e fazer novo array para as laps

        /**
         * @var UploadedFile $file
         */
        foreach ($request->allFiles()['upload'] as $file){
            //dump($file);
            $filename = $file->getClientOriginalName();

            Log::debug("Received file: $filename");

            $fullPath = Storage::disk('local')->path($file->storeAs('coach-tmp', $filename));
            $unzipedFile = self::unzip($fullPath);
            //Storage::disk('local')->delete("coach-tmp/$filename"); // keep and delete after one week

            $a = Str::of(Str::of($unzipedFile)->explode('.')->first())->explode('_');

            if($sessions->doesntContain('id', $a->first())){

                $session = Session::find($a->first());
                if(empty($session)){
                    Log::error("Error importing file. Session not found. File: $unzipedFile");
                    continue;
                }

                $sessions->add($session);
            }


            if(Str::contains($unzipedFile, 'l.txt', true)){
                Log::debug("Adding job for lap file: $unzipedFile");
                $jobsLaps[] = new CoachSessionUploadLapJob($session, Storage::disk('local')->path('coach-tmp/'.$unzipedFile));
            }
            else{
                Log::debug("Adding job for file: $unzipedFile");
                $jobs[] = new CoachSessionUploadJob($session, Storage::disk('local')->path('coach-tmp/'.$unzipedFile));
            }

          //  dump($filename);

        }
        $allJobs = collect($jobs, $jobsLaps)->concat($jobsLaps);
        $sessions->each(function (Session $item, $key) use ($allJobs){
            $allJobs->add(new CoachSessionUploadStatsJob($item));
        });
        Bus::chain($allJobs)->dispatch();

        return 'ok';
    }

    public function index(Request $request)
    {
        Log::debug('index', $request->toArray());
        return 'index';
    }

    public function show(Request $request)
    {
        Log::debug('show', $request->toArray());
        return 'show';
    }

    private static function unzip(string $filename){
        // Raising this value may increase performance
        $buffer_size = 1048576; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $filename);

        // Open our files (in binary mode)
        $file = gzopen($filename, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);

        return basename($out_file_name);
    }
}
