<?php

use App\Models\BoatRegistration;
use App\Models\Boat;
use App\Enums\StatusEnum;
use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {

    public function boats():Collection{
        return Auth::user()->boats()->with('boat')->get();
    }

    public function headers():array
    {
        return [
            ['key' => 'boat.external_id', 'label' => 'Boat ID'],
            ['key' => 'boat.model', 'label' => 'Model'],
            ['key' => 'status', 'label' => 'Status'],
        ];
    }

    public function with():array{
        return [
            'boats' => $this->boats(),
            'headers' => $this->headers(),
            'cell_decoration' => [
                'boat.external_id' => [
                    'p-0' => fn( $registration) => $registration->status == StatusEnum::COMPLETE || $registration->status == StatusEnum::VALIDATED,
                ],
                'boat.model' => [
                    'p-0' => fn(BoatRegistration $registration) => $registration->status == StatusEnum::COMPLETE || $registration->status == StatusEnum::VALIDATED,
                ],
            ],
        ];
    }
};
?>
<x-mary-table :headers="$headers" :rows="$boats" :cell-decoration="$cell_decoration">
    @scope('cell_boat.external_id', $row)
        @if($row->status == StatusEnum::COMPLETE || $row->status == StatusEnum::VALIDATED)
            <a href="{{ route('boats.show', $row->id) }}" wire:navigate class="block py-3 px-4">{{ $row->boat->external_id }}</a>
        @else
            {{ $row->boat->external_id }}
       @endif
    @endscope
    @scope('cell_boat.model', $row)
        @if($row->status == StatusEnum::COMPLETE || $row->status == StatusEnum::VALIDATED)
            <a href="{{ route('boats.show', $row->id) }}" wire:navigate class="block py-3 px-4">{{ $row->boat->model }}</a>
        @else
            {{ $row->boat->model }}
        @endif
    @endscope
    @scope('cell_status', $row)
    <x-mary-badge :class="$row->status->cssClass()" :value="$row->status->toString()"></x-mary-badge>
    @endscope
</x-mary-table>
