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

        <x-mary-menu-item title="Home" icon="o-home" link="dashboard" />
        <x-mary-menu-sub title="Settings" icon="o-cog-6-tooth">
            <x-mary-menu-item title="Wifi" icon="o-wifi" link="####" />
            <x-mary-menu-item title="Archives" icon="o-archive-box" link="####" />
        </x-mary-menu-sub>
    </x-mary-menu>
</nav>
