<?php

use App\Models\User;
use App\Models\Country;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $country_id = '';
    public $photo  =null;
    public ?string $date_of_birth;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->country_id = Auth::user()->country_id;
        $this->photo = Auth::user()->photo;
        $this->date_of_birth = Auth::user()->date_of_birth;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validation = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'country_id' => ['nullable'],
            'date_of_birth' => ['nullable', 'date'],
        ];

        $url = null;
        if ($this->photo && !is_string($this->photo)) {
            $url = $this->photo->store('users', 'public');
        //    dd($url);
            //$this->user->update(['avatar' => "/storage/$url"]);
            $validation['photo'] = ['nullable','image'];
        }

        $validated = $this->validate($validation);
        if(!empty($url)){
            $validated['photo'] = "/storage/$url";
        }


        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);

        activity()
            ->on($user)
            ->event('updated profile info')
            ->log('USER update');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: RouteServiceProvider::HOME);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function with(): array
    {
        return [
            'countries' => Country::all(),
        ];

    }
}?>
@push('head')
    {{-- Cropper.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
@endpush
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <x-mary-form wire:submit="updateProfileInformation" class="mt-6 ">
        <div class="grid lg:grid-cols-[400px_1fr] gap-5">
            <div class="space-y-4">
                <x-mary-input label="Name" wire:model="name" required autofocus autocomplete="name"></x-mary-input>
                <x-mary-input label="email" wire:model="email" required></x-mary-input>

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            {{ __('Your email address is unverified.') }}

                            <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
                <x-mary-select label="Country" wire:model="country_id" :options="$countries" placeholder="---"></x-mary-select>
                <x-mary-datetime label="Date of birth" wire:model="date_of_birth"></x-mary-datetime>
            </div>
            <div>
                <x-mary-file label="Photo" wire:model="photo" accept="image/png, image/jpeg" hint="Click to change" crop-after-change>
                    <img src="{{ $photo ?? '/images/no-user-photo.png' }}" class="w-40 rounded-lg" alt="Profile photo" />
                </x-mary-file>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <x-mary-button label="Save" spinner="save" type="submit" class="btn-primary" />

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </x-mary-form>
</section>
