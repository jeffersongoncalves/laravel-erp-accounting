<div class="filament-hidden">

![Laravel ERP Accounting](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-accounting/main/art/jeffersongoncalves-laravel-erp-accounting.png)

</div>

# Laravel ERP Accounting

ERP accounting — chart of accounts, general ledger, journal/payment entries and invoices for the Laravel ERP ecosystem.

This package is the accounting / general-ledger module of the Laravel ERP ecosystem. It sits at the **base** of the transactional dependency graph: it depends only on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) and never on selling, buying or stock packages. Those packages depend on *it*.

## Features

- **Chart of Accounts** — Hierarchical accounts with `RootType` / `AccountType` classification, cost centers, and a configurable table tree
- **Double-Entry General Ledger** — A single `GeneralLedgerService` posts and reverses balanced ledger entries for every submittable voucher, enforcing `debit == credit`
- **Transaction Documents** — Journal entries, payment entries, sales invoices and purchase invoices, all built on the core `IsSubmittable` lifecycle (`Draft → Submitted → Cancelled`)
- **Immutable Ledger** — `gl_entries` rows can never have their monetary impact edited or be deleted; cancellation writes mirror rows so the net effect is zero
- **Masters** — Payment terms, modes of payment, tax templates, banks, bank accounts and budgets
- **Period Closing & Bank Reconciliation** — Period closing vouchers that zero out income/expense into a closing account, plus a lightweight bank transaction model
- **Customizable Models** — Override any model via config (ModelResolver pattern); `Account` and `GlEntry` ship swappable contracts
- **Translations** — English and Brazilian Portuguese

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-accounting
```

Publish and run the migrations (the core package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-accounting-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-accounting-config"
```

## Dynamic Party & Item Links

To keep accounting at the base of the dependency graph, transaction documents reference external parties and items **without hard foreign keys** — the `Customer`, `Supplier` and `Item` models live in other packages that may not be installed.

- **Party reference** = `party_type` (string, e.g. `'Customer'` / `'Supplier'`) + `party_id` (nullable unsigned big integer) + a denormalized `party_name` / `customer_name` / `supplier_name`. There is **no** FK constraint to a Customer or Supplier table.
- **Item reference** (on invoice lines) = `item_code` (string) + `item_name` (string) + the chosen GL account (`income_account_id` / `expense_account_id`). There is **no** FK to an Item model.

This mirrors the classic "Dynamic Link" fields: the ledger records *what* was transacted and *against whom* by value, while only the genuinely accounting-owned references (accounts, cost centers, companies) use real foreign keys.

## The General Ledger

`GeneralLedgerService` is the heart of the module and is registered as a singleton.

```php
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;

$gl = app(GeneralLedgerService::class);

// Post a balanced set of entries for a submittable voucher.
$gl->post($voucher, [
    ['account_id' => $ar->id,     'debit' => 110, 'credit' => 0],
    ['account_id' => $income->id, 'debit' => 0,   'credit' => 100],
    ['account_id' => $tax->id,    'debit' => 0,   'credit' => 10],
]);

// Unwind every entry of a voucher on cancellation (net zero).
$gl->reverse($voucher);

// Running balance (debit - credit), optionally up to a date.
$gl->accountBalance($account);
```

`post()` throws `DomainException('Debit and credit not balanced')` (compared on rounded 2-decimals) when the entries do not balance. Documents implementing `PostsToLedger` call these hooks automatically on `submit()` and `cancel()`.

### Posting logic

- **Sales Invoice** — `debit` the receivable (`debit_to`) for the grand total, `credit` each line's `income_account` for its amount, `credit` each tax account for its tax amount; `outstanding_amount` is set to the grand total.
- **Payment Entry** — *Receive*: `debit` `paid_to` (bank) and `credit` `paid_from` (receivable) for `paid_amount`; *Pay* is the inverse; *Internal Transfer* debits `paid_to` and credits `paid_from`.
- **Purchase Invoice** — mirror of the sales invoice: `credit` the payable (`credit_to`) for the grand total, `debit` each line's `expense_account` and each tax account.
- **Journal Entry** — posts one GL row per child account line exactly as entered, after validating that total debit equals total credit.

## Database Tables

All tables use the configured prefix shared with the core package (default: `erp_`): `accounts`, `cost_centers`, `payment_terms`, `modes_of_payment`, `tax_templates`, `tax_template_taxes`, `banks`, `bank_accounts`, `budgets`, `budget_accounts`, `gl_entries`, `journal_entries`, `journal_entry_accounts`, `payment_entries`, `sales_invoices`, `sales_invoice_items`, `sales_invoice_taxes`, `purchase_invoices`, `purchase_invoice_items`, `purchase_invoice_taxes`, `period_closing_vouchers`, `bank_transactions`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Simão Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
