<nav class="-mx-3 flex flex-col sm:flex-row flex-1 justify-end">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="rounded-md px-3 sm:py-2 text-black text-xs sm:text-sm ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            Dashboard
        </a>
    @else
        <div class="flex flex-row">
            <a
                href="{{ route('login') }}"
                class="rounded-md px-3 sm:py-2 text-black text-xs sm:text-sm ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-black dark:hover:text-black/80 dark:focus-visible:ring-black"
            >
                Log in
            </a>

            @if (Route::has('register'))
                <a
                    href="{{ route('register') }}"
                    class="rounded-md px-3 sm:py-2 text-black text-xs sm:text-sm ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-black dark:hover:text-black/80 dark:focus-visible:ring-black"
                >
                    Register
                </a>
            </div>
        @endif

        <a
            href="{{ \Illuminate\Support\Facades\Storage::disk('s3')->url('public-pdfs/quickStart_sfdi_20250906.pdf') }}"
            class="text-right rounded-md px-3 sm:py-2 text-black text-xs sm:text-sm ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-black dark:hover:text-black/80 dark:focus-visible:ring-black"
            target="_blank"
        >
            QuickStart
        </a>
    @endauth
</nav>
