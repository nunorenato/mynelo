<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $country_id = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'country_id' => ['required'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <x-mary-input label="Name" wire:model="name" required autofocus autocomplete="name" icon="o-user" placeholder="Your name" />

        <!-- Email Address -->
        <div class="mt-4">
            <x-mary-input label="Email" wire:model="email" required autocomplete="username" icon="o-at-symbol" type="email" placeholder="Your email" />
        </div>

        <!-- Country -->
        <div class="mt-4">
            @php
                $countries = App\Models\Country::all(['id', 'name']);
            @endphp
            <x-mary-select label="Country" :options="$countries" wire:model="country_id" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-mary-input label="Password" wire:model="password" icon="o-eye" type="password" required />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-mary-input label="Confirm Password" wire:model="password_confirmation" type="password" required/>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
