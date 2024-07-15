<?php

use Livewire\Volt\Component;

new class extends Component{
    use \Mary\Traits\Toast;

    public bool $showChooser = false;
    public bool $showResults = false;

    public array $boatChooser = [];
    public array $suggestedBoats = [];

    public array $rules = [];

    public int $heightId = 0;
    public int $weightId = 0;
    public ?int $boatChooserDiscipline;
    public array $disciplines = [
            ['id' => 1, 'name' => 'Flatwater racing'],
            ['id' => 2, 'name' => 'Fitness'],
        ];

    public function loadChooser(){

        $this->showChooser = false;

        if(empty($this->boatChooserDiscipline)){
            $this->error('Please select a discipline');
            return;
        }

        $api = new \App\Services\NeloApiClient();

        $this->boatChooser = $api->getBoatChooserQuestions($this->boatChooserDiscipline);

        $user = Auth::user();

        $this->rules = [];
        foreach ($this->boatChooser as $group){
            foreach ($group['questions'] as $question){
                if($question['required'] == 1)
                    $this->rules[$question['slug']] = 'required';

                if(is_numeric($user->height) && $question['slug'] == 'height'){
                    $this->heightId = $this->preselectAnswer($user->height, $question['answers']);
                }elseif(is_numeric($user->weight) && $question['slug'] == 'weight'){
                    $this->weightId = $this->preselectAnswer($user->weight, $question['answers']);
                }
            }
        }

       // dump($this->heightId);

        $this->showChooser = true;
    }

    private function preselectAnswer($localValue, $answers):int{
        foreach ($answers as $answer){
            $value = Str::of($answer['answer']);
            if($value->contains('<')) {
                if ($localValue < floatval($value->substr(1)->toString())) {
                   return $answer['id'];
                }
            }elseif($value->contains('>')){
                if($localValue > floatval($value->substr(1)->toString())){
                    return $answer['id'];
                }
            }
            else{
                $parts = $value->explode('-');
                if($localValue >= floatval($parts[0]) && $localValue <= floatval($parts[1])) {
                    return $answer['id'];
                }
            }
        }
        return 0;
    }

    public function chooseBoat($data){

        $validator = Validator::make($data, $this->rules);
        $validator->validate();

        $questions = collect($data)->reject(function ($value) {
            return empty($value);
        });

        $api = new \App\Services\NeloApiClient();
       // dump($questions->toArray());
        $boats = $api->chooseBoat($questions->toArray());

        //dump($boats);
        $this->suggestedBoats = [];
        if(!empty($boats)){
            foreach($boats['modelos'] as $model=>$versions){
                $product = null;
                foreach($versions as $version){
                    if($product = \App\Models\Product::firstWhere('external_id', $version['id'])){
                        break;
                    }
                }
                $this->suggestedBoats[$model] = $product;
            }
        }
        //dump($this->suggestedBoats);
        $this->showResults = true;
    }
}
?>
<div>
    @section('title') Order new boat @endsection

    <x-mary-header title="Order a boat"></x-mary-header>

    <div class="grid lg:grid-cols-2 gap-5">
        <x-mary-card title="Configure your boat" subtitle="Create your dream boat and order it">
            <p class="mb-4">The boat of your dreams can be just a few clicks away</p>
            <x-mary-button label="Configure your boat" icon="o-swatch" class="btn-info" link="https://myorder.nelo.eu" external></x-mary-button>
        </x-mary-card>
        <x-mary-card title="Choose your boat" subtitle="Find the best model for you">
            <p class="mb-4">Not sure what model fits you best? From you physical attributes, paddling style and preferences we can help you
            find which of our models will fit you better.</p>
            <x-mary-select wire:model="boatChooserDiscipline" :options="$disciplines" placeholder="Select a discipline">
                <x-slot:append>
                    <x-mary-button label="Give it a go" icon="tabler.wand" class="btn-accent rounded-s-none" wire:click="loadChooser()" spinner></x-mary-button>
                </x-slot:append>
            </x-mary-select>

            <span class="text-sm">More options will be available soon.</span>
            <div>

            </div>
        </x-mary-card>
    </div>

    @if($showChooser)
    <x-mary-card title="Fill the form to find the best boat for you" subtitle="Available only for flatwater racing boats" wire:transition class="mt-5">
        <x-mary-form class="mt-4" wire:submit="chooseBoat(Object.fromEntries(new FormData($event.target)))">
            @foreach($boatChooser as $group)
                <h3 class="text-xl mt-2">{{ $group['name'] }}</h3>
                <div  class="grid lg:grid-cols-2 gap-4">
                @foreach($group['questions'] as $question)
                    @if($question['required'])
                        @if($question['slug'] == 'height')
                            <x-mary-select name="{{ $question['slug'] }}" wire:model="heightId" :label="$question['question']" :options="$question['answers']" option-label="answer" placeholder="Choose your" required></x-mary-select>
                        @elseif($question['slug'] == 'weight')
                            <x-mary-select name="{{ $question['slug'] }}" wire:model="weightId" :label="$question['question']" :options="$question['answers']" option-label="answer" placeholder="Choose your" required></x-mary-select>
                        @else
                            <x-mary-select name="{{ $question['slug'] }}" :label="$question['question']" :options="$question['answers']" option-label="answer" placeholder="Choose your" required></x-mary-select>
                        @endif
                    @else
                    <x-mary-select name="{{ $question['slug'] }}" :label="$question['question']" :options="$question['answers']" option-label="answer" placeholder="Choose your" ></x-mary-select>
                    @endif
                @endforeach
                </div>
            @endforeach
            <x-slot:actions>
                <x-mary-button type="submit" label="Find my boat" icon="o-magnifying-glass" spinner class="btn-info"></x-mary-button>
                <x-mary-button label="Close" icon="o-x-mark" wire:click="showChooser = false"></x-mary-button>
            </x-slot:actions>
        </x-mary-form>
    </x-mary-card>
    @endif

    <x-mary-modal title="Your results" right box-class="w-2/3 max-w-5xl bg-base-200" wire:model="showResults">
        <div class="grid lg:grid-cols-3 gap-3">
        @foreach($suggestedBoats as $boat)
            @isset($boat)
            <x-mary-card :title="$boat->nameWithoutLayup()">
                <x-slot:figure>
                    <img src="{{ $boat->image }}" alt="{{$boat->nameWithoutLayup()}}">
                </x-slot:figure>
                <x-slot:actions>
                    @if(!Str::contains($boat->name, 'Viper', true))
                        <x-mary-button label="Configure yours" link="{{ config('nelo.myorder.base_url') }}?product={{ $boat->nameWithoutLayup() }}" external></x-mary-button>
                    @endif
                </x-slot:actions>
            </x-mary-card>
                @endisset
        @endforeach
        </div>
        <x-slot:actions>
            <x-mary-button label="Close" icon="o-x-mark" wire:click="showResults = false"></x-mary-button>
        </x-slot:actions>
    </x-mary-modal>
</div>
