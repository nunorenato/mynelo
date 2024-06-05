<?php

use App\Enums\ProductTypeEnum;
use App\Enums\FieldEnum;
use App\Models\BoatRegistration;
use App\Models\Product;
use App\Models\Boat;
use App\Models\Discipline;
use App\Models\Attribute;
use App\Models\Content;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;
use Mary\Traits\Toast;
use Livewire\WithFileUploads;
use Mary\Traits\WithMediaSync;


new class extends Component{
    use Toast, WithFileUploads, WithMediaSync;

    public BoatRegistration $boatRegistration;

    public bool $showSetup = false;
    public bool $deleteModal = false;
    public bool $showUpgrades = false;
    public bool $showContent = false;
    public bool $showRepair = false;

    public Boat $boat;
    public Product $model;
    public Discipline $discipline;

    public Illuminate\Database\Eloquent\Collection $upgradables;

    public ?int $seat_id;
    public ?string $seat_position;
    public ?string $seat_height;
    public ?int $footrest_id;
    public ?string $footrest_position;
    public ?int $rudder_id;
    public ?string $paddle;
    public ?string $paddle_length;
    public ?string $voucher;

    public ?Content $layup;
    public ?Content $selectedContent;

    public bool $notComplete = true;

    public string $repairDescription;
    #[Rule(['$repairImages.*' => 'image'])]
    public array $repairImages;
    public \Illuminate\Support\Collection $repairLibrary;

    public array $rules = [
        'seat_id' => ['numeric', 'sometimes','nullable'],
        'seat_position' => ['numeric', 'sometimes','nullable'],
        'seat_height' => ['numeric', 'sometimes','nullable'],
        'footrest_id' => ['numeric', 'sometimes','nullable'],
        'footrest_position' => ['numeric', 'sometimes','nullable'],
        'rudder_id' => ['numeric', 'sometimes','nullable'],
        'paddle' => ['string', 'sometimes','nullable'],
        'paddle_length' => ['string', 'sometimes','nullable'],
    ];

    public function mount():void
    {
        Gate::authorize('view', $this->boatRegistration);

        $this->boat = $this->boatRegistration->boat;
        $this->model = $this->boat->product;
        $this->discipline = $this->model->discipline;

        $this->notComplete = $this->boatRegistration->status != \App\Enums\StatusEnum::COMPLETE;

        $this->seat_id = $this->boatRegistration->seat_id;
        $this->seat_position = $this->boatRegistration->seat_position??10;
        $this->seat_height = $this->boatRegistration->seat_height??0;
        $this->footrest_id = $this->boatRegistration->footrest_id;
        $this->footrest_position = $this->boatRegistration->footrest_position??10;
        $this->rudder_id = $this->boatRegistration->rudder_id;
        $this->paddle = $this->boatRegistration->paddle;
        $this->paddle_length = $this->boatRegistration->paddle_length;
        $this->voucher = $this->boatRegistration->voucher;

        if(!empty($this->discipline)){
            foreach ($this->discipline->fields as $field){
                if($field->required){
                    $this->rules[$field->column] = array_filter($this->rules[$field->column], function($element){
                        return $element != 'nullable';
                    });
                    $this->rules[$field->column][] = 'required';
                }
            }
        }

        $pos = strripos($this->boat->model, ' ');
        $layupKey = trim(strtolower(substr($this->boat->model, $pos+1)));
        $this->layup = Content::where('path', "layups/$layupKey")->first();

        $this->repairLibrary = new \Illuminate\Support\Collection();
    }

    public function saveSetup():void
    {

        $validated = $this->validate();

        $this->boatRegistration->fill($validated);
        $this->boatRegistration->status = \App\Enums\StatusEnum::COMPLETE;
        $this->boatRegistration->save();

        activity()
            ->on($this->boatRegistration)
            ->by(Auth::user())
            ->event('updated')
            ->withProperties($validated)
            ->log('Boat registration My Setup update');

        $this->showSetup = false;
        $this->notComplete = false;
    }

    public function saveVoucher():void{
        $this->boatRegistration->voucher = $this->voucher;
    }

    public function sendRepair()
    {
        $validated = $this->validate([
            'repairDescription' => ['required'],
            'repairImages.*' => ['image'],
        ]);

        $attachs = [];
        foreach($validated['repairImages'] as $image){
            $attachs[] = $image->getPathname();
        }
        //dump($attachs);

        Mail::to(config('nelo.emails.admins'))
            ->send(new \App\Mail\RepairMail(Auth::user(), $this->boat, $validated['repairDescription'], $attachs));

        activity()
            ->on($this->boat)
            ->by(Auth::user())
            ->event('request')
            ->log('Sent repair request');

        $this->success('Message sent successfully');

        $this->showRepair = false;
    }

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

    public function with():array{

        $products = $this->boatRegistration->boat->products()
            ->get()
            ->transform(function(Product $item, $key){
            //dump($item->pivot);
                if(is_numeric($item->pivot->attribute_id)){
                    $item->attribute = Attribute::find($item->pivot->attribute_id);
                }
                return $item;
        });

        /**
         * sacar o nome do modelo
         */
        $parts = explode(' ', $this->boat->model);
        // retirar construcao
        array_pop($parts);
        // retirar tamanho
        // TODO validar se é um tamanho
        array_pop($parts);

        $boatMedia = [];
        foreach($this->boat->getMedia('*') as $media){
            $boatMedia[] = $media->getUrl();
        }
        foreach($this->boat->product->getMedia('*') as $media){
            $boatMedia[] = $media->getUrl();
        }

        $details = [
            ['name' => $this->boat->external_id, 'sub-value' => 'Boat ID','icon' => 'o-finger-print'],
            ['name' => $this->boat->model, 'sub-value' => 'Boat model', 'icon' => 'o-cube-transparent'],
        ];

        if(!empty($this->boat->finished_at))
            $details[] = ['name' => substr($this->boat->finished_at, 0, 10), 'sub-value' => 'Finished date', 'icon' => 'o-calendar'];
        if(is_numeric($this->boat->finished_weight))
            $details[] = ['name' => $this->boat->finished_weight, 'sub-value' => 'Final weight (kg)', 'icon' => 'o-scale', 'class' => $this->notComplete?'blur-sm':null];
        if(is_numeric($this->boat->co2))
            $details[] = ['name' => $this->boat->co2, 'sub-value' => 'Carbon footprint (kg co2 eq.)', 'icon' => 'carbon.carbon-accounting', 'class' => $this->notComplete?'blur-sm':null];
        $marketValue = $this->boat->marketValue();
        if(is_numeric($marketValue))
            $details[] = ['name' => '€'.$marketValue, 'sub-value' => 'Market value', 'icon' => 'o-banknotes', 'class' => $this->notComplete?'blur-sm':null];

        return [
            'details' => $details,
            'fittings' => $products->where('product_type_id', '<>', ProductTypeEnum::Color->value),
            'colors' => $products->where('product_type_id', '=', ProductTypeEnum::Color->value),
            'setupFields' => empty($this->discipline)?[]:$this->discipline->fields,
            'aboutModel' => Content::where('path', 'model/about/'.strtolower(implode('_', $parts)))->first(),
            'boatMedia' => $boatMedia,
            'voucherQueryString' => Arr::query([
                'mynelo' => 1,
                'voucher' => $this->boatRegistration->voucher,
                'boat-id' => $this->boat->external_id,
                'email' => $this->boatRegistration->user->email,
                'owner' => $this->boatRegistration->user->name,
            ]),
        ];
    }

    public function loadUpgrades($id){

        $this->upgradables = $this->model->options()->wherePivot('attribute_id', $id)->get();

        //dump($this->upgradables);

        $this->showUpgrades = true;
    }

    public function loadContent(Content $content){
        $this->showContent = true;
        $this->selectedContent = $content;
    }
}
?>
<div>
@section('title'){{ $boatRegistration->boat->model }}@endsection

@push('head')
    {{-- PhotoSwipe --}}
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe-lightbox.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/photoswipe.min.css" rel="stylesheet">
@endpush

    <x-mary-header title="{{$boatRegistration->boat->model}}">
        <x-slot:actions>
            <x-mary-button label="Remove boat" icon="o-trash" class="btn-error" wire:click="$dispatch('showDelete')" spinner></x-mary-button>
        </x-slot:actions>
    </x-mary-header>

    @if(count($boatMedia) > 0)
    <x-mary-image-gallery :images="$boatMedia" class="h-40 rounded-box mb-5"></x-mary-image-gallery>
    @endif

    <div class="mb-5 gap-5 {{ $notComplete?'blur-sm':'' }}">
        <x-mary-button label="Boat care" icon="tabler.shield-heart" class="lg:btn-lg btn-secondary w-full mb-2 lg:w-auto lg:mb-0" wire:click="loadContent({{ Content::findByPath('boatcare')->id }})" spinner></x-mary-button>
        <x-mary-button label="Move this boat" icon="o-truck" link="https://moveyourboat.paddle-lab.com" class="lg:btn-lg btn-secondary w-full mb-2 lg:w-auto lg:mb-0" external></x-mary-button>
        <x-mary-button label="Repair" icon="o-wrench-screwdriver" class="lg:btn-lg btn-secondary w-full mb-2 lg:w-auto lg:mb-0" @click="$wire.showRepair = true" spinner></x-mary-button>
        <x-mary-button label="Sell" icon="o-currency-euro" class="lg:btn-lg btn-neutral w-full mb-0 lg:w-auto" wire:click="loadContent({{ Content::findByPath('sell')->id }})" spinner></x-mary-button>
    </div>

    <div class="grid lg:grid-cols-3 gap-5">
        <!-- BOAT DETAILS -->
        <x-mary-card title="Boat details">
            @foreach($details as $detail)
                <x-mary-list-item :item="$detail" sub-value="sub-value" class="{{ $detail['class']??'' }}">
                    <x-slot:avatar>
                        <x-mary-icon :name="$detail['icon']" class="h-10" />
                    </x-slot:avatar>
                    @if($detail['sub-value'] == 'Boat model' && isset($aboutModel))
                        <x-slot:actions>
                            <x-mary-button icon="o-information-circle" class="btn-circle btn-sm" wire:click="loadContent({{ $aboutModel->id }})" spinner></x-mary-button>
                        </x-slot:actions>
                    @endif
                    @if($detail['sub-value'] == 'Market value')
                        <x-slot:actions>
                            <x-mary-button icon="o-information-circle" class="btn-circle btn-sm" wire:click="loadContent({{ Content::findByPath('marketvalue')->id }})" spinner></x-mary-button>
                        </x-slot:actions>
                    @endif
                </x-mary-list-item>
            @endforeach
        </x-mary-card>

        <!-- MY SETUP -->
        <x-mary-card class="lg:col-span-2" title="My setup">
            @if($boatRegistration->status == \App\Enums\StatusEnum::VALIDATED)
                <div class="align-middle self-center space-y-3">
                    @if(empty($boat->finished_at))
                        <p>Your boat is yet not finished. As soon as that happens, you will have access to all the details and features.</p>
                        @if(!$boat->voucher_used)
                            <p>By pre-registering with your voucher code you can have access to some extra goodies.</p>
                            @if(empty($boatRegistration->voucher))
                                <p>You will need a voucher code to complete this operation. Please request one to your dealer.</p>
                                <x-mary-form wire:submit="saveVoucher" class="lg:w-1/3">
                                    <x-mary-input label="Voucher code" wire:model="voucher" inline>
                                        <x-slot:append>
                                            <x-mary-button label="Send" type="submit" spinner icon="o-paper-airplane"></x-mary-button>
                                        </x-slot:append>
                                    </x-mary-input>
                                </x-mary-form>
                            @else
                                <x-mary-button label="Get my goodies" icon="o-sparkles" link="https://www.nelo.eu/pre-registration-options/?{{ $voucherQueryString }}" external></x-mary-button>
                            @endif
                        @endif
                    @else
                        <h2 class="mb-3">Please complete your boat registration</h2>
                        <x-mary-button label="Finish registration" icon="o-adjustments-horizontal" @click="$wire.showSetup = true"></x-mary-button>
                    @endif
                </div>
            @else
                <x-slot:menu>
                    <x-mary-button icon="o-pencil-square" class="btn-circle btn-sm" wire:click="$toggle('showSetup')"></x-mary-button>
                </x-slot:menu>
                @isset($discipline)
                    <div class="lg:grid lg:grid-cols-2 lg:gap-3">
                    @foreach($discipline->fields as $field)
                        @switch(FieldEnum::from($field->id))
                            @case(FieldEnum::Seat)
                                @isset($boatRegistration->seat)
                                    <x-mary-list-item :item="$boatRegistration->seat">
                                        <x-slot:sub-value>Seat</x-slot:sub-value>
                                        <x-slot:avatar><x-mary-avatar :image="$boatRegistration->seat->image" class="!w-11"></x-mary-avatar></x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset
                                @break

                            @case(FieldEnum::SeatPosition)
                                @isset($boatRegistration->seat_position)
                                    <x-mary-list-item :item="$boatRegistration" value="seat_position">
                                        <x-slot:sub-value>Seat Position</x-slot:sub-value>
                                        @php /* <x-slot:avatar><x-mary-avatar image="/images/SeatPosition.svg" class="!w-11"></x-mary-avatar></x-slot:avatar> */ @endphp
                                        <x-slot:avatar><x-mary-icon name="tabler.arrow-autofit-width" class="!w-11"></x-mary-icon></x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::SeatHeight)
                                @isset($boatRegistration->seat_height)
                                    <x-mary-list-item :item="$boatRegistration" value="seat_height">
                                        <x-slot:sub-value>Seat Height</x-slot:sub-value>
                                        @php /* <x-slot:avatar><x-mary-avatar image="/images/SeatHeight.svg" class="!w-11"></x-mary-avatar></x-slot:avatar>*/ @endphp
                                        <x-slot:avatar><x-mary-icon name="tabler.arrow-autofit-height" class="!w-11"></x-mary-icon></x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::Footrest)
                                @isset($boatRegistration->footrest)
                                    <x-mary-list-item :item="$boatRegistration->footrest">
                                        <x-slot:sub-value>Rudder</x-slot:sub-value>
                                        <x-slot:avatar><x-mary-avatar :image="$boatRegistration->footrest->image" class="!w-11"></x-mary-avatar></x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::FootrestPosition)
                                @isset($boatRegistration->footrest_position)
                                    <x-mary-list-item :item="$boatRegistration" value="footrest_position">
                                        <x-slot:sub-value>Footrest Position</x-slot:sub-value>
                                        @php /*<x-slot:avatar><x-mary-avatar image="/images/FootRestPosition.svg" class="!w-11"></x-mary-avatar></x-slot:avatar>*/ @endphp
                                        <x-slot:avatar><x-mary-icon name="tabler.arrow-bar-both" class="!w-11"></x-mary-icon></x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::Rudder)

                                @isset($boatRegistration->rudder)
                                    <x-mary-list-item :item="$boatRegistration->rudder">
                                        <x-slot:sub-value>Rudder</x-slot:sub-value>
                                        <x-slot:avatar><x-mary-avatar :image="$boatRegistration->rudder->image" class="!w-11"></x-mary-avatar></x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::Paddle)

                                @isset($boatRegistration->paddle)
                                    <x-mary-list-item :item="$boatRegistration" value="paddle">
                                        <x-slot:sub-value>Paddle</x-slot:sub-value>
                                        <x-slot:avatar>
                                            <x-mary-icon name="mdi.oar" class="!w-11"></x-mary-icon>
                                        </x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                            @case(FieldEnum::PaddleLength)

                                @isset($boatRegistration->paddle_length)
                                    <x-mary-list-item :item="$boatRegistration" value="paddle_length">
                                        <x-slot:sub-value>Paddle Length</x-slot:sub-value>
                                        <x-slot:avatar>
                                            <x-mary-icon name="carbon.ruler-alt" class="!w-11"></x-mary-icon>
                                        </x-slot:avatar>
                                    </x-mary-list-item>
                                @endisset

                                @break

                        @endswitch
                    @endforeach
                    </div>
                @endisset
            @endif
        </x-mary-card>

        <!-- FITTINGS -->
        <x-mary-card title="Fittings" @class(['blur-sm' => $notComplete])>
            @foreach($fittings as $product)
                <x-mary-list-item :item="$product" sub-value="attribute.name">
                    <x-slot:avatar><x-mary-avatar :image="$product->image" class="!w-11"></x-mary-avatar></x-slot:avatar>
                    <x-slot:actions>
                        @isset($product->attribute)
                        <x-mary-button label="Upgrade" wire:click="loadUpgrades({{ $product->attribute->id }})" spinner></x-mary-button>
                        @endisset
                    </x-slot:actions>
                </x-mary-list-item>
            @endforeach
            @isset($boatRegistration->boat->assembler)
                <div class="mt-10">
                    <x-mary-avatar :title="$boatRegistration->boat->assembler->name" subtitle="Assembly" :image="$boatRegistration->boat->assembler->photo" class="!w-14"></x-mary-avatar>
                </div>
            @endisset
            @isset($boatRegistration->boat->evaluator)
                <div class="mt-10">
                    <x-mary-avatar :title="$boatRegistration->boat->evaluator->name" subtitle="Quality control" :image="$boatRegistration->boat->evaluator->photo" class="!w-14"></x-mary-avatar>
                </div>
            @endisset
        </x-mary-card>

        <!-- DESIGN -->
        <x-mary-card title="Design" @class(['blur-sm' => $notComplete])>
            @foreach($colors as $product)
                <x-mary-list-item :item="$product" sub-value="attribute.name">
                    <x-slot:avatar><div style="background-color: {{ $product->attributes['hex']??'#ffffff' }};" class="w-11 h-11 rounded-full"></div></x-slot:avatar>
                </x-mary-list-item>
            @endforeach
                @if(!empty($boatRegistration->boat->painter))
                    <div class="mt-10">
                        <x-mary-avatar :title="$boatRegistration->boat->painter->name" subtitle="Painter" :image="$boatRegistration->boat->painter->photo" class="!w-14"></x-mary-avatar>
                    </div>
                @endif
        </x-mary-card>

        <!-- LAYUP -->
        <x-mary-card title="Layup" @class(['blur-sm' => $notComplete])>
            @isset($layup)
                <x-basic-content :content="$layup"></x-basic-content>
            @endisset
            @if(!empty($boatRegistration->boat->layuper))
                <div class="mt-10">
                    <x-mary-avatar :title="$boatRegistration->boat->layuper->name" subtitle="Layup" :image="$boatRegistration->boat->layuper->photo" class="!w-14"></x-mary-avatar>
                </div>
            @endif
        </x-mary-card>
    </div>

    <x-mary-drawer wire:model="showSetup" right class="w-11/12 lg:w-1/3">
        <x-mary-form wire:submit="saveSetup">
            @foreach($setupFields as $field)
                @switch(FieldEnum::from($field->id))
                    @case(FieldEnum::Seat)
                        <x-mary-select label="Seat" wire:model="seat_id" :options="$model->options()->where('product_type_id', ProductTypeEnum::Seat)->get()" placeholder="---"></x-mary-select>
                        @break

                    @case(FieldEnum::SeatPosition)
                        <x-mary-range label="Seat Position" wire:model="seat_position" min="0" max="20"></x-mary-range>
                        <x-mary-badge x-text="$wire.seat_position">{{$seat_position}}</x-mary-badge>
                        @break

                    @case(FieldEnum::SeatHeight)
                        <x-mary-input label="Seat Height" wire:model="seat_height"></x-mary-input>
                        @break

                    @case(FieldEnum::Footrest)
                        <x-mary-select label="Footrest" wire:model="footrest_id" :options="$model->options()->where('product_type_id', ProductTypeEnum::Footrest)->get()" placeholder="---"></x-mary-select>
                        @break

                    @case(FieldEnum::FootrestPosition)
                        <x-mary-range label="Footrest Position" wire:model="footrest_position" min="0" max="20"></x-mary-range>
                        <x-mary-badge x-text="$wire.footrest_position">{{$footrest_position}}</x-mary-badge>
                        @break

                    @case(FieldEnum::Rudder)
                        <x-mary-select label="Rudder" wire:model="rudder_id" :options="$model->options()->where('product_type_id', ProductTypeEnum::Rudder)->get()" placeholder="---"></x-mary-select>
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

    <x-mary-drawer wire:model="showUpgrades" title="Available upgrades" right class="w-11/12 lg:w-1/3">
        @isset($upgradables)
            @foreach($upgradables as $option)
                <x-mary-list-item :item="$option">
                    <x-slot:subValue>{!! $option->description !!}</x-slot:subValue>
                    <x-slot:avatar><x-mary-avatar :image="$option->image" class="!w-11"></x-mary-avatar></x-slot:avatar>
                    @isset($option->attributes['magento_url'])
                    <x-slot:actions>
                        <x-mary-button label="Buy" :link="config('nelo.shop.base_product_url').$option->attributes['magento_url'].'.html'" external></x-mary-button>
                    </x-slot:actions>
                    @endisset
                </x-mary-list-item>
            @endforeach
        @endisset
        <x-mary-menu-separator></x-mary-menu-separator>
            <x-mary-button icon="o-beaker" link="https://paddle-lab.com/paddles-accessories/https-paddle-lab-com-paddles-accessories-nelo-training-gadgets-html.html" external>Discover our training gadgets</x-mary-button>
            <x-slot:actions>
                <x-mary-button label="Close" @click="$wire.showUpgrades = false"></x-mary-button>
            </x-slot:actions>
    </x-mary-drawer>

    <livewire:boats.delete :boat_registration="$boatRegistration"></livewire:boats.delete>

    <x-mary-drawer wire:model="showContent" :title="$selectedContent->title??''" right class="w-11/12 lg:w-1/2">
        @isset($selectedContent)
            <x-basic-content :content="$selectedContent" no-title></x-basic-content>
        @endisset
        <x-slot:actions>
            <x-mary-button label="Close" @click="$wire.showContent = false"></x-mary-button>
        </x-slot:actions>
    </x-mary-drawer>

    <x-mary-modal title="Repair service" subtitle="Fill the form bellow so our team can analyze it and send you a quote." wire:model="showRepair">
        @push('head')
            {{-- Cropper.js --}}
            <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

            {{-- Sortable.js --}}
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.1/Sortable.min.js"></script>
        @endpush
        <x-mary-form wire:submit="sendRepair">
            <x-mary-textarea label="Describe the issues with your boat" rows="5" wire:model="repairDescription" required></x-mary-textarea>
            <x-mary-image-library label="Photos of the issues" wire:model="repairImages" wire:library="repairLibrary" :preview="$repairLibrary" required></x-mary-image-library>
            <x-slot:actions>
                <x-mary-button label="Send" icon="o-paper-airplane" class="btn-primary" type="submit"></x-mary-button>
                <x-mary-button label="Close" @click="$wire.showRepair = false"></x-mary-button>
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</div>
