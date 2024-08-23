<header class="shadow-lg py-2 h-12">

    <h1 class="w-full text-left pl-2 md:text-center text-xl text-black pt-2">
        <a href="{{ route('welcome') }}">StudentFolder.info</a>
    </h1>

    @if (Route::has('login'))
        <div class="absolute top-0 right-4">
            <livewire:welcome.navigation/>
        </div>
    @endif
</header>
