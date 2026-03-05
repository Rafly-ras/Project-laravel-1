<header class="h-16 flex items-center justify-between px-4 sm:px-8 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 sticky top-0 z-40">
    <div class="flex items-center gap-4">
        <!-- Sidebar Toggle (Mobile) -->
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-400 hover:text-gray-500 lg:hidden">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Sidebar Toggle (Desktop) -->
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden lg:block p-2 rounded-md text-gray-400 hover:text-gray-500 transition-colors">
            <svg class="h-6 w-6 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>

        <!-- Breadcrumbs or Module Title -->
        <div class="hidden md:flex items-center text-sm font-medium text-gray-500 gap-2">
            <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">ERP</a>
            <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
            <span class="text-gray-900 dark:text-white capitalize">{{ Request::segment(1) ?: 'Dashboard' }}</span>
        </div>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-2 sm:gap-4">
        
        <!-- Dark Mode -->
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
            class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
        >
            <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <svg x-show="darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m3.343-5.657l.707.707m12.728 12.728l.707.707M6.343 17.657l-.707.707M17.657 6.343l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>

        <!-- Notifications (Migrated from navigation.blade.php) -->
        <div class="relative">
            <x-dropdown align="right" width="64">
                <x-slot name="trigger">
                    <button class="relative p-2 text-gray-400 hover:text-indigo-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 h-4 w-4 rounded-full bg-rose-500 text-[10px] text-white font-bold flex items-center justify-center border-2 border-white dark:border-gray-800">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>
                </x-slot>
                <x-slot name="content">
                    <!-- Notification content remains similar to previous implementation but with refined UI -->
                    <div class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-50 dark:border-gray-700">Notifications</div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse(auth()->user()->unreadNotifications->take(5) as $n)
                            <div class="p-4 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition truncate">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $n->data['message'] }}</p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400 italic text-sm">Clear as a summer sky</div>
                        @endforelse
                    </div>
                </x-slot>
            </x-dropdown>
        </div>

        <!-- User Menu -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span class="hidden lg:block text-sm font-semibold">{{ Auth::user()->name }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">Account Settings</x-dropdown-link>
                <x-dropdown-link :href="route('home')">App Launcher</x-dropdown-link>
                <div class="border-t border-gray-100 dark:border-gray-700"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Sign Out
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>
