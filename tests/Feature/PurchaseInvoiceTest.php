<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\GlEntry;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoice;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->creditTo = Account::factory()->ofType(RootType::Liability, AccountType::Payable)->create(['company_id' => $this->company->id]);
    $this->expense = Account::factory()->ofType(RootType::Expense, AccountType::ExpenseAccount)->create(['company_id' => $this->company->id]);
    $this->tax = Account::factory()->ofType(RootType::Asset, AccountType::Tax)->create(['company_id' => $this->company->id]);
});

function draftPurchaseInvoice(): PurchaseInvoice
{
    $invoice = PurchaseInvoice::factory()->create([
        'company_id' => test()->company->id,
        'credit_to_id' => test()->creditTo->id,
    ]);

    $invoice->items()->create([
        'item_code' => 'RAW',
        'qty' => 4,
        'rate' => 25,
        'expense_account_id' => test()->expense->id,
    ]);

    $invoice->taxes()->create([
        'account_id' => test()->tax->id,
        'rate' => 5,
        'tax_amount' => 5,
    ]);

    return $invoice->refresh();
}

it('computes totals from items and taxes', function () {
    $invoice = draftPurchaseInvoice();

    expect($invoice->net_total)->toBe(100.0)
        ->and($invoice->total_taxes)->toBe(5.0)
        ->and($invoice->grand_total)->toBe(105.0);
});

it('posts a balanced ledger crediting AP and debiting expense and tax', function () {
    $invoice = draftPurchaseInvoice();

    $invoice->submit();

    $gl = app(GeneralLedgerService::class);

    expect($invoice->docstatus)->toBe(DocStatus::Submitted)
        ->and($gl->accountBalance($this->creditTo))->toBe(-105.0)
        ->and($gl->accountBalance($this->expense))->toBe(100.0)
        ->and($gl->accountBalance($this->tax))->toBe(5.0)
        ->and((float) GlEntry::query()->sum('debit'))->toBe((float) GlEntry::query()->sum('credit'));
});

it('reverses the ledger on cancel', function () {
    $invoice = draftPurchaseInvoice();
    $invoice->submit();
    $invoice->cancel();

    $gl = app(GeneralLedgerService::class);

    expect($gl->accountBalance($this->creditTo))->toBe(0.0)
        ->and($gl->accountBalance($this->expense))->toBe(0.0);
});
