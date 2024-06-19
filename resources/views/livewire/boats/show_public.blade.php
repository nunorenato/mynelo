<?php

use App\Enums\ProductTypeEnum;
use App\Enums\FieldEnum;
use App\Models\Product;
use App\Models\Boat;
use App\Models\Discipline;
use App\Models\Attribute;
use App\Models\Content;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;
use Mary\Traits\WithMediaSync;
use Livewire\Attributes\Layout;

new #[Layout('layouts.public')] class extends Component{
    use WithMediaSync;

    public bool $showContent = false;

    public int $boat_id;

    public Boat $boat;
    public Product $model;
    public Discipline $discipline;

    public ?Content $layup;
    public ?Content $selectedContent;

    public bool $notComplete = true;

    public function boot():void{
        $this->boat = Boat::getWithSync($this->boat_id);
    }

    public function mount():void
    {

        if(!$this->boat->synced)
            return;

        $this->model = $this->boat->product;
        $this->discipline = $this->model->discipline;

        $pos = strripos($this->boat->model, ' ');
        $layupKey = trim(strtolower(substr($this->boat->model, $pos+1)));
        $this->layup = Content::where('path', "layups/$layupKey")->first();

        $this->repairLibrary = new \Illuminate\Support\Collection();
    }


    public function with():array{

        /**
         * sacar o nome do modelo
         */
        $parts = explode(' ', $this->boat->model);
        // retirar construcao
        array_pop($parts);
        // retirar tamanho
        // TODO validar se é um tamanho
        array_pop($parts);

        $details = [
            ['name' => $this->boat->external_id, 'sub-value' => 'Boat ID','icon' => 'o-finger-print'],
            ['name' => $this->boat->model, 'sub-value' => 'Boat model', 'icon' => 'o-cube-transparent'],
        ];
        if(!empty($this->boat->finished_at))
            $details[] = ['name' => substr($this->boat->finished_at, 0, 10), 'sub-value' => 'Finished date', 'icon' => 'o-calendar'];
        if(is_numeric($this->boat->finished_weight))
            $details[] = ['name' => $this->boat->finished_weight, 'sub-value' => 'Final weight (kg)', 'icon' => 'o-scale'];
        if(is_numeric($this->boat->co2))
            $details[] = ['name' => $this->boat->co2, 'sub-value' => 'Carbon footprint (kg co2 eq.)', 'icon' => 'carbon.carbon-accounting'];


        if(!$this->boat->synced){
            return [
                'details' => $details,
                'fittings' => [],
                'colors' => [],
                'aboutModel' => Content::where('path', 'model/about/'.strtolower(implode('_', $parts)))->first(),
                'boatMedia' => [],
            ];
        }

        $products = $this->boat->products()
            ->get()
            ->transform(function(Product $item, $key){
            //dump($item->pivot);
                if(is_numeric($item->pivot->attribute_id)){
                    $item->attribute = Attribute::find($item->pivot->attribute_id);
                }
                return $item;
        });

        $boatMedia = [];
        foreach($this->boat->getMedia('*') as $media){
            $boatMedia[] = $media->getUrl();
        }
        foreach($this->boat->product->getMedia('*') as $media){
            $boatMedia[] = $media->getUrl();
        }


        $marketValue = $this->boat->marketValue();
        if(is_numeric($marketValue))
            $details[] = ['name' => '€'.$marketValue, 'sub-value' => 'Market value', 'icon' => 'o-banknotes',];

        return [
            'details' => $details,
            'fittings' => $products->where('product_type_id', '<>', ProductTypeEnum::Color->value)->where('type.fitting', true),
            'colors' => $products->where('product_type_id', '=', ProductTypeEnum::Color->value),
            'aboutModel' => Content::where('path', 'model/about/'.strtolower(implode('_', $parts)))->first(),
            'boatMedia' => $boatMedia,
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
@section('title'){{ $boat->model }}@endsection

@push('head')
    {{-- PhotoSwipe --}}
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/umd/photoswipe-lightbox.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/photoswipe@5.4.3/dist/photoswipe.min.css" rel="stylesheet">
@endpush

    <x-mary-header title="{{$boat->model}}">
    </x-mary-header>

    @if(count($boatMedia) > 0 && $boat->synced)
    <x-mary-image-gallery :images="$boatMedia" class="h-40 rounded-box mb-5"></x-mary-image-gallery>
    @endif

    <div class="grid lg:grid-cols-2 gap-5">
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

        <!-- FITTINGS -->
        @if(!$boat->synced)
            <div wire:poll.1s>
                <x-mary-card title="Loading more details" subtitle="Please wait while we load all the details for the boat.">
                    <x-mary-loading class="loading-bars loading-lg" />
                </x-mary-card>
            </div>
        @else
            <x-mary-card title="Fittings">
                @foreach($fittings as $product)
                    <x-mary-list-item :item="$product" sub-value="attribute.name">
                        <x-slot:avatar><x-mary-avatar :image="$product->image" class="!w-11"></x-mary-avatar></x-slot:avatar>
                    </x-mary-list-item>
                @endforeach
                @isset($boatRegistration->boat->assembler)
                    <div class="mt-10">
                        <x-mary-avatar :title="$boat->assembler->name" subtitle="Assembly" :image="$boat->assembler->photo" class="!w-14"></x-mary-avatar>
                    </div>
                @endisset
                @isset($boatRegistration->boat->evaluator)
                    <div class="mt-10">
                        <x-mary-avatar :title="$boat->evaluator->name" subtitle="Quality control" :image="$boat->evaluator->photo" class="!w-14"></x-mary-avatar>
                    </div>
                @endisset
            </x-mary-card>

            <!-- DESIGN -->
            <x-mary-card title="Design">
                @foreach($colors as $product)
                    <x-mary-list-item :item="$product" sub-value="attribute.name">
                        <x-slot:avatar><div style="background-color: {{ $product->attributes['hex']??'#ffffff' }};" class="w-11 h-11 rounded-full"></div></x-slot:avatar>
                    </x-mary-list-item>
                @endforeach
                    @if(!empty($boatRegistration->boat->painter))
                        <div class="mt-10">
                            <x-mary-avatar :title="$boat->painter->name" subtitle="Painter" :image="$boat->painter->photo" class="!w-14"></x-mary-avatar>
                        </div>
                    @endif
            </x-mary-card>

            <!-- LAYUP -->
            <x-mary-card title="Layup">
                @isset($layup)
                    <x-basic-content :content="$layup"></x-basic-content>
                @endisset
                @if(Str::contains($boat->model, '400'))
                    <p><iframe width="560" height="315" src="https://www.youtube.com/embed/TQibdFLR78A" title="" frameBorder="0"   allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"  allowFullScreen></iframe></p>
                @endif
                @if(!empty($boatRegistration->boat->layuper))
                    <div class="mt-10">
                        <x-mary-avatar :title="$boat->layuper->name" subtitle="Layup" :image="$boat->layuper->photo" class="!w-14"></x-mary-avatar>
                    </div>
                @endif
            </x-mary-card>
        @endif
    </div>

    <x-mary-drawer wire:model="showContent" :title="$selectedContent->title??''" right class="w-11/12 lg:w-1/2">
        @isset($selectedContent)
            <x-basic-content :content="$selectedContent" no-title></x-basic-content>
        @endisset
        <x-slot:actions>
            <x-mary-button label="Close" @click="$wire.showContent = false"></x-mary-button>
        </x-slot:actions>
    </x-mary-drawer>

</div>
