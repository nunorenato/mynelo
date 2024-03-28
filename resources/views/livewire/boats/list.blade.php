<?php

use App\Models\BoatRegistration;
use App\Models\Boat;
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
        ];
    }
};
?>
<x-mary-table :headers="$headers" :rows="$boats">
    @scope('cell_status', $row)
    <x-mary-badge :class="$row->status->cssClass()" :value="$row->status->toString()"></x-mary-badge>
    @endscope
</x-mary-table>
