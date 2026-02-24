<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-saas-border dark:border-gray-700 shadow-saas relative z-20 w-full">

    <!-- ============================================================
         PRIMARY NAVIGATION BAR — fixed 64px height, no scrollbar
         ============================================================ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Left: Logo + Nav Links -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Desktop Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('products.view')
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            {{ __('Products') }}
                        </x-nav-link>
                        <x-nav-link :href="route('warehouses.index')" :active="request()->routeIs('warehouses.*')">
                            {{ __('Warehouses') }}
                        </x-nav-link>
                    @endcan

                    @can('transactions.view')
                        <x-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                            {{ __('Transactions') }}
                        </x-nav-link>
                    @endcan

                    @can('ro.view')
                        <x-nav-link :href="route('request-orders.index')" :active="request()->routeIs('request-orders.*')">
                            {{ __('Requests') }}
                        </x-nav-link>
                    @endcan

                    @can('so.view')
                        <x-nav-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')">
                            {{ __('Sales') }}
                        </x-nav-link>
                    @endcan

                    @can('invoices.view')
                        <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                            {{ __('Invoices') }}
                        </x-nav-link>
                    @endcan

                    @can('payments.view')
                        <x-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">
                            {{ __('Payments') }}
                        </x-nav-link>
                    @endcan

                    @can('employees.manage')
                        <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                            {{ __('Employees') }}
                        </x-nav-link>
                        <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                            {{ __('Roles') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Right: Dark Mode + Notifications + User Menu -->
            <div class="hidden sm:flex sm:items-center sm:gap-1">

                <!-- Dark Mode Toggle -->
                <button
                    x-data="{
                        darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                        toggle() {
                            this.darkMode = !this.darkMode;
                            if (this.darkMode) {
                                document.documentElement.classList.add('dark');
                                localStorage.theme = 'dark';
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.theme = 'light';
                            }
                        }
                    }"
                    @click="toggle()"
                    class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition"
                >
                    <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m3.343-5.657l.707.707m12.728 12.728l.707.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                <!-- Notifications Dropdown -->
                @auth
                    <div class="flex items-center">
                        <x-dropdown align="right" width="64">
                            <x-slot name="trigger">
                                <button class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="absolute top-1.5 right-1.5 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 text-[10px] text-white font-bold items-center justify-center">
                                                {{ auth()->user()->unreadNotifications->count() }}
                                            </span>
                                        </span>
                                    @endif
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-gray-700">
                                    Notifications
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-gray-50 dark:border-gray-700/50">
                                            <p class="text-sm text-gray-900 dark:text-white leading-tight">
                                                {{ $notification->data['message'] }}
                                            </p>
                                            <div class="mt-2 flex justify-between items-center text-[10px] text-gray-500">
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-primary-600 dark:text-primary-400 font-bold hover:underline italic">Mark as read</button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-4 py-6 text-center text-gray-400 italic text-sm">
                                            No unread notifications
                                        </div>
                                    @endforelse
                                </div>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="border-t border-gray-100 dark:border-gray-700">
                                        @csrf
                                        <button type="submit" class="block w-full text-center px-4 py-2 text-xs font-bold text-primary-600 hover:text-primary-700 transition">
                                            Mark all as read
                                        </button>
                                    </form>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth

                <!-- Settings / User Dropdown -->
                @auth
                    <div class="flex items-center ms-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="flex items-center ms-6 space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline uppercase font-bold tracking-widest">Login</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 dark:text-gray-500 underline uppercase font-bold tracking-widest">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Mobile: Hamburger Button -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>{{-- /flex row --}}
    </div>{{-- /max-w-7xl --}}

    <!-- ============================================================
         RESPONSIVE MOBILE MENU — renders below the bar when open
         ============================================================ -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Dark Mode Toggle (Mobile) -->
            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                <button
                    x-data="{
                        darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                        toggle() {
                            this.darkMode = !this.darkMode;
                            if (this.darkMode) {
                                document.documentElement.classList.add('dark');
                                localStorage.theme = 'dark';
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.theme = 'light';
                            }
                        }
                    }"
                    @click="toggle()"
                    class="flex items-center w-full text-left text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition"
                >
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-md me-3">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m3.343-5.657l.707.707m12.728 12.728l.707.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span x-text="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"></span>
                </button>
            </div>

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('products.view')
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                    {{ __('Products') }}
                </x-responsive-nav-link>
            @endcan

            @can('transactions.view')
                <x-responsive-nav-link :href="route('transactions.index')" :active="request()->routeIs('transactions.*')">
                    {{ __('Transactions') }}
                </x-responsive-nav-link>
            @endcan

            @can('ro.view')
                <x-responsive-nav-link :href="route('request-orders.index')" :active="request()->routeIs('request-orders.*')">
                    {{ __('Requests') }}
                </x-responsive-nav-link>
            @endcan

            @can('so.view')
                <x-responsive-nav-link :href="route('sales-orders.index')" :active="request()->routeIs('sales-orders.*')">
                    {{ __('Sales Orders') }}
                </x-responsive-nav-link>
            @endcan

            @can('invoices.view')
                <x-responsive-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                    {{ __('Invoices') }}
                </x-responsive-nav-link>
            @endcan

            @can('payments.view')
                <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">
                    {{ __('Payments') }}
                </x-responsive-nav-link>
            @endcan

            @can('employees.manage')
                <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                    {{ __('Employees') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                    {{ __('Roles') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        @endauth
    </div>{{-- /responsive mobile menu --}}

</nav>
