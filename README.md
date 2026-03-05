# Laravel-ERP-Project-1

A comprehensive Enterprise Resource Planning (ERP) and Inventory Management system built with **Laravel 12.52** and **PHP 8.3**.

## 🚀 Key Modules & Features

### 📊 Dashboard & Analytics
- **Real-time Metrics**: KPI cards for Total Revenue, Gross Profit, Expenses, and Net Profit.
- **Dynamic Charts**: Interactive visualizations for stock levels by category and monthly transaction trends.
- **Low Stock Monitoring**: Quick overview of products requiring restock across all warehouses.

### 📦 Inventory & Warehouse Management
- **Multi-Warehouse Support**: Management of multiple stock locations with warehouse-specific stock levels.
- **Stock Movement Tracking**: Record-keeping for all product transactions (stock-in/stock-out).
- **Automated Calculations**: Automatic stock value and margin calculations.
- **Imports & Exports**: Bulk product importing and stock summary exports in CSV and PDF formats.

### 💰 Order-to-Cash (O2C) Workflow
- **Request Orders**: Full lifecycle management from draft status to approved/rejected and eventual conversion.
- **Sales Orders**: Streamlined order confirmation and automated generation of linked invoices.
- **Automated Invoicing**: Professional invoice tracking with due date monitoring and automated balance updates.
- **Payment Processing**: Integrated payment tracking with support for multiple payment methods and partial payments.

### 🏦 Enterprise Accounting Engine
- **Double-Entry Core**: Full compliance with standard accounting principles using a hierarchical Chart of Accounts (CoA).
- **🛡️ Immutable Ledger**: Cryptographic sequential SHA-256 hash chaining ensuring every journal entry is tamper-proof and traceable.
- **Zero-Edit Policy**: Strict enforcement of immutability where corrections are handled via **Automated Reversals** to maintain a perfect audit trail.
- **📈 Accrual & Deferral Engine**: Automated scheduling and posting for deferred revenue and prepaid expenses over time (e.g., monthly service recognition).
- **⚡ CQRS-lite Optimization**: High-performance reporting using **Account Snapshots**, enabling O(1) balance lookups for historical data.
- **Multi-Currency Clarity**: Real-time conversion and pinning of exchange rates at the transaction level to prevent financial "penny-drift."
- **Integrated Reporting**: Sub-second generation of Trial Balance, Balance Sheet, and Profit & Loss statements.

### 🛡️ Security & Administration
- **RBAC (Role-Based Access Control)**: Fine-grained permission system controlling access to every module and action.
- **Audit Logging**: Comprehensive activity logs tracking critical user actions for accountability.
- **In-App Notifications**: Real-time alerts for system events and order status changes.

## 🛠️ Technical Stack
- **Framework**: [Laravel 12.52.0](https://laravel.com)
- **Runtime**: [PHP 8.3.6](https://php.net)
- **Database**: [SQLite](https://sqlite.org) (Default) / Compatible with PostgreSQL and MySQL
- **CSS Engine**: [Tailwind CSS](https://tailwindcss.com)
- **Build Tool**: [Vite](https://vitejs.dev)

## 🔧 Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd Project_1/laravel
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize database**:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Run the application**:
   ```bash
   npm run dev
   # In a separate terminal
   php artisan serve
   ```
