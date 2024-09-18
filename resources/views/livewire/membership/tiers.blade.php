<?php

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {

    public ?User $user = null;
    public ?\App\Models\Membership $membership = null;
    public ?string $color = null;

    public function mount(): void
    {

        if (Auth::hasUser()) {
            $this->user = Auth::user();
            $this->membership = $this->user->membership ?? null;
            $this->color = $this->user->membership_id?->color();
        }

    }

}
?>
<div class="bg-white py-5 sm:py-6 rounded-xl">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">MyNelo membership tiers</p>
        </div>
        <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-600">Price discounts. Early access.
            Exclusive deals. Nelo has four membership tiers, each with increasing benefits and advantages.</p>
        <div
            class="isolate mx-auto mt-10 grid max-w-md grid-cols-1 gap-8 md:max-w-2xl md:grid-cols-2 lg:max-w-4xl xl:mx-0 xl:max-w-none xl:grid-cols-4">
            <div class="rounded-3xl p-8 @if($membership !== null && $membership->name == 'Bronze') ring-2 ring-{{$color}} @else ring-1 ring-gray-200 @endif">
                <h3 id="tier-hobby" class="text-lg font-semibold leading-8 @if($membership !== null && $membership->name == 'Bronze') text-{{$color}} @else text-gray-900 @endif">Bronze</h3>
                <p class="mt-4 text-sm leading-6 text-gray-600">The first tier of membership. Bronze provides an entry
                    level experience.</p>
                <p class="text-sm text-gray-600 mt-4 font-semibold">Criteria</p>
                <ul class="text-sm text-gray-600">
                    <li class=" mt-2">1 boat registered</li>
                </ul>
                <hr class="mt-8">
                <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        5% discount on future Paddle Lab orders
                    </li>
                </ul>
            </div>
            <div class="rounded-3xl p-8 @if($membership !== null && $membership->name == 'Silver') ring-2 ring-{{$color}} @else ring-1 ring-gray-200 @endif">
                <h3 id="tier-freelancer" class="text-lg font-semibold leading-8 @if($membership !== null && $membership->name == 'Silver') text-{{$color}} @else text-gray-900 @endif">Silver</h3>
                <p class="mt-4 text-sm leading-6 text-gray-600">A new range of advantages and services await you in the
                    Silver tier.</p>
                <p class="text-sm text-gray-600 mt-4 font-semibold">Criteria</p>
                <ul class="text-sm text-gray-600">
                    <li class=" mt-2">2 registered boats</li>
                    <li class=" mt-2">Or 1 registered boat and over €500 in Paddle Lab orders</li>
                </ul>
                <hr class="mt-8">
                <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        10% discount on future Paddle Lab orders
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Monthly newsletter with updates and promotions
                    </li>
                </ul>
            </div>
            <div class="rounded-3xl p-8 @if($membership !== null && $membership->name == 'Gold') ring-2 ring-{{$color}} @else ring-1 ring-gray-200 @endif">
                <h3 id="tier-startup" class="text-lg font-semibold leading-8 @if($membership !== null && $membership->name == 'Gold') text-{{$color}} @else text-gray-900 @endif">Gold</h3>
                <p class="mt-4 text-sm leading-6 text-gray-600">Look forward to a higher level of benefits and more
                    premium services with a Gold membership.</p>
                <p class="text-sm text-gray-600 mt-4 font-semibold">Criteria</p>
                <ul class="text-sm text-gray-600">
                    <li class=" mt-2">2 registered boats and over €500 in Paddle Lab orders</li>
                    <li class=" mt-2">Or 1 registered boat and over €1000 in Paddle Lab orders</li>
                </ul>
                <hr class="mt-8">
                <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        15% on future Paddle Lab orders
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        10% on future boat orders
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Monthly newsletter with updates and promotions
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Free shipping on Paddle Lab orders
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Early access to new boat models
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Annual gift voucher for your Birthday
                    </li>
                </ul>
            </div>
            <div class="rounded-3xl p-8  @if($membership !== null && $membership->name == 'Platinum') ring-2 ring-{{$color}} @else ring-1 ring-gray-200 @endif">
                <h3 id="tier-enterprise" class="text-lg font-semibold leading-8 @if($membership !== null && $membership->name == 'Platinum') text-{{$color}} @else text-gray-900 @endif">Platinum</h3>
                <p class="mt-4 text-sm leading-6 text-gray-600">Our highest tier rewards our most loyal customers with
                    advantages carefully designed to make Nelo experience unforgettable. </p>
                <p class="text-sm text-gray-600 mt-4 font-semibold">Criteria</p>
                <ul class="text-sm text-gray-600">
                    <li class=" mt-2">4 or more registered boats</li>
                </ul>
                <hr class="mt-8">
                <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-600">
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        20% on future Paddle Lab orders
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        10% on future boat orders
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Monthly newsletter with updates and promotions
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Free shipping on Paddle Lab orders <a href="#conditions-shipping"><sup>1</sup></a>
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Early access to new boat models
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Annual gift voucher for your Birthday
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Guided tour to our factory <a href="#conditions-tour"><sup>2</sup></a>
                    </li>
                    <li class="flex gap-x-3">
                        <svg class="h-6 w-5 flex-none text-secondary" viewBox="0 0 20 20" fill="currentColor"
                             aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Complimentary maintenance service once a year <a href="#conditions-maintenance"><sup>3</sup></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-5 text-sm text-gray-400">
            <p>
                <span class="font-bold">Disclaimer:</span><br>
                Membership status is evaluated at the start of every month.<br>
                Benefits are only applied on future orders.<br>
                Benefits included in the membership are non-transferable and cannot be shared with or passed to another person.
            </p>
            <p class="mt-4">
                <a id="conditions-shipping">1</a> Not applicable to all products. Please check the product notes to confirm if it's  available for free shipping.<br>
                <a id="conditions-tour">2</a> Flight and transportation not included. Accommodation for 1 night for up to 2 persons included, on selected properties, booked by Nelo.<br>
                <a id="conditions-maintenance">3</a> Only available on countries and areas covered by official Nelo dealers certified for this service. Please contact your local dealer for coverage.<br>
            </p>
        </div>
    </div>
</div>
