<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\Bank;
use JeffersonGoncalves\Erp\Accounting\Models\BankAccount;
use JeffersonGoncalves\Erp\Accounting\Models\BankTransaction;
use JeffersonGoncalves\Erp\Accounting\Models\Budget;
use JeffersonGoncalves\Erp\Accounting\Models\BudgetAccount;
use JeffersonGoncalves\Erp\Accounting\Models\CostCenter;
use JeffersonGoncalves\Erp\Accounting\Models\GlEntry;
use JeffersonGoncalves\Erp\Accounting\Models\JournalEntry;
use JeffersonGoncalves\Erp\Accounting\Models\JournalEntryAccount;
use JeffersonGoncalves\Erp\Accounting\Models\ModeOfPayment;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentEntry;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentTerm;
use JeffersonGoncalves\Erp\Accounting\Models\PeriodClosingVoucher;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoice;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoiceItem;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoiceTax;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoiceItem;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoiceTax;
use JeffersonGoncalves\Erp\Accounting\Models\TaxTemplate;
use JeffersonGoncalves\Erp\Accounting\Models\TaxTemplateTax;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package. This is shared with
    | laravel-erp-core so that foreign keys across the ERP ecosystem resolve
    | against a single set of prefixed tables. Set to null to disable.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior. Swappable models that ship a contract must implement it
    | (see src/Models/Contracts/).
    |
    */
    'models' => [
        'account' => Account::class,
        'cost_center' => CostCenter::class,
        'payment_term' => PaymentTerm::class,
        'mode_of_payment' => ModeOfPayment::class,
        'tax_template' => TaxTemplate::class,
        'tax_template_tax' => TaxTemplateTax::class,
        'bank' => Bank::class,
        'bank_account' => BankAccount::class,
        'budget' => Budget::class,
        'budget_account' => BudgetAccount::class,
        'gl_entry' => GlEntry::class,
        'journal_entry' => JournalEntry::class,
        'journal_entry_account' => JournalEntryAccount::class,
        'payment_entry' => PaymentEntry::class,
        'sales_invoice' => SalesInvoice::class,
        'sales_invoice_item' => SalesInvoiceItem::class,
        'sales_invoice_tax' => SalesInvoiceTax::class,
        'purchase_invoice' => PurchaseInvoice::class,
        'purchase_invoice_item' => PurchaseInvoiceItem::class,
        'purchase_invoice_tax' => PurchaseInvoiceTax::class,
        'period_closing_voucher' => PeriodClosingVoucher::class,
        'bank_transaction' => BankTransaction::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Default currency used by transaction documents and the account used to
    | absorb rounding differences when posting to the general ledger.
    |
    */
    'default_currency' => 'USD',

    'round_off_account' => null,
];
