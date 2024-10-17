<?php

namespace App\Providers;

use App\Livewire\Coach\SessionSelectionSynth;
use App\Models\Coach\SessionSelection;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS
        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        JsonResource::withoutWrapping();

        FilamentColor::register([
            'orange-700' => Color::hex('#c2410c'),
            'slate-400' => Color::hex('#94a3b8'),
            'yellow-500' => Color::hex('#eab308'),
            'stone-300' => Color::hex('#d6d3d1'),
        ]);

        \Livewire::propertySynthesizer(SessionSelectionSynth::class);
    }
}
