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
                            <x-mary-button icon="o-information-circle" class="btn-circle btn-sm" wire:click="loadContent({{ \App\Models\Content::findByPath('marketvalue')->id }})" spinner></x-mary-button>
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
