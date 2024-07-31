<?php

namespace App\Console\Commands;

use App\Helpers\ZipHelper;
use App\Models\Coach\Session;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportCoachSessionLapsCommand extends Command
{
    protected $signature = 'import:coach-session-laps {filename}';

    protected $description = 'Command description';

    public function handle(): void
    {
        //dd(Storage::disk('local')->path($this->argument('filename')));

        $splits = explode('_', $this->argument('filename'));
        $session = Session::find($splits[0]);

        //dd($session);

        $session->importLaps(
            Storage::disk('local')->path('coach-tmp/'.
                ZipHelper::unzip(
                    Storage::disk('local')->path('coach-tmp/'.$this->argument('filename'))
                )
            )
        );
    }
}
