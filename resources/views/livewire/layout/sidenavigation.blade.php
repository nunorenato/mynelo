<?php
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component{

    public bool $showFeedback = false;

    #[On('feedback-sent')]
    public function closeFeedback()
    {
        $this->showFeedback = false;
    }

}
?>
<nav>
    <x-mary-menu activate-by-route>

        {{-- User --}}
        @if($user = auth()->user())
            @php $user->avatar = $user->photo; @endphp
            <x-mary-menu-separator />

            <x-mary-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
                <x-slot:actions>
                    @php //<x-mary-button icon="o-power" class="btn-circle btn-ghost btn-xs" tooltip-left="logoff" no-wire-navigate link="/logout" /> @endphp
                    <x-mary-dropdown title="Settings" icon="o-cog-6-tooth">
                        <x-slot:trigger>
                            <x-mary-button icon="o-cog-6-tooth" class="btn-circle btn-ghost btn-xs" />
                        </x-slot:trigger>
                        <x-mary-menu-item icon="o-user" title="Profile" :link="route('profile')" />
                        <x-mary-menu-item icon="o-power" title="Logout" :link="route('logout')"  no-wire-navigate />
                    </x-mary-dropdown>
                </x-slot:actions>
            </x-mary-list-item>

            <x-mary-menu-separator />
        @endif

        <x-mary-menu-item title="Home" icon="o-home" :link="route('dashboard')" />
        <x-mary-menu-item title="Boats" icon="tabler.kayak" :link="route('boats')" />
        <x-mary-menu-separator />
        <x-mary-menu-item title="Feedback" icon="o-chat-bubble-bottom-center-text" @click="$wire.showFeedback = true"></x-mary-menu-item>
        @if(Auth::user()->hasRole(Spatie\Permission\Models\Role::findByName('Admin')))
        <x-mary-menu-separator />
        <x-mary-menu-item title="Admin" icon="o-bolt" link="/admin"></x-mary-menu-item>
        @endif

        @php /*
        <x-mary-menu-sub title="Settings" icon="o-cog-6-tooth">
            <x-mary-menu-item title="Wifi" icon="o-wifi" link="####" />
            <x-mary-menu-item title="Archives" icon="o-archive-box" link="####" />
        </x-mary-menu-sub>
        */ @endphp
    </x-mary-menu>
    <template x-teleport="body">
        <x-mary-modal wire:model="showFeedback" title="Send us your feedback">
            <livewire:components.feedback>
                    <x-mary-button label="close" @click="$wire.showFeedback = false"></x-mary-button>
            </livewire:components.feedback>
        </x-mary-modal>
    </template>
</nav>

