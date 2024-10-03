<?php

namespace App\Livewire\Boats;

use Illuminate\Support\Facades\Route;
use Livewire\Component;
use App\Enums\ProductTypeEnum;
use App\Enums\FieldEnum;
use App\Models\Product;
use App\Models\Boat;
use App\Models\Discipline;
use App\Models\Attribute;
use App\Models\Content;
use Illuminate\Database\Eloquent\Collection;
use Mary\Traits\WithMediaSync;
use Livewire\Attributes\Layout;
class ShowPublic extends Component
{
    use WithMediaSync;

    public bool $showContent = false;

    public int $boat_id;

    public Boat $boat;
    public Product $model;
    public Discipline $discipline;

    public ?Content $layup;
    public ?Content $selectedContent;

    public bool $notComplete = true;

    public bool $isPool = false;

    public function boot():void{
        $boat = Boat::getWithSync($this->boat_id);
        if(empty($boat)){
            $this->redirectRoute('boat-not-found');
            return;
        }

        $this->boat = $boat;
    }

    public function mount():void
    {

        $this->isPool = Route::currentRouteName() == 'boats.pool';

        if(empty($this->boat) || !$this->boat?->synced)
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
        if($this->isPool){
            if(!empty($this->boat->remarks))
                $details[] = ['name' => $this->boat->remarks, 'sub-value' => 'Remarks', 'icon' => 'tabler.sticker-2'];
            if(!empty($this->boat->reference))
                $details[] = ['name' => $this->boat->reference, 'sub-value' => 'Reference', 'icon' => 'tabler.barcode'];
        }



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
            if($this->isPool || $media->collection_name != 'pool'){
                $boatMedia[] = $media->getUrl();
            }
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

    #[Layout('layouts.public')]
    public function render()
    {
        return view('livewire.boats.show_public', $this->with());
    }
}
