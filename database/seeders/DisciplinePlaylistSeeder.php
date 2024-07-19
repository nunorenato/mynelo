<?php

namespace Database\Seeders;

use App\Models\Discipline;
use Illuminate\Database\Seeder;

class DisciplinePlaylistSeeder extends Seeder
{
    public function run(): void
    {
        $playlists = [
            'Sprint' => 'PL97bnGYVvtIElcYvqUwSvReh9qqo8uASH',
            'Fitness' => 'PL97bnGYVvtIExn4OS-_bnKYWmXPdiSVhQ',
            'Marathon' => 'PL97bnGYVvtIElcYvqUwSvReh9qqo8uASH',
            'Slalom' => 'PL97bnGYVvtIEirC_hNM0bWYO_kkieNALk',
            'Touring' => 'PL97bnGYVvtIGFq6HxTzjDPMO6XN5Ai8rk',
            'Kids' => 'PL97bnGYVvtIEokSdw2kDfNw3gwxL3HydH',
        ];

        foreach ($playlists as $discipline=>$playlist){
            Discipline::firstWhere('name', $discipline)->update([
                'playlist' => $playlist,
            ]);
        }
    }
}
