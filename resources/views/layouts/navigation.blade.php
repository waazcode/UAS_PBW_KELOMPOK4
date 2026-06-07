<nav x-data="{ open: false }" class="bg-midnight border-b border-neptune/50 sticky top-0 z-50">
    <div class="page-container">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="flex items-center gap-2 shrink-0">
                    <x-application-logo class="block h-8 w-8 text-isotonic" />
                    <span class="font-bold text-cheviot text-lg hidden sm:block">SafeZone</span>
                </a>

                @auth
                    <div class="hidden sm:flex sm:items-center sm:gap-1">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('laporan.peta')" :active="request()->routeIs('laporan.peta')">
                            {{ __('Peta') }}
                        </x-nav-link>
                        <x-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.index', 'laporan.create', 'laporan.show')">
                            {{ __('Laporan') }}
                        </x-nav-link>
                        @if (Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.index', 'admin.laporan.show')">
                                {{ __('Kelola Laporan') }}
                            </x-nav-link>
                        @endif
                    </div>
                @endauth
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <div class="hidden sm:flex sm:items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-cheviot hover:text-isotonic transition">
                                    <span class="w-8 h-8 flex items-center justify-center rounded-full bg-neptune text-cheviot text-xs font-bold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                    <span class="hidden md:block">{{ Auth::user()->name }}</span>
                                    <svg class="fill-current h-4 w-4 text-grape-mist" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profil') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Keluar') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden sm:flex sm:items-center sm:gap-2">
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-cheviot hover:text-isotonic transition">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-nav">
                                Daftar
                            </a>
                        @endif
                    </div>
                @endauth

                <button @click="open = ! open" class="sm:hidden inline-flex items-center justify-center p-2 rounded-lg text-grape-mist hover:text-cheviot hover:bg-neptune/50 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-neptune/50">
        @auth
            <div class="pt-2 pb-3 space-y-1 px-2">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('laporan.peta')" :active="request()->routeIs('laporan.peta')">
                    {{ __('Peta') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.index', 'laporan.create', 'laporan.show')">
                    {{ __('Laporan') }}
                </x-responsive-nav-link>
                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.laporan.index')" :active="request()->routeIs('admin.laporan.index', 'admin.laporan.show')">
                        {{ __('Kelola Laporan') }}
                    </x-responsive-nav-link>
                @endif
            </div>

            <div class="pt-4 pb-4 border-t border-neptune/50 px-4">
                <div class="font-medium text-cheviot">{{ Auth::user()->name }}</div>
                <div class="text-sm text-grape-mist">{{ Auth::user()->email }}</div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profil') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="py-4 px-4 space-y-2">
                <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-sm font-medium text-cheviot border border-grape-mist/40 rounded-xl">
                    Masuk
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="block w-full text-center btn-nav !py-2.5">
                        Daftar
                    </a>
                @endif
            </div>
        @endauth
    </div>
</nav>
