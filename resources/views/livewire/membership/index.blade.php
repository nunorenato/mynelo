<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {

    public int $nBoats;
    public float $totalOrders;
    public User $user;

    public function mount()
    {

        $this->user = Auth::user();

        $this->nBoats = $this->user->allRegisteredBoats()->count();

        $this->totalOrders = \App\Models\Magento\PaddleLabSalesOrder::allOrders($this->user)->sum('total_invoiced');

    }
}
?>
<div>
    <x-mary-header title="Membership" separator></x-mary-header>
    <div class="grid lg:grid-cols-4 gap-4 lg:gap-5 mb-5">
        @empty($user->membership)
            <x-mary-stat title="Membership" value="None" icon="tabler.award-filled" color="text-neutral"></x-mary-stat>
        @else
            <x-mary-stat title="Membership" :value="$user->membership?->name" icon="tabler.award-filled"
                         color="text-{{ $user->membership_id?->color() }}"></x-mary-stat>
        @endempty
        <x-mary-stat title="Boats registered" :value="$nBoats" icon="tabler.kayak" color="text-red-700"></x-mary-stat>
        <x-mary-stat title="Paddle Lab Orders" :value="Number::currency($totalOrders, 'EUR')" icon="o-shopping-bag"
                     color="text-sky-700"></x-mary-stat>
        @isset($user->extras['paddle_lab_discount'])
            <x-mary-stat title="Coupon" description="Use it when ordering" :value="$user->extras['paddle_lab_discount']" icon="tabler.discount" color="text-neutral"></x-mary-stat>
        @endisset
    </div>
    <livewire:membership.tiers></livewire:membership.tiers>
</div>
