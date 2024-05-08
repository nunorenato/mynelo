<?php

use App\Models\BoatRegistration;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\On;
use Livewire\Attributes\Modelable;

new class extends Component {
    use Toast;

    #[Modelable]
    public ?BoatRegistration $boatRegistration;

    public bool $deleteModal = false;

    public function removeBoat():void
    {

        $this->boatRegistration->delete();

        activity()
            ->on($this->boatRegistration)
            ->by(Auth::user())
            ->event('removed')
            ->log('Boat registration removed');

        $this->deleteModal = false;
        $this->success(
            title: 'Boat removed',
            redirectTo: route('boats')
        );
    }

    #[On('showDelete')]
    public function showModal()
    {
        $this->deleteModal = true;
    }
}
?>
<div>
    <x-mary-modal wire:model="deleteModal" title="Confirm removal">
        <div>Are you sure you wish to delete this boat from your list?</div>
        <x-slot:actions>
            <x-mary-button label="Delete" wire:click="removeBoat" class="btn-error" spinner></x-mary-button>
            <x-mary-button label="Cancel" @click="$wire.deleteModal = false"></x-mary-button>
        </x-slot:actions>
    </x-mary-modal>
</div>
