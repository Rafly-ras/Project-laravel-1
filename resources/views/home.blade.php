<x-launcher-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- Welcome Header -->
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight sm:text-4xl mb-4">
                Welcome back, <span class="text-indigo-600">{{ Auth::user()->name }}</span>
            </h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Select an application to start managing your enterprise operations.
            </p>
        </div>

        <!-- Search & Utils -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <x-ui.search-bar />
            
            <div class="flex items-center gap-3">
                <!-- Short-cut or Status Indicators can go here -->
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 me-2 animate-pulse"></span>
                    System Online
                </span>
            </div>
        </div>

        <!-- Favorites / Recent (Optional for now, but placeholder) -->
        {{-- 
        <div class="mb-12">
            <h2 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                Favorite Apps
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                 <!-- Favorite cards would go here -->
            </div>
        </div> 
        --}}

        <!-- All Applications Grid -->
        <div>
            <h2 class="text-sm font-bold uppercase tracking-widest text-gray-400 mb-6 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Applications
            </h2>
            
            @if(count($modules) > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6" id="app-grid">
                    @foreach($modules as $module)
                        <x-ui.app-card 
                            :name="$module['name']" 
                            :description="$module['description']" 
                            :icon="$module['icon']" 
                            :route="$module['route']"
                            :color="$module['color']"
                        />
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-12 text-center border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Access Available</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        Your account does not have permissions to any modules yet. Please contact your system administrator.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-launcher-layout>
