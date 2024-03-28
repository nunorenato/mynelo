
<x-app-layout>
    <x-mary-header title="Boats" separator>
        <x-slot:actions>
            <livewire:boats.preregister></livewire:boats.preregister>
        </x-slot:actions>
    </x-mary-header>
    <livewire:boats.list></livewire:boats.list>
</x-app-layout>
