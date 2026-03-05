@php
    $segment = Request::segment(1);
    
    // Module detection logic
    $isInventory = in_array($segment, ['products', 'warehouses', 'categories', 'transactions']);
    $isSales = in_array($segment, ['sales-orders', 'request-orders']);
    $isAccounting = in_array($segment, ['invoices', 'payments']);
    $isFinance = $segment === 'finance';
    $isReports = $segment === 'reports';
    $isManagement = in_array($segment, ['employees', 'roles', 'profile']);
    
    // Default to Dashboard if no specific module segment matches
    if (!$isInventory && !$isSales && !$isAccounting && !$isFinance && !$isReports && !$isManagement) {
        $isInventory = true; // Fallback or show all for Dashboard? 
        // User said: "Inventory clicked -> show inventory. Sales clicked -> show sales."
        // We'll show the module the user is currently in.
    }
@endphp

<aside 
    class="fixed inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white dark:bg-gray-900 border-e border-gray-100 dark:border-gray-800"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'w-20' : 'w-64'
    ]"
>
    <!-- Header Section -->
    <div class="h-16 flex items-center px-4 shrink-0 border-b border-gray-50 dark:border-gray-800 justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 overflow-hidden">
            <x-application-logo class="w-8 h-8 shrink-0 fill-current text-indigo-600" />
            <span class="font-bold text-gray-900 dark:text-white transition-opacity duration-300 whitespace-nowrap" 
                  x-show="!sidebarCollapsed" x-transition:enter="delay-150">
                Enterprise <span class="text-indigo-600">ERP</span>
            </span>
        </a>
    </div>

    <!-- App Switcher (Back to Launcher) -->
    <div class="px-3 py-4 border-b border-gray-50 dark:border-gray-800">
        <a href="{{ route('home') }}" 
           class="flex items-center gap-3 p-2 rounded-xl bg-gray-50 dark:bg-gray-800/50 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200 group">
            <div class="w-8 h-8 bg-white dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow group-hover:scale-105 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </div>
            <span class="text-xs font-bold uppercase tracking-wider" x-show="!sidebarCollapsed">Switch Module</span>
        </a>
    </div>

    <!-- Navigation Area -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 space-y-8 scrollbar-hide">
        
        <!-- Dashboard (Persistent if we want, or contextual) -->
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                General
            </div>
            <div class="space-y-1">
                <x-ui.sidebar-item :href="route('dashboard')" label="Dashboard" icon="dashboard" :active="request()->routeIs('dashboard')" />
            </div>
        </div>

        <!-- Inventory Module -->
        @if($isInventory)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                Inventory Management
            </div>
            <div class="space-y-1">
                @can('products.view')
                    <x-ui.sidebar-item :href="route('products.index')" label="Products" icon="inventory" :active="request()->routeIs('products.*')" />
                    <x-ui.sidebar-item :href="route('warehouses.index')" label="Warehouses" icon="inventory" :active="request()->routeIs('warehouses.*')" />
                    <x-ui.sidebar-item :href="route('categories.index')" label="Categories" icon="inventory" :active="request()->routeIs('categories.*')" />
                @endcan
                @can('transactions.view')
                    <x-ui.sidebar-item :href="route('transactions.index')" label="Transactions" icon="inventory" :active="request()->routeIs('transactions.*')" />
                @endcan
            </div>
        </div>
        @endif

        <!-- Sales Module -->
        @if($isSales)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                Sales Operations
            </div>
            <div class="space-y-1">
                @can('ro.view')
                    <x-ui.sidebar-item :href="route('request-orders.index')" label="Requests" icon="sales" :active="request()->routeIs('request-orders.*')" />
                @endcan
                @can('so.view')
                    <x-ui.sidebar-item :href="route('sales-orders.index')" label="Orders" icon="sales" :active="request()->routeIs('sales-orders.*')" />
                @endcan
            </div>
        </div>
        @endif

        <!-- Accounting Module -->
        @if($isAccounting)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                Finance & Ledger
            </div>
            <div class="space-y-1">
                @can('invoices.view')
                    <x-ui.sidebar-item :href="route('invoices.index')" label="Invoices" icon="accounting" :active="request()->routeIs('invoices.*')" />
                @endcan
                @can('payments.view')
                    <x-ui.sidebar-item :href="route('payments.index')" label="Payments" icon="accounting" :active="request()->routeIs('payments.*')" />
                @endcan
            </div>
        </div>
        @endif

        <!-- Finance (Expenses) Module -->
        @if($isFinance)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                Expenses Tracking
            </div>
            <div class="space-y-1">
                <x-ui.sidebar-item :href="route('expenses.index')" label="Expenses" icon="expenses" :active="request()->routeIs('expenses.*')" />
                <x-ui.sidebar-item :href="route('expense-categories.index')" label="Categories" icon="expenses" :active="request()->routeIs('expense-categories.*')" />
            </div>
        </div>
        @endif

        <!-- Reports Module -->
        @if($isReports)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                Financial Analysis
            </div>
            <div class="space-y-1">
                @can('reports.cashflow')
                    <x-ui.sidebar-item :href="route('reports.cashflow')" label="Cash Flow" icon="reports" :active="request()->routeIs('reports.cashflow')" />
                @endcan
                @can('reports.profit')
                    <x-ui.sidebar-item :href="route('reports.profit')" label="Profit & Loss" icon="reports" :active="request()->routeIs('reports.profit')" />
                @endcan
            </div>
        </div>
        @endif

        <!-- Administration / Settings -->
        @if($isManagement)
        <div>
            <div class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest transition-opacity duration-300"
                 :class="sidebarCollapsed ? 'opacity-0' : 'opacity-100'">
                Administration
            </div>
            <div class="space-y-1">
                @can('employees.manage')
                    <x-ui.sidebar-item :href="route('employees.index')" label="Employees" icon="settings" :active="request()->routeIs('employees.*')" />
                    <x-ui.sidebar-item :href="route('roles.index')" label="Roles" icon="settings" :active="request()->routeIs('roles.*')" />
                @endcan
                <x-ui.sidebar-item :href="route('profile.edit')" label="Profile" icon="settings" :active="request()->routeIs('profile.*')" />
            </div>
        </div>
        @endif

    </div>

    <!-- Bottom Footer / Collapse Toggle for Mobile -->
    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 lg:hidden text-center">
        <button @click="sidebarOpen = false" class="text-xs font-bold text-gray-500 hover:text-indigo-600 transition">
            Close Menu
        </button>
    </div>
</aside>

<!-- Mobile Overlay -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false" 
    class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
    x-transition:enter="transition-opacity ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
></div>
