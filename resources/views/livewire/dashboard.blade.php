<?php

use App\Models\Magento\CustomerEntity;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public int $nBoats;
    public int $nOrders;

    public function mount(){

        $user = Auth::user();

        $this->nBoats = $user->boats()
            ->whereIn('status', [\App\Enums\StatusEnum::VALIDATED, \App\Enums\StatusEnum::COMPLETE])
            ->count();

        $this->nOrders = \App\Models\Magento\PaddleLabSalesOrder::allOrders($user)->count();
    }
}
?>
<div>
    <x-mary-header title="Dashboard" separator></x-mary-header>

    <div class="grid lg:grid-cols-4 gap-4 lg:gap-5">
        <x-mary-stat title="Boats registered" :value="$nBoats" icon="tabler.kayak" color="text-red-700"></x-mary-stat>
        <x-mary-stat title="Paddle Lab orders" :value="$nOrders" icon="o-shopping-bag" color="text-sky-700"></x-mary-stat>
        @if(Auth::user()->isAdmin())
        <x-mary-stat title="Membership" value="Bronze" icon="tabler.award-filled" class="text-orange-700" color="text-orange-700"></x-mary-stat>
        @endif
    </div>

    <div class="grid lg:grid-cols-2 gap-5 lg:gap-8">
        <livewire:profile.dashboard></livewire:profile.dashboard>
        <livewire:boats.dashboard></livewire:boats.dashboard>
        <livewire:paddlelab.coupon></livewire:paddlelab.coupon>
    </div>
</div>
