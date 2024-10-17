<?php

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Collection;

new class extends Component {

    use WithPagination, \App\Traits\ResetsPaginationWhenPropsChanges;

    public int $perPage = 10;

    public bool $showFilters = false;

    public ?int $month = 0;
    public ?int $year = 0;

    #[\Livewire\Attributes\Computed]
    public function sessions(): LengthAwarePaginator
    {
        return Auth::user()->coachSessions()
            ->processed()
            ->when($this->month > 0, fn(Builder $query) => $query->whereMonth('createdon', $this->month))
            ->when($this->year > 0, fn(Builder $query) => $query->whereYear('createdon', $this->year))
            ->orderByDesc('createdon')
            ->paginate($this->perPage);
    }

    #[\Livewire\Attributes\Computed]
    public function headers(): array
    {
        return [
            ['key' => 'createdon', 'label' => 'Date'],
            ['key' => 'details', 'label' => 'Description'],
            ['key' => 'distance', 'label' => 'Distance'],
            ['key' => 'duration', 'label' => 'Duration'],
            ['key' => 'boat.boat.model', 'label' => 'Boat'],
        ];
    }

    public function filterCount():int{
        return ($this->month==0?0:1);
    }

    public function clear()
    {
        $this->reset();
    }

    public function with()
    {
        $months = [];
        for ($i = 1; $i < 13; $i++) {
            $months[] = [
                'id' => $i,
                'name' => date('F', mktime(0, 0, 0, $i, 1, date('Y'))),
            ];
        }

        $years = Auth::user()->coachSessions()
            ->processed()
            ->selectRaw('YEAR(createdon) as id, YEAR(createdon) as name')
            ->distinct()
            ->get();

        return [
            'months' => $months,
            'years' => $years,
            'filterCount' => $this->filterCount(),
        ];
    }
}
?>
<div>
    <x-mary-header title="Nelo Coach" separator></x-mary-header>

    @if(count($this->sessions) == 0 && $filterCount == 0)
        <div class="grid lg:grid-cols-2 gap-5">
            <x-mary-card title="Start training">
                Get the Nelo Coach app and start recording and analysing your training sessions. It's free!

                <x-slot:figure>
                    <img src="images/neloSite_coach.png" alt="Nelo Coach app"/>
                </x-slot:figure>
                <x-slot:actions>
                    <x-mary-button class="btn-accent" label="Get it now" icon="tabler.brand-google-play"
                                   link="https://play.google.com/store/apps/details?id=com.nelocoach.mobile.android"
                                   external></x-mary-button>
                </x-slot:actions>
            </x-mary-card>

            <x-mary-card title="Instrument your sessions">
                The perfect partner to the Nelo Coach app, the Nelo Motion Sensor gives you perfect cadence information
                and with its 3-axis sensor can help you become a better paddler.

                <x-slot:figure>
                    <img src="images/nelo_motion_sensor.jpg" alt="Nelo Motion Sensor"/>
                </x-slot:figure>
                <x-slot:actions>
                    <x-mary-button class="btn-primary" label="Buy on Paddle Lab" icon="o-shopping-bag"
                                   link="https://paddle-lab.com/motion-sensor.html" external></x-mary-button>
                </x-slot:actions>
            </x-mary-card>
        </div>
    @else
        <livewire:coach.stats></livewire:coach.stats>
        <x-mary-card title="Your training sessions" class="mt-5">
            <x-slot:menu>
                <x-mary-button
                    label="Filters"
                    icon="o-adjustments-horizontal"
                    :badge="$filterCount"
                    badge-classes="bg-primary"
                    @click="$wire.showFilters = true"
                ></x-mary-button>
            </x-slot:menu>
            <x-mary-table :headers="$this->headers" :rows="$this->sessions"
                          with-pagination per-page="perPage" :per-page-values="[10, 20, 50]"
                          link="/coach/{id}"
            >
                @scope('cell_distance', $row)
                @if(is_numeric($row->distance))
                    {{ Number::format($row->distance/1000, 2) }} Km
                @else
                    -
                @endif
                @endscope
                @scope('cell_duration', $row)
                @if(is_numeric($row->duration))
                    {{ \Illuminate\Support\Carbon::createFromTimestamp($row->duration)->format('H:i:s') }}
                @else
                    -
                @endif
                @endscope
            </x-mary-table>
        </x-mary-card>
    @endif

    <x-mary-drawer title="Filters" right wire:model="showFilters" class="lg:w-1/3 gap-5">
        <x-mary-select label="Month" :options="$months" wire:model.live="month" placeholder="All" />
        <x-mary-select label="Year" :options="$years" wire:model.live="year" placeholder="All" />

        <x-slot:actions>
            <x-mary-button label="Close" icon="o-check" class="btn-secondary" @click="$wire.showFilters = false" />
            <x-mary-button label="Reset" icon="o-x-mark" wire:click="clear()" spinner />
        </x-slot:actions>
    </x-mary-drawer>
</div>
