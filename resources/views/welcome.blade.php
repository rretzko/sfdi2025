<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>StudentFolder.info</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="antialiased font-sans">

    <div id="content" class="flex flex-col h-screen justify-between">

        {{-- HEADER --}}
        <x-site-header />

        {{-- MAIN --}}
        <div id="main" class="">

            <img id="background" class=" w-full" alt="musicStand Logo" src="https://auditionsuite-production.s3.amazonaws.com/musicStand.svg" />

        </div>

        {{-- FOOTER --}}
        <x-site-footer />

    </div>


{{--        <div class="bg-green-50 text-black/50 dark:bg-black dark:text-white/50">--}}

{{--                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">--}}
{{--                    --}}{{-- HEADER --}}
{{--                    <header>--}}

{{--                        <h1 class="text-center w-full pt-2 text-xl text-black">--}}
{{--                            StudentFolder.info--}}
{{--                        </h1>--}}

{{--                        @if (Route::has('login'))--}}
{{--                            <div class="absolute top-0 right-0">--}}
{{--                                <livewire:welcome.navigation/>--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                    </header>--}}
{{--                    <main class="mt-6 bg-red-600">--}}
{{--                        <img id="background" class="absolute left-0 top-12 w-full mx-auto" alt="musicStand Logo" src="https://auditionsuite-production.s3.amazonaws.com/musicStand.svg" />--}}
{{--                        <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">--}}


{{--                        </div>--}}
{{--                    </main>--}}


{{--                </div>--}}
{{--            </div>--}}

    </body>
</html>
