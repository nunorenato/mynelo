<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {

    public User $user;
    public ?string $coupon = null;

    public function mount()
    {
        $this->user = Auth::user();

        if(!empty($this->user->extras) && !empty($this->user->extras['coupon'])){
            if(empty($this->user->extras['coupon_used'])){
                $this->user->extras = array_merge($this->user->extras, ['coupon_used' => false]);
                $this->user->save();
            }
            if($this->user->extras['coupon_used'])
                $this->coupon = null;
            else
                $this->coupon = $this->user->extras['coupon'];
        }

    }
}
?>
<div>
    <x-mary-header title="Paddle Lab" separator></x-mary-header>
    <div class="grid lg:grid-cols-2 gap-5">
        <x-mary-card title="Very soon">
            <x-slot:figure>
                <img src="https://paddle-lab.com/pub/media//logo/stores/6/logo_white_pl.jpg" alt="Paddle Lab"/>
            </x-slot:figure>
            This will soon be the place to manage your Paddle Lab orders. Stay tuned.
        </x-mary-card>
        @isset($coupon)
            <x-mary-card title="Your coupon">
                <p class="mb-5">For having registered in MyNelo, please enjoy a €10 discount in any order on Paddle Lab.</p>
                <p class="mb-5">Use the following on the Paddle Lab store to get your discount:</p>
                <x-mary-badge :value="$coupon" class="badge-neutral p-5 mb-5"></x-mary-badge>
                <x-mary-button link="https://paddle-lab.com" external>Start shopping</x-mary-button>
            </x-mary-card>
        @endisset
    </div>
</div>
