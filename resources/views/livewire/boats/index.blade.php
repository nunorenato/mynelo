<x-app-layout>
    @section('title') Boats @endsection
    <x-mary-header title="Boats" separator>
        <x-slot:actions>
            <livewire:boats.preregister></livewire:boats.preregister>
            <x-mary-button label="Order" icon="o-building-storefront" class="btn-primary" :link="route('boats.order')"></x-mary-button>
        </x-slot:actions>
    </x-mary-header>
    <livewire:boats.list></livewire:boats.list>
</x-app-layout>
