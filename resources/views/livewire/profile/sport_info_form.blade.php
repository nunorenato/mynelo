<?php

use App\Models\User;
use App\Models\Goal;
use App\Enums\GenderEnum;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Volt\Component;
use Illuminate\Validation\Rules\Enum;

new class extends Component
{
    public ?int $competition = 1;
    public ?string $club;
    public ?int $discipline_id;
    public ?int $weekly_trainings;
    public ?string $time_500;
    public ?string $time_1000;
    public ?array $goals;

    public array $selectedGoals = [];

    public array $competitive = [
        ['id' => 1, 'name' => 'Racing'],
        ['id' => 0, 'name' => 'Non racing'],
    ];

    //public array $allGoals;

    public function mount():void
    {
        $user = Auth::user();

        $this->competition = $user->competition===false?0:1;
        $this->club = $user->club;
        $this->discipline_id = $user->discipline_id;
        $this->weekly_trainings = $user->weekly_trainings;
        $this->time_500 = $user->time_500;
        $this->time_1000 = $user->time_1000;

        if(is_numeric($user->time_500)){
            $this->time_500 = rtrim(CarbonInterval::milliseconds($this->time_500*1000)->cascade()->format('%I:%S.%f'), '0');
        }
        if(is_numeric($user->time_1000)){
            $this->time_1000 = rtrim(CarbonInterval::milliseconds($this->time_1000*1000)->cascade()->format('%I:%S.%f'), '0');
        }

        $this->goals = array_fill_keys($user->goals->pluck('id')->toArray(), true);
    }

    public function updateSports():void
    {
        $user = Auth::user();
        $validated = $this->validate([
            'competition' => ['required'],
            'club' => ['nullable', 'max:255'],
            'discipline_id' => ['nullable'],
            'weekly_trainings' => ['nullable', 'numeric'],
            'time_500' => ['nullable', 'regex:/^\d+:\d\.?\d*$/'],
            'time_1000' => ['nullable', 'regex:/^\d+:\d\.?\d*$/'],
            'goals' => ['nullable'],
        ]);
        //dd($validated);

        self::toDecimalSeconds($validated['time_500']);
        self::toDecimalSeconds($validated['time_1000']);

        $validated['alert_fill'] = false;

        $user->fill($validated);
        $user->save();

        /**
         * o array validado vem no formato id => true ou false
         *
         * obter os ids apenas dos true, ou seja, seleccionados
         */
        $user->goals()->sync(array_keys($validated['goals'], true));

        $this->dispatch('profile-updated', name: $user->name);

        activity()
            ->on($user)
            ->event('updated sports attributes')
            ->log('USER update');
    }

    public function with():array{
//        dd(Goal::all());
        return [
            'allGoals' => Goal::all(),
        ];
    }

    private static function toDecimalSeconds(&$field):void{
        if(empty($field)){
            $field = null;
            return;
        }

        if(Str::contains($field, '.')){
            $t500 = CarbonInterval::createFromFormat('i:s.v', $field);
        }
        else{
            $t500 = CarbonInterval::createFromFormat('i:s', $field);
        }
        $field = $t500->total('seconds');

    }

};
?>
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Sport Attributes') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("What kind of paddler are you?") }}
        </p>
    </header>

    <x-mary-form wire:submit="updateSports" class="mt-6 space-y-4" x-data="{isCompetition: $wire.competition }">
        <x-mary-select label="What is your main purpose?" wire:model="competition" :options="$competitive" required x-model="isCompetition"></x-mary-select>
        <x-mary-input type="text" label="Club" wire:model="club"></x-mary-input>
        <x-mary-select label="Favorite discipline" wire:model="discipline_id" :options="\App\Models\Discipline::all()" required ></x-mary-select>
        <div class="grid lg:grid-cols-3 gap-3" x-show="isCompetition == 1" x-transition>
            <x-mary-input type="number" label="Weekly training sessions" wire:model="weekly_trainings" class="space-y-4 text-right"></x-mary-input>
            <x-mary-input type="text" label="Best time to 500m" wire:model="time_500" class="space-y-4 text-right" placeholder="mm:ss.ms" hint="Examples: 02:20 or 02:20.35"></x-mary-input>
            <x-mary-input type="text" label="Best time to 1000m" wire:model="time_1000" class="space-y-4 text-right" placeholder="mm:ss.ms" hint="Examples: 05:20 or 05:20.35"></x-mary-input>
        </div>
        @php /*
        <x-mary-choices label="What is your main goal" wire:model="selectedGoals" :options="$allGoals" hint="Please select the 2 most important" />
        */
        @endphp
        <div>
            <h3 class="text-md font-medium text-gray-900 dark:text-gray-100">
                {{ __('What are your main goals?') }}
            </h3>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __("Please select the 2 most important") }}
            </p>
        </div>
        <div class="grid lg:grid-cols-3 gap-3" x-data="{blockGoals: false, limitGoals() {this.blockGoals = document.querySelectorAll('.chkGoal:checked').length==2;} }" x-init="$nextTick(()=>{limitGoals()})">
            @foreach($allGoals as $goal)
            <x-mary-checkbox label="{{$goal->name}}" wire:model="goals.{{$goal->id}}" class="space-y-3 chkGoal" @change="limitGoals" x-bind:disabled="blockGoals && !$el.checked"></x-mary-checkbox>
            @endforeach
        </div>
        <div class="flex items-center gap-4 ">
            <x-mary-button label="Save" spinner="save" type="submit" class="btn-primary" />

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </x-mary-form>

</section>
