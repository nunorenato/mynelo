<?php

namespace App\Console\Commands;

use App\Helpers\ZipHelper;
use App\Models\Coach\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportCoachSessionDataCommand extends Command
{
    protected $signature = 'coach:import-session-data {filename} {--clear}';

    protected $description = 'Import CSV data for Nelo Coach session data';

    public function handle(): void
    {
        //dd(Storage::disk('local')->path($this->argument('filename')));

        $splits = explode('_', $this->argument('filename'));
        $session = Session::find($splits[0]);

        if($this->option('clear')) {
            $session->sessionData()->delete();
        }

        $session->importData(
            Storage::disk('local')->path('coach-tmp/'.
                ZipHelper::unzip(
                    Storage::disk('local')->path('coach-tmp/'.$this->argument('filename'))
                )
            )
        );
    }
}
