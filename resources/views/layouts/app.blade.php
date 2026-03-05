<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ERP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            // Theme initial state
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>

        <style>
            [x-cloak] { display: none !important; }
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 overflow-hidden" 
          x-data="{ 
            sidebarOpen: false, 
            sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true' 
          }"
          x-init="$watch('sidebarCollapsed', val => localStorage.setItem('sidebarCollapsed', val))"
          x-cloak>
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- Sidebar -->
            <x-ui.sidebar />

            <!-- Content Area -->
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden transition-all duration-300"
                 :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
                
                <!-- Topbar -->
                <x-ui.topbar />

                <!-- Main Section -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden p-6 lg:p-10 relative scrollbar-hide">
                    @isset($header)
                        <div class="mb-10 animate-fade-in-down">
                            {{ $header }}
                        </div>
                    @endisset

                    <div class="max-w-7xl mx-auto space-y-10 animate-fade-in">
                        {{ $slot }}
                    </div>
                </main>
                
                <footer class="h-10 flex items-center justify-center text-[10px] text-gray-400 border-t border-gray-100 dark:border-gray-800 shrink-0">
                    &copy; {{ date('Y') }} Enterprise ERP Platform. Powered by Laravel 12.
                </footer>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
