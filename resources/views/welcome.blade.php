<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">
            @if (Route::has('login'))
                <livewire:welcome.navigation />
            @endif

            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="flex justify-center">
                    <x-application-logo class="h-8 w-auto bg-gray-100"></x-application-logo>
                </div>

                <div class="relative isolate px-6 pt-14 lg:px-8">
                    <!--<div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                    </div>-->
                    <div class="mx-auto max-w-2xl py-16 sm:py-24 lg:py-28">
                        <!--<div class="hidden sm:mb-8 sm:flex sm:justify-center">
                            <div class="relative rounded-full px-3 py-1 text-sm leading-6 text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
                                Announcing our next round of funding. <a href="#" class="font-semibold text-indigo-600"><span class="absolute inset-0" aria-hidden="true"></span>Read more <span aria-hidden="true">&rarr;</span></a>
                            </div>
                        </div>-->
                        <div class="text-center">
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl">Become part of the Nelo Family</h1>
                            <p class="mt-6 text-lg leading-8 text-gray-600">My Nelo is your home for all Nelo related. A single place for all your Nelo products and services. Join now and be part of an exclusive club.</p>
                            <div class="mt-10 flex items-center justify-center gap-x-6">
                                <a href="{{route('register')}}" class="rounded-md bg-red-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Get started</a>
                                <!-- <a href="#" class="text-sm font-semibold leading-6 text-gray-900">Learn more <span aria-hidden="true">→</span></a>-->
                            </div>
                        </div>
                    </div>
                    <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]" aria-hidden="true">
                        <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                    </div>
                </div>

                <!-- FEATURES -->
                <div class="overflow-hidden bg-white py-24 sm:py-32">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="mx-auto grid grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
                            <div class="lg:pr-8 lg:pt-4">
                                <div class="lg:max-w-lg">
                                    <h2 class="text-base font-semibold leading-7 text-red-600">All in one place</h2>
                                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Everything about your boat</p>
                                    <p class="mt-6 text-lg leading-8 text-gray-600">Besides having all the info about your boats, MyNelo offers lots of tools to improve your boat care, the way you use your boat, logistics, and many others.</p>
                                    <dl class="mt-10 max-w-xl space-y-8 text-base leading-7 text-gray-600 lg:max-w-none">
                                        <div class="relative pl-9">
                                            <dt class="inline font-semibold text-gray-900">
                                                <x-mary-icon name="tabler.kayak"  class="absolute left-1 top-1 h-5 w-5 text-red-600"></x-mary-icon>
                                                Boat management.
                                            </dt>
                                            <dd class="inline">Register all your boats and discover all the benefits we offer. Discover how you can take care and upgrade your boats.</dd>
                                        </div>
                                        <div class="relative pl-9">
                                            <dt class="inline font-semibold text-gray-900">
                                                <x-mary-icon name="o-adjustments-horizontal"  class="absolute left-1 top-1 h-5 w-5 text-red-600"></x-mary-icon>
                                                Boat setup.
                                            </dt>
                                            <dd class="inline">Save al your setups for future reference.</dd>
                                        </div>
                                        <div class="relative pl-9">
                                            <dt class="inline font-semibold text-gray-900">
                                                <x-mary-icon name="tabler.history"  class="absolute left-1 top-1 h-5 w-5 text-red-600"></x-mary-icon>
                                                Boat history.
                                            </dt>
                                            <dd class="inline">When was your boat made, by whom, what were its previous owners.</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <img src="/images/boat-profile.png" alt="Product screenshot" class="w-[48rem] max-w-none rounded-xl shadow-xl ring-1 ring-gray-400/10 sm:w-[57rem] md:-ml-4 lg:-ml-0" width="2432" height="1442">
                        </div>
                    </div>
                </div>
                <!-- /FEATURES -->

                <livewire:membership.tiers></livewire:membership.tiers>
            </div>
        </div>
    </body>
</html>
