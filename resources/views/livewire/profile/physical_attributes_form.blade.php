<?php

use App\Models\User;
use App\Enums\GenderEnum;
use Livewire\Volt\Component;
use Illuminate\Validation\Rules\Enum;

new class extends Component
{
    public ?string $height;
    public ?string $weight;
    public ?GenderEnum $gender;

    public array $genders;

    public function mount():void
    {
        $this->height = Auth::user()->height;
        $this->weight = Auth::user()->weight;
        $this->gender = Auth::user()->gender;

        $x = array_combine(GenderEnum::values(), GenderEnum::names());
        $v = [];
        foreach ($x as $value=>$name){
            $v[] = ['id' => $value, 'name' => $name];
        }
        $this->genders = $v;
    }

    public function updatePhysical():void
    {
        $user = Auth::user();

        if(isset($this->height))
            $this->height = Str::replace(',', '.', $this->height);
        if(isset($this->weight))
            $this->weight = Str::replace(',', '.', $this->weight);

        $validated = $this->validate([
            'height' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'gender' => ['nullable', new Enum(\App\Enums\GenderEnum::class)],
        ]);
        $validated['alert_fill'] = false;
        $user->fill($validated);
        $user->save();

        $this->dispatch('profile-updated', name: $user->name);

        activity()
            ->on($user)
            ->event('updated physical attributes')
            ->log('USER update');
    }
};
?>
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Physical Attributes') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Add your physical information") }}
        </p>
    </header>

    <x-mary-form wire:submit="updatePhysical" class="mt-6 ">
        <div class="grid lg:grid-cols-2 gap-5">
            <x-mary-input type="text" label="Height" wire:model="height" suffix="m"  class="space-y-6 text-right" autofocus></x-mary-input>
            <x-mary-input type="text" label="Weight" wire:model="weight" suffix="kg"  class="space-y-6 text-right" ></x-mary-input>
            <x-mary-select label="Gender" wire:model="gender" :options="$genders"  class="space-y-6" placeholder="---"></x-mary-select>
        </div>
        <div class="flex items-center gap-4">
            <x-mary-button label="Save" spinner="save" type="submit" class="btn-primary" />

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </x-mary-form>

</section>
