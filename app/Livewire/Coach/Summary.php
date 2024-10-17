<?php

namespace App\Livewire\Coach;

use App\Enums\UnitsEnum;
use App\Helpers\SessionDataConvertible;
use App\Models\Coach\Session;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

// #[On("units-changed")]
class Summary extends Component
{
    public Session $session;
    #[Reactive]
    public UnitsEnum $units;

    public function mount(Session $session = null, UnitsEnum $units = null){

        $this->session = $session;
        $this->units = $units??UnitsEnum::Kilometers;

        //dump($this->session);
    }
    public function render()
    {
        return view('livewire.coach.summary', [
            'sessionDataConvertible' => new SessionDataConvertible($this->session, $this->units),
        ]);
    }


}
