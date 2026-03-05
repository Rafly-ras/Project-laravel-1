<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the application launcher.
     */
    public function index()
    {
        $modules = [
            [
                'name' => 'Dashboard',
                'description' => 'Real-time stats and overviews',
                'icon' => 'dashboard',
                'route' => 'dashboard',
                'permission' => 'reports.view',
                'color' => 'bg-emerald-600',
            ],
            [
                'name' => 'Inventory',
                'description' => 'Stock, Products, Warehouses',
                'icon' => 'cube',
                'route' => 'products.index',
                'permission' => 'products.view',
                'color' => 'bg-blue-500',
            ],
            [
                'name' => 'Sales',
                'description' => 'Orders, Invoices, Payments',
                'icon' => 'shopping-cart',
                'route' => 'sales-orders.index',
                'permission' => 'so.view',
                'color' => 'bg-emerald-500',
            ],
            [
                'name' => 'Accounting',
                'description' => 'Invoices, Ledger, Snapshots',
                'icon' => 'banknotes',
                'route' => 'invoices.index',
                'permission' => 'invoices.view',
                'color' => 'bg-indigo-500',
            ],
            [
                'name' => 'Expenses',
                'description' => 'Expense tracking, Categories',
                'icon' => 'credit-card',
                'route' => 'expenses.index',
                'permission' => 'reports.view', 
                'color' => 'bg-red-500',
            ],
            [
                'name' => 'Reports',
                'description' => 'Financial analysis, Cashflow',
                'icon' => 'chart-bar',
                'route' => 'reports.cashflow',
                'permission' => 'reports.view',
                'color' => 'bg-amber-500',
            ],
            [
                'name' => 'Settings',
                'description' => 'Employees, Roles, System',
                'icon' => 'cog',
                'route' => 'employees.index',
                'permission' => 'employees.manage',
                'color' => 'bg-slate-600',
            ],
        ];

        // Filter modules by permission
        $accessibleModules = array_filter($modules, function ($module) {
            return Auth::user()->hasPermission($module['permission']);
        });

        return view('home', ['modules' => $accessibleModules]);
    }
}
