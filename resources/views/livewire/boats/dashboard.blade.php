<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new class extends Component {

    public bool $noBoats = false;

    public function mount():void
    {
        $this->noBoats = Auth::user()->boats->count()==0;
    }

    /*public function dismiss():void{
        $this->show = false;
        $user = Auth::user();
        $user->alert_fill = false;
        $user->save();
    }*/

} ?>
<div class=""><!-- class hidden?? -->
    @if($noBoats)
        <x-mary-alert title="No registered boats" description="Register your first boat" icon="o-information-circle" class="alert-info" wire:transition>
            <x-slot:actions>
                <livewire:boats.preregister class="btn-sm" />
            </x-slot:actions>
        </x-mary-alert>
    @endif
</div>
