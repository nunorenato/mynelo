<?php

use App\Enums\ProductTypeEnum;
use App\Enums\FieldEnum;
use App\Models\BoatRegistration;
use App\Models\Product;
use App\Models\Boat;
use App\Models\Discipline;
use App\Models\Attribute;
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new class extends Component{
    use Toast;

    public BoatRegistration $boatRegistration;

    public bool $showSetup = false;
    public bool $deleteModal = false;

    public Boat $boat;
    public Product $model;
    public Discipline $discipline;

    public ?int $seat_id;
    public ?int $seat_position;
    public ?int $seat_height;
    public ?int $footrest_id;
    public ?int $footrest_position;
    public ?int $rudder_id;
    public ?string $paddle;
    public ?string $paddle_length;

    public bool $notComplete = true;

    public array $rules = [
        'seat_id' => ['numeric', 'nullable'],
        'seat_position' => ['numeric', 'nullable'],
        'seat_height' => ['numeric', 'nullable'],
        'footrest_id' => ['numeric', 'nullable'],
        'footrest_position' => ['numeric', 'nullable'],
        'rudder_id' => ['numeric', 'nullable'],
        'paddle' => ['string', 'nullable'],
        'paddle_length' => ['string', 'nullable'],
    ];

    public function mount():void
    {
        Gate::authorize('view', $this->boatRegistration);

        $this->boat = $this->boatRegistration->boat;
        $this->model = $this->boat->product;
        $this->discipline = $this->model->discipline;

        $this->notComplete = $this->boatRegistration->status != \App\Enums\StatusEnum::COMPLETE;

        $this->seat_id = $this->boatRegistration->seat_id;
        $this->seat_position = $this->boatRegistration->seat_position;
        $this->seat_height = $this->boatRegistration->seat_height;
        $this->footrest_id = $this->boatRegistration->footrest_id;
        $this->footrest_position = $this->boatRegistration->footrest_position;
        $this->rudder_id = $this->boatRegistration->rudder_id;
        $this->paddle = $this->boatRegistration->paddle;
        $this->paddle_length = $this->boatRegistration->paddle_length;

        if(!empty($this->discipline)){
            foreach ($this->discipline->fields as $field){
                if($field->required){
                    $this->rules[$field->columnn][] = 'required';
                }
            }
        }
    }

    public function saveSetup():void
    {

        $validated = $this->validate();
        //dump($validated);
        $this->boatRegistration->fill($validated);
        $this->boatRegistration->status = \App\Enums\StatusEnum::COMPLETE;
        $this->boatRegistration->save();

        $this->showSetup = false;
        $this->notComplete = false;
    }

    public function removeBoat():void
    {

        $this->boatRegistration->delete();

        $this->deleteModal = false;
        $this->success(
            title: 'Boat removed',
            redirectTo: route('boats')
        );
    }

    public function with():array{

        $products = $this->boatRegistration->boat->products()
            ->with('image')
            ->get()
            ->transform(function(Product $item, $key){
            //dump($item->pivot);
                if(is_numeric($item->pivot->attribute_id)){
                    $item->attribute = Attribute::find($item->pivot->attribute_id);
                }
                return $item;
        });

        //dd($this->boatRegistration->boat);

        return [
            'details' => [
                ['name' => $this->boat->external_id, 'sub-value' => 'Boat ID','icon' => 'o-finger-print'],
                ['name' => $this->boat->model, 'sub-value' => 'Boat model', 'icon' => 'o-cube-transparent'],
                ['name' => substr($this->boat->finished_at, 0, 10), 'sub-value' => 'Finished date', 'icon' => 'o-calendar'],
                ['name' => $this->boat->finished_weight, 'sub-value' => 'Final weight (kg)', 'icon' => 'o-scale'],
                ['name' => $this->boat->co2, 'sub-value' => 'Carbon footprint (kg co2 eq.)', 'icon' => 'carbon.carbon-accounting'],
            ],
            'fittings' => $products->where('product_type_id', '<>', ProductTypeEnum::Color->value),
            'colors' => $products->where('product_type_id', '=', ProductTypeEnum::Color->value),
            'setupFields' => empty($this->discipline)?[]:$this->discipline->fields,
        ];
    }
}
?>
<div>
@push('head')
    {{-- PhotoSwipe --}}
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe-lightbox.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/photoswipe.min.css" rel="stylesheet">
@endpush

    <x-mary-header title="{{$boatRegistration->boat->model}}">
        <x-slot:actions>
            <x-mary-button label="Remove boat" icon="o-trash" class="btn-error" @click="$wire.deleteModal = true"></x-mary-button>
        </x-slot:actions>
    </x-mary-header>

    <x-mary-image-gallery :images="$boatRegistration->boat->images->pluck('path')->toArray()" class="h-40 rounded-box mb-5"></x-mary-image-gallery>
    <div class="grid lg:grid-cols-3 gap-5">
        <!-- BOAT DETAILS -->
        <x-mary-card title="Boat details" @class(['blur-sm' => $notComplete])>
            @foreach($details as $detail)
                <x-mary-list-item :item="$detail" sub-value="sub-value">
                    <x-slot:avatar>
                        <x-mary-icon :name="$detail['icon']" class="h-10" />
                    </x-slot:avatar>
                </x-mary-list-item>
            @endforeach
        </x-mary-card>

        <!-- MY SETUP -->
        <x-mary-card class="col-span-2" title="My setup">
            @if($boatRegistration->status == \App\Enums\StatusEnum::VALIDATED)
                <div class="align-middle items-center">
                    <h2>Please complete your boat registration</h2>
                    <x-mary-button label="Finish registration" @click="$wire.showSetup = true"></x-mary-button>
                </div>
            @else
                <x-slot:menu>
                    <x-mary-button icon="o-pencil-square" class="btn-circle btn-sm" wire:click="$toggle('showSetup')"></x-mary-button>
                </x-slot:menu>
                @isset($discipline)
                    @foreach($discipline->fields as $field)
                        @switch(FieldEnum::from($field->id))
                            @case(FieldEnum::Seat)
                                @isset($boatRegistration->seat)
                                    <x-mary-list-item :item="$boatRegistration->seat" avatar="image.path">
                                        <x-slot:sub-value>Seat</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset
                                @break

                            @case(FieldEnum::SeatPosition)
                                @isset($boatRegistration->seat_position)
                                    <x-mary-list-item :item="$boatRegistration" value="seat_position">
                                        <x-slot:sub-value>Seat Position</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::SeatHeight)
                                @isset($boatRegistration->seat_height)
                                    <x-mary-list-item :item="$boatRegistration" value="seat_height">
                                        <x-slot:sub-value>Seat Height</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::Footrest)
                                @isset($boatRegistration->footrest)
                                    <x-mary-list-item :item="$boatRegistration->footrest" avatar="image.path">
                                        <x-slot:sub-value>Rudder</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::FootrestPosition)
                                @isset($boatRegistration->footrest_position)
                                    <x-mary-list-item :item="$boatRegistration" value="footrest_position">
                                        <x-slot:sub-value>Footrest Position</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::Rudder)

                                @isset($boatRegistration->rudder)
                                    <x-mary-list-item :item="$boatRegistration->rudder" avatar="image.path">
                                        <x-slot:sub-value>Rudder</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::Paddle)

                                @isset($boatRegistration->paddle)
                                    <x-mary-list-item :item="$boatRegistration" value="paddle">
                                        <x-slot:sub-value>Paddle</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::PaddleLength)

                                @isset($boatRegistration->paddle_length)
                                    <x-mary-list-item :item="$boatRegistration" value="paddle_length">
                                        <x-slot:sub-value>Paddle Length</x-slot:sub-value>
                                    </x-mary-list-item>
                                @endisset

                                @break

                        @endswitch
                    @endforeach
                @endisset
            @endif
        </x-mary-card>

        <!-- FITTINGS -->
        <x-mary-card title="Fittings" @class(['blur-sm' => $notComplete])>
            @foreach($fittings as $product)
                <x-mary-list-item :item="$product" sub-value="attribute.name" avatar="image.path">
                    <x-slot:actions>
                        <x-mary-button label="Upgrade"></x-mary-button>
                    </x-slot:actions>
                </x-mary-list-item>
            @endforeach
            @if(!empty($boatRegistration->boat->evaluator))
                <div class="mt-10">
                    <x-mary-avatar :title="$boatRegistration->boat->evaluator->name" subtitle="Quality control" :image="$boatRegistration->boat->evaluator->image->path" class="!w-14"></x-mary-avatar>
                </div>
            @endif
        </x-mary-card>

        <!-- DESIGN -->
        <x-mary-card title="Design" @class(['blur-sm' => $notComplete])>
            @foreach($colors as $product)
                <x-mary-list-item :item="$product" sub-value="attribute.name" avatar="image.path"></x-mary-list-item>
            @endforeach
                @if(!empty($boatRegistration->boat->painter))
                    <div class="mt-10">
                        <x-mary-avatar :title="$boatRegistration->boat->painter->name" subtitle="Painter" :image="$boatRegistration->boat->painter->image->path" class="!w-14"></x-mary-avatar>
                    </div>
                @endif
        </x-mary-card>

        <!-- LAYUP -->
        <x-mary-card title="Layup" @class(['blur-sm' => $notComplete])>
            <p>Descrição da construção (WIP)</p>
            @if(!empty($boatRegistration->boat->layuper))
                <div class="mt-10">
                    <x-mary-avatar :title="$boatRegistration->boat->layuper->name" subtitle="Layup" :image="$boatRegistration->boat->layuper->image->path" class="!w-14"></x-mary-avatar>
                </div>
            @endif
        </x-mary-card>
    </div>
    @php /*
    <x-mary-tabs selected="details-tab">
        <x-mary-tab name="details-tab" label="Boat details" icon="o-information-circle">
            <div></div>
        </x-mary-tab>
        <x-mary-tab name="setup-tab" label="My setup" icon="o-cog-6-tooth">
            <div>
            </div>
        </x-mary-tab>
        <x-mary-tab name="fittings-tab" label="Fittings" icon="tabler.artboard">
            <div>
            </div>
        </x-mary-tab>
        <x-mary-tab name="design-tab" label="Design" icon="tabler.spray">
            <div>

            </div>
        </x-mary-tab>
        <x-mary-tab name="layup-tab" label="Layup" icon="carbon.carbon">
            <div>
            </div>
        </x-mary-tab>
    </x-mary-tabs>
    */ @endphp
    <x-mary-drawer wire:model="showSetup" right class="w-11/12 lg:w-1/3">
        <x-mary-form wire:submit="saveSetup">
            @foreach($setupFields as $field)
                @switch(FieldEnum::from($field->id))
                    @case(FieldEnum::Seat)
                        <x-mary-select label="Seat" wire:model="seat_id" :options="Product::where('product_type_id', ProductTypeEnum::Seat)->get()" placeholder="---"></x-mary-select>
                        @break

                    @case(FieldEnum::SeatPosition)
                        <x-mary-range label="Seat Position" wire:model="seat_position" min="0" max="20"></x-mary-range>
                        <x-mary-badge x-text="$wire.seat_position">{{$seat_position}}</x-mary-badge>
                        @break

                    @case(FieldEnum::SeatHeight)
                        <x-mary-input label="Seat Height" wire:model="seat_height"></x-mary-input>
                        @break

                    @case(FieldEnum::Footrest)
                        <x-mary-select label="Footrest" wire:model="footrest_id" :options="Product::where('product_type_id', ProductTypeEnum::Footrest)->get()" placeholder="---"></x-mary-select>
                        @break

                    @case(FieldEnum::FootrestPosition)
                        <x-mary-range label="Footrest Position" wire:model="footrest_position" min="0" max="20"></x-mary-range>
                        <x-mary-badge x-text="$wire.footrest_position">{{$footrest_position}}</x-mary-badge>
                        @break

                    @case(FieldEnum::Rudder)
                        <x-mary-select label="Rudder" wire:model="rudder_id" :options="Product::where('product_type_id', ProductTypeEnum::Rudder)->get()" placeholder="---"></x-mary-select>
                        @break

                    @case(FieldEnum::Paddle)
                        <x-mary-input label="Paddle" wire:model="paddle"></x-mary-input>
                        @break

                    @case(FieldEnum::PaddleLength)
                        <x-mary-input label="Paddle Length" wire:model="paddle_length"></x-mary-input>
                        @break

                @endswitch
            @endforeach
            <x-slot:actions>
                <x-mary-button label="Save" class="btn-primary" type="submit" spinner="save"></x-mary-button>
                <x-mary-button label="Cancel" @click="$wire.showSetup = false"></x-mary-button>
            </x-slot:actions>
        </x-mary-form>
    </x-mary-drawer>

    <x-mary-modal wire:model="deleteModal" title="Confirm removal">
        <div>Are you sure you wish to delete this boat from your list?</div>
        <x-slot:actions>
            <x-mary-button label="Delete" wire:click="removeBoat" class="btn-error" spinner></x-mary-button>
            <x-mary-button label="Cancel" @click="$wire.deleteModal = false"></x-mary-button>
        </x-slot:actions>
    </x-mary-modal>
</div>
