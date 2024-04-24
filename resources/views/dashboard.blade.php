<x-app-layout>
    <x-mary-header title="Dashboard" separator></x-mary-header>

    <x-mary-card title="Welcome" separator>
        <p>Welcome to My Nelo. This will be your hub for your Nelo products and services.</p>
        <p>We will continually be introducing new features and options to this platform, so stay in touch.</p>
    </x-mary-card>

    <div class="grid lg:grid-cols-2 gap-5 lg:gap-8">
        <livewire:profile.dashboard></livewire:profile.dashboard>
        <livewire:boats.dashboard></livewire:boats.dashboard>
    </div>
</x-app-layout>
