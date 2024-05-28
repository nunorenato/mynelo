<?php

use App\Models\Magento\SalesOrder;
use App\Enums\MagentoStatusEnum;
use Livewire\Volt\Component;

new class extends Component {

    public SalesOrder $order;

    public int $step;
    public array $headers;
   // public array $cellDecoration;

    public ?\App\Models\Magento\Address $billing;
    public ?\App\Models\Magento\Address $shipping;

    public function mount():void{

        $this->headers = [
            ['key' => 'name', 'label' => 'Product'],
            ['key' => 'qty_ordered', 'label' => 'Qty', 'class' => 'text-right'],
            ['key' => 'price', 'label' => 'Price', 'class' => 'text-right'],
            ['key' => 'row_total', 'label' => 'Total', 'class' => 'text-right'],
        ];



        $this->step = match($this->order->status){
            MagentoStatusEnum::PENDING => 1,
            MagentoStatusEnum::PROCESSING => 2,
            MagentoStatusEnum::COMPLETE => 3,
            default => 0,
        };

        $this->billing = $this->order->addresses()->where('address_type', 'billing')->first();
        $this->shipping = $this->order->addresses()->where('address_type', 'shipping')->first();

    }

    public function with():array{
        return [
            'cellDecoration' => [
                'price' => [
                    'text-right' => fn() => true,
                ],
                'row_total' => [
                     'text-right' => fn() => true,
                 ],
            ],
        ];
    }

}

?>
<div>
    <x-mary-header title="Order #{{ $order->increment_id }}" subtitle="{{ $order->created_at }}" separator>
        <x-slot:actions>
            <x-mary-badge :value="$order->status->toString()" class="{{$order->status->cssClass()}} p-5"></x-mary-badge>
        </x-slot:actions>
    </x-mary-header>
    @if($step > 0)
        <x-mary-steps wire:model="step" class="my-5 w-full">
            <x-mary-step step="1" text="Created" step-classes="!step-warning"></x-mary-step>
            <x-mary-step step="2" text="Processing" step-classes="!step-info"></x-mary-step>
            <x-mary-step step="3" text="Shipped" step-classes="!step-success"></x-mary-step>
        </x-mary-steps>
    @endif
    <x-mary-card title="Items">
        <x-mary-table :headers="$headers" :rows="$order->items()->whereNull('parent_item_id')->get()" :cell-decoration="$cellDecoration">
            @scope('cell_name', $row)
                {{ $row->name }}
                @isset($row->product_options['attributes_info'])
                    @foreach($row->product_options['attributes_info'] as $attribute)
                        <div class="text-sm">{{ $attribute['label'] }}: {{ $attribute['value'] }}</div>
                    @endforeach
                @endisset
                @isset($row->product_options['options'])
                    @foreach($row->product_options['options'] as $option)
                        <div class="text-sm">{{ $option['label'] }}: {{ $option['print_value'] }}</div>
                    @endforeach
                @endisset
            @endscope
            @scope('cell_qty_ordered', $row)
            {{ Number::format($row->qty_ordered) }}
            @endscope
            @scope('cell_price', $row)
                {{ Number::currency($row->price, 'EUR') }}
            @endscope
            @scope('cell_row_total', $row)
            {{ Number::currency($row->row_total, 'EUR') }}
            @endscope
        </x-mary-table>
    </x-mary-card>
    <div class="lg:grid grid-cols-3 gap-5 my-5">
        <x-mary-card title="Billing Address">
            {{ $billing->firstname }} {{ $billing->lastname }} <br>
            @isset($billing->company) {{ $billing->company }}<br>@endisset
            {{ $billing->street }}<br>
            {{ $billing->city }}<br>
            @isset($billing->region) {{ $billing->region }}<br>@endisset
            {{ $billing->country->name }}
        </x-mary-card>
        <x-mary-card title="Shipping Address">
            {{ $shipping->firstname }} {{ $shipping->lastname }} <br>
            @isset($shipping->company) {{ $shipping->company }}<br>@endisset
            {{ $shipping->street }}<br>
            {{ $shipping->city }}<br>
            @isset($shipping->region) {{ $shipping->region }}<br>@endisset
            {{ $shipping->country->name }}
        </x-mary-card>
        <x-mary-card title="Summary">
            <div class="grid grid-cols-2 gap-2">
                <div class="text-right">Subtotal</div>
                <div class="text-right">{{ Number::currency($order->subtotal_invoiced, 'EUR') }}</div>
                @isset($order->discount_invoiced)
                    <div class="text-right">Discount</div>
                    <div class="text-right">{{ Number::currency($order->discount_invoiced, 'EUR') }}</div>
                @endisset
                <div class="text-right">Shipping & Handling</div>
                <div class="text-right">{{ Number::currency($order->shipping_invoiced, 'EUR') }}</div>
                <div class="text-right font-bold">Total</div>
                <div class="text-right font-bold">{{ Number::currency($order->total_invoiced, 'EUR') }}</div>
            </div>
        </x-mary-card>
    </div>
</div>
