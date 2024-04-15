<?php

use App\Models\Dealer;
use App\Models\BoatRegistration;
use App\Enums\StatusEnum;
use App\Mail\PreRegistrationMail;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use App\Traits\HasCssClassAttribute;

new class extends Component {
    use HasCssCLassAttribute;

    public string $label = 'Register';

    public bool $show = false;
    public bool $showBoatError = false;
    public bool $showOK = false;

    public string $boatId;

    public string $seller;

    public function save():void{

        $user = Auth::user();

        $validated = $this->validate([
            'boatId' => ['required', 'numeric', Rule::unique(BoatRegistration::class, 'boat_id')->where(fn (Builder $query) => $query->where('user_id', $user->id))],
            'seller' => ['required', 'max:250'],
        ],
        [
            'boatId.unique' => 'You have already registered this boat'
        ]);

        activity()
            ->by($user)
            ->event('initiated')
            ->withProperties(['of_id' => $validated['boatId']])
            ->log('Started pre-registering');

        $bc = new \App\Http\Controllers\BoatController();
        $boat = $bc->getWithSync($validated['boatId']);

        if($boat != null){
            $boatRegistration = BoatRegistration::create([
                'boat_id' => $boat->id,
                'user_id' => $user->id,
                'seller' => $validated['seller'],
                'status' => StatusEnum::PENDING,
            ]);

            Mail::to(config('nelo.emails.admins'))
                ->queue(new PreRegistrationMail($boatRegistration));

            activity()
                ->on($boatRegistration)
                ->by($user)
                ->event('created')
                ->log('Pre registration');
        }
        else{
            activity()
                ->by($user)
                ->event('error')
                ->log('Error getting boat information');
        }

        $this->showBoatError = $boat==null;
        $this->showOK = !$this->showBoatError;
    }

} ?>
<div>
    <x-mary-button :label="$label" @click="$wire.show = true" icon="o-plus" class="btn-primary {{ $class }}" responsive />

{{-- This component can be used inside another forms. So we teleport it to body to avoid nested form submission conflict --}}
<template x-teleport="body">
    <x-mary-modal wire:model="show" title="Register a boat">
        <hr class="mb-5" />
        @if($showBoatError)
            <x-mary-alert title="Invalid boat number" description="Please check if the boat ID number is correct" icon="o-exclamation-triangle" class="alert-error mb-5"></x-mary-alert>
        @endif
        @if($showOK)
            <p>{{ __('Your boat registration request was successfully submitted and our team will evaluate it as soon as possible.
                You will then be notified in order to complete the registration') }}</p>
            <x-slot:actions>
                <x-mary-button label="Continue" link="boats" class="btn-success" />
            </x-slot:actions>
        @else
            <x-mary-form wire:submit="save">
                <x-mary-input label="Boat ID" wire:model="boatId" />
                <x-mary-input label="Where did you buy it from" wire:model="seller" hint="Write the name of the dealer or person that sold you the boat"></x-mary-input>

                <x-slot:actions>
                    <x-mary-button label="Cancel" @click="$wire.show = false" />
                    <x-mary-button label="Submit" icon="o-check" class="btn-primary" type="submit" spinner="save" />
                </x-slot:actions>
            </x-mary-form>
        @endif
    </x-mary-modal>
</template>
</div>
