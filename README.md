# Laravel ERP System

<div align="center">
  <h3>Enterprise-Grade ERP built on Laravel 12 + PHP 8.3</h3>
  <p>A production-ready financial and operational management platform with an immutable accounting core, advanced reconciliation, and a modular operational layer.</p>
</div>

---

## 📋 Table of Contents

- [Tech Stack](#-tech-stack)
- [Modules Overview](#-modules-overview)
  - [Home Launcher & Navigation](#-home-launcher--navigation)
  - [Dashboard & Analytics](#-dashboard--analytics)
  - [Inventory & Warehouse](#-inventory--warehouse-management)
  - [Order-to-Cash (O2C)](#-order-to-cash-workflow)
  - [Enterprise Accounting Engine](#-enterprise-accounting-engine)
  - [Bank Reconciliation Engine](#-bank-reconciliation-engine)
  - [Advanced Operational Layer](#-advanced-operational-layer)
  - [Security & RBAC](#-security--role-based-access-control)
- [Installation](#-installation)
- [Default Credentials](#-default-credentials)

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 12.52 |
| **Runtime** | PHP 8.3.6 |
| **Database** | SQLite (default) / PostgreSQL / MySQL |
| **CSS Engine** | Tailwind CSS |
| **Build Tool** | Vite |
| **Queue** | Laravel Queue (Database driver) |
| **UI Interactivity** | Alpine.js |

---

## 🧩 Modules Overview

### 🏠 Home Launcher & Navigation

A tile-based application launcher serves as the central hub for all modules.

- **RBAC-aware tiles** — each module card is only shown if the logged-in user has the required permission; no configuration needed
- **Live search filter** — instant JavaScript filtering across all visible tiles
- **Contextual sidebar** — module-aware collapsible sidebar that adapts its navigation links to the current section
- **Dark mode support** — system-preference aware theme across all layouts
- **Top bar** — user avatar, notification bell, and profile actions

---

### 📊 Dashboard & Analytics

Central KPI overview with real-time financial health of the business.

- **KPI Cards**: Total Revenue, Gross Profit, Total Expenses, Net Profit
- **Monthly Trend Chart**: Dynamic bar/line chart for 6-month revenue vs expense trend
- **Stock Level Chart**: Inventory value breakdown by category
- **Low Stock Alerts**: Products approaching reorder threshold across all warehouses
- **Cash Flow Summary**: Current-month cash in vs cash out with net position
- **Recent Activity Feed**: Latest transactions at a glance

---

### 📦 Inventory & Warehouse Management

- **Multi-warehouse support** — stock levels tracked per warehouse location
- **Product Catalog** — full CRUD with name, SKU, base price, cost, and stock tracking
- **Stock Movement Log** — every stock-in / stock-out recorded with timestamps and references
- **Automated Calculations** — stock value, margin, and reorder status computed automatically
- **Category Management** — hierarchical category system for product classification
- **Bulk Import / Export** — CSV import for products and stock levels; PDF/CSV export for reports

---

### 💰 Order-to-Cash Workflow

Complete O2C pipeline from customer request to cash received.

| Stage | Description |
|---|---|
| **Request Order** | Customer order intake with status tracking (Draft → Approved / Rejected) |
| **Sales Order** | Confirmation from Request Order; triggers automated Invoice creation |
| **Invoice** | Professional invoice with due dates, partial balance tracking |
| **Payment** | Multi-method payment recording; partial payments supported; auto-marks invoice paid |

- **Sequential numbering** — all documents (RO, SO, INV, PAY) use auto-generated sequential reference numbers
- **Multi-currency** — exchange rates pinned at transaction time to prevent rounding drift

---

### 🏦 Enterprise Accounting Engine

The core financial engine. All other modules post to this layer and never bypass it.

#### � Immutable Ledger
- Every `JournalEntry` carries a **SHA-256 hash** of its own data chained to the hash of the previous entry — identical in principle to a blockchain
- Any tampering is detectable via `php artisan ledger:verify`
- **Zero-edit policy**: corrections are handled exclusively via system-generated **reversal journal entries**, preserving a perfect audit trail

#### 📖 Double-Entry Core
- Full **Chart of Accounts (CoA)** with hierarchical account types (Asset, Liability, Equity, Revenue, Expense)
- Every financial event produces a balanced journal entry (∑ Debit = ∑ Credit) enforced at the service layer
- **Automated PostingEngine** creates journal entries for: Sales Orders, Invoices, Payments, Expenses, Reversals, and Reconciliation Adjustments

#### 📈 Accrual & Deferral Engine
- **Deferred Revenue**: income spread across future periods (e.g., annual subscriptions recognized monthly)
- **Prepaid Expenses**: costs deferred and expensed over their useful period
- **RecognitionEngine** runs scheduled jobs to post recognition entries on their due dates
- Full audit trail for every recognition event

#### ⚡ CQRS-lite Snapshot Optimization
- Closing an accounting period triggers `GenerateAccountSnapshots` background job
- Snapshots pre-aggregate `debit_total`, `credit_total`, and `balance` per account per period
- Historical balance lookups use snapshots + delta (O(1) instead of O(n) full ledger scan)
- Report generation sub-second even on large ledger datasets

#### 💱 Multi-Currency
- `CurrencyService` manages exchange rates
- Transactions store both the original amount and base-currency equivalent (`base_amount`)
- `HasMultiCurrency` trait enforces this pattern across models

#### 📑 Financial Reports
All reports generated by `AccountingReportService`:

| Report | Description |
|---|---|
| **Trial Balance** | All accounts with debit/credit totals for any period |
| **Balance Sheet** | Assets = Liabilities + Equity (snapshot-powered) |
| **Profit & Loss** | Revenue, COGS, Gross Profit, Expenses, Net Profit |
| **Cash Flow Report** | Cash In (Payments) vs Cash Out (Expenses) with monthly breakdown |
| **Profit Report** | Detailed margin and expense breakdown with PDF/CSV export |

---

### 🏦 Bank Reconciliation Engine

Reconcile imported bank statement CSV files against the General Ledger.

#### How It Works
1. Upload a CSV file from your bank via the **Import** modal
2. `ProcessBankStatementJob` processes the file in the background (queue-powered, 5-min timeout, 3 retries)
3. The **3-Tier Auto-Matching Engine** runs:
   - **Tier 1 – Exact**: Amount + Date + Reference match
   - **Tier 2 – Fuzzy**: Amount + Date within ±3 days
   - **Tier 3 – Pattern**: Invoice number extracted from description text using regex (`INV-xxx`)
4. Suggested matches are flagged; user reviews and confirms via the side-by-side matching UI
5. On confirmation, the match is recorded in `bank_reconciliations`

#### Edge Cases Handled
| Scenario | Resolution |
|---|---|
| **Nominal difference / bank fee** | Auto-posts `Dr Bank Charges / Cr Cash` adjustment journal to clear the gap |
| **Split payments** | Multiple journal entries linkable to a single statement line |
| **Unmatched lines** | Left in `unmatched` status for manual review |
| **Duplicate CSVs** | Each import generates a unique batch; lines are never duplicated across batches |

#### RBAC
Access requires the `bank.reconcile` permission.

---

### ⚙️ Advanced Operational Layer

Controls and workflow rules that sit between business operations and the accounting engine.

#### 💼 Department & Budget Control

- **Departments** — organisational units with a designated manager
- **Budgets** — per-department, per-account-code, per-accounting-period spending limits
- **Real-time enforcement** — `BudgetControlService::checkBudget()` is called automatically inside `PostingEngine::postExpense()` before any expense is posted; exceeding the budget throws `BudgetExceededException` and stops the transaction
- **Spent tracking** — `spent_amount` is automatically incremented after a successful posting

#### ✅ Multi-Level Approval Workflow

- **Approval Matrix** — configurable rules mapping document type + minimum amount to a required role and sequence number
- **ApprovalService** — creates a chain of `Approval` records when a document is submitted; each step must be approved in sequence
- **Polymorphic design** — works with any model (Purchase Request, Expense, etc.) via `approvable_type` / `approvable_id`
- **Auto-approval** — if no matrix rule matches the amount, the document is auto-approved

#### 🚦 Customer Credit Control

`CreditControlService` enforces two hard-stop rules before any sales order can be created:

| Rule | Threshold | Action |
|---|---|---|
| **AR Aging Block** | Any invoice > 60 days overdue | Hard block — throws `ArAgingBlockException` |
| **Credit Limit** | Outstanding balance + new order > credit limit | Hard block — throws `CreditLimitExceededException` |

- `getCustomerCreditSummary()` returns live utilization percentage for dashboard widgets

#### 📉 Cash Flow Forecasting

`CashFlowService` provides:
- **Monthly cash flow breakdown** (up to 6 months) for trend charts
- **Outstanding receivables** summary (all unpaid invoices)
- **Current-month stats** — revenue, expense, net position
- Designed for background pre-computation via queued jobs for dashboard performance

---

### 🛡️ Security & Role-Based Access Control

- **Roles & Permissions** — fully seeded RBAC with a many-to-many `role_permission` table
- **Permission slugs** — every route group and action is guarded by a named permission slug (e.g., `invoices.view`, `payments.create`, `bank.reconcile`)
- **`hasPermission($slug)`** — lightweight method on `User` model used throughout blade templates and middleware
- **`isAdmin()`** — shortcut for unrestricted admin access
- **Audit Logging** — `LogsActivity` trait on key models records created/updated events to an `activity_logs` table
- **In-App Notifications** — real-time alert system for order events and system changes

---

## 🔧 Installation

### Requirements
- PHP 8.3+
- Composer 2+
- Node.js 18+ & npm

### Steps

```bash
# 1. Clone and enter the directory
git clone <repository-url>
cd Project_1/laravel

# 2. Install all dependencies
composer install
npm install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Create and migrate the database
touch database/database.sqlite
php artisan migrate --seed

# 5. Set up queue worker (for background jobs)
php artisan queue:work --daemon &

# 6. Start development servers
npm run dev
# In a separate terminal:
php artisan serve
```

Visit **http://127.0.0.1:8000** to access the application.

---

## 🔑 Default Credentials

| Field | Value |
|---|---|
| Email | `admin@erp.com` |
| Password | `password` |
| Role | Administrator (all permissions) |

---

## 📂 Key Directory Structure

```
app/
├── Http/Controllers/
│   ├── BankReconciliationController.php  # Bank reconciliation endpoints
│   ├── DashboardController.php
│   ├── ExpenseController.php
│   ├── InvoiceController.php
│   ├── PaymentController.php
│   ├── SalesOrderController.php
│   └── ...
├── Jobs/
│   ├── GenerateAccountSnapshots.php      # CQRS snapshot generation
│   ├── ProcessBankStatementJob.php       # CSV import background job
│   ├── ProcessRecognitionEntries.php     # Accrual/deferral scheduler
│   └── ...
├── Models/
│   ├── BankStatement.php / BankStatementLine.php / BankReconciliation.php
│   ├── Budget.php / Department.php / Approval.php / ApprovalMatrix.php
│   ├── JournalEntry.php / JournalEntryLine.php
│   └── ...
└── Services/
    ├── AccountingService.php             # Journal entry creation & period management
    ├── ApprovalService.php               # Multi-level approval workflow
    ├── BankReconciliationService.php     # 3-tier matching engine
    ├── BudgetControlService.php          # Budget enforcement
    ├── CreditControlService.php          # AR aging & credit limit control
    ├── LedgerHasher.php                  # SHA-256 hash chain computation
    ├── PostingEngine.php                 # Automated double-entry posting
    ├── RecognitionEngine.php             # Accrual/deferral recognition
    ├── ReversalService.php               # Immutable correction via reversal
    └── SnapshotService.php               # CQRS balance snapshots
```

---

## 📜 License

This project is open-sourced software licensed under the [MIT license](LICENSE).
