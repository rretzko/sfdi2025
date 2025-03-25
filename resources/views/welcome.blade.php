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

            <img id="background" class="mx-auto w-full sm:w-1/2 " alt="musicStand Logo" src="https://auditionsuite-production.s3.amazonaws.com/musicStand.svg" />

        </div>

        {{-- FOOTER --}}
        <x-site-footer />

    </div>

    </body>
</html>
