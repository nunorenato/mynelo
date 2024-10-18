<?php

use App\Helpers\AthleteTotals;
use App\Helpers\SessionDataConvertible;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {

    private AthleteTotals $totals;

    private SessionDataConvertible $totalsFormated;

    public function mount()
    {
        $this->totals = new AthleteTotals(Auth::user());

        if($this->totals->nSessions > 0){
            $this->totalsFormated = new SessionDataConvertible($this->totals, \App\Enums\UnitsEnum::Kilometers);
        }

    }

    public function with():array
    {
        return[
            'nSessions' => $this->totals->nSessions,
            'duration' => $this->totals->getDuration(),
            'totalsFormated' => $this->totalsFormated??null,
        ];
    }

}
?>
<div class="gap-5 flex">
    @if($nSessions > 0)
        <x-mary-stat
            title="Sessions"
            icon="tabler.ripple"
            color="text-blue-500"
            value="{{ $nSessions }}"
        />
        <x-mary-stat
            title="Total distance"
            icon="tabler.route-2"
            color="text-amber-800"
            value="{{ $totalsFormated?->getDistanceWithUnits() }}"
        />
        <x-mary-stat
            title="Total time"
            icon="tabler.clock-play"
            color="text-teal-800"
            value="{{ \Carbon\CarbonInterval::seconds($duration)->cascade()->forHumans(parts: 2) }}"
            description="hours"
        />
        <x-mary-stat
            title="Average speed"
            icon="tabler.gauge"
            color="text-red-700"
            value="{{ $totalsFormated->getAvgSpeedWithUnits() }}"
        />
    @endif
</div>
