<?php

namespace App\Console\Commands;

use App\Models\Coach\Session;
use Illuminate\Console\Command;

class CoachUpdateSessionStatsCommand extends Command
{
    protected $signature = 'coach:update-session-stats {session}';

    protected $description = 'Update session stats';

    public function handle(): void
    {
        $session = Session::find($this->argument('session'));
        $session->updateStats();
    }
}
