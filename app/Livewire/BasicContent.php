<?php

namespace App\Livewire;

use App\Models\Content;
use Livewire\Component;

class BasicContent extends Component
{
    public ?bool $noTitle = false;
    public $content;

    public function mount($content)
    {

        $this->content = $content;
    }
    public function render()
    {
        //dump('xxx');
        return view('livewire.components.basic-content')->with(['content' => null]);
    }
}
