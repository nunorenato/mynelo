<?php

use App\Models\Magento\CustomerEntity;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;

new class extends Component {

    public ?Collection $orders;
    public array $ordersHeaders;
    public ?CustomerEntity $customer;

    public function mount():void{

        $this->ordersHeaders = [
            ['key' => 'increment_id', 'label' => 'Order #'],
            ['key' => 'created_at', 'label' => 'Purchase date'],
            ['key' => 'total_paid', 'label' => 'Grand total'],
            ['key' => 'status', 'label' => 'Status'],
        ];

        $this->customer = Auth::user()->paddleLabCustomer;

        /*$nonCustomer = \App\Models\Magento\PaddleLabSalesOrder::whereNull('customer_id')->where('customer_email', Auth::user()->email);

        if(!empty($this->customer)){
            // search for order done by the same email before beign a customer
            $this->orders = $this->customer->orders()->union($nonCustomer)->latest()->get();
        }
        else{
            $this->orders = $nonCustomer->latest()->get();
        }*/

        $this->orders = \App\Models\Magento\PaddleLabSalesOrder::allOrders(Auth::user());
    }

}
?>
<div>
    <x-mary-header title="Paddle Lab" separator>
        <x-slot:actions>
            <x-mary-button label="Shop" icon="o-shopping-bag" class="btn-accent" link="https://paddle-lab.com" external></x-mary-button>
        </x-slot:actions>
    </x-mary-header>
    <div class="flex justify-between gap-5 mb-5">
        @empty($customer)
            <x-mary-card title="New to Paddle Lab?" subtitle="It seems you don't have an account in our online shop">
                <x-mary-button label="Create an account" class="btn-lg" icon="o-plus" link="https://paddle-lab.com/customer/account/create/" external></x-mary-button>
            </x-mary-card>
        @endempty
        <livewire:paddlelab.coupon></livewire:paddlelab.coupon>
    </div>
    @isset($orders)
        <x-mary-card title="Your orders" separator>
            <x-mary-table :headers="$ordersHeaders" :rows="$orders" link="/paddle-lab/order/{entity_id}">
                @scope('cell_status', $row)
                <x-mary-badge :class="$row->status->cssClass()" :value="$row->status->toString()"></x-mary-badge>
                @endscope
            </x-mary-table>
        </x-mary-card>
    @endisset
</div>
