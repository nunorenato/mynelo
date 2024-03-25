<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new
#[Title('Login')]
class extends Component {

    public bool $show = false;

    public function mount():void
    {
        $this->show = Auth::user()->alert_fill;
    }

    public function dismiss():void{
        $this->show = false;
        $user = Auth::user();
        $user->alert_fill = false;
        $user->save();
    }

} ?>
<div class="hidden">
    @if($show)
    <x-mary-alert title="Some profile data is missing" description="Tell us more about yourself" icon="o-exclamation-triangle" class="" wire:transition>
        <x-slot:actions>
            <x-mary-button label="Fill" :link="route('profile')" class="btn-outline btn-sm" />
            <x-mary-button label="Dismiss" wire:click="dismiss" class="btn-error btn-sm" />
        </x-slot:actions>
    </x-mary-alert>
    @endif
</div>
