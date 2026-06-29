<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\GlEntry;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->debitTo = Account::factory()->ofType(RootType::Asset, AccountType::Receivable)->create(['company_id' => $this->company->id]);
    $this->income = Account::factory()->ofType(RootType::Income, AccountType::Income)->create(['company_id' => $this->company->id]);
    $this->tax = Account::factory()->ofType(RootType::Liability, AccountType::Tax)->create(['company_id' => $this->company->id]);
});

function draftSalesInvoice(): SalesInvoice
{
    $invoice = SalesInvoice::factory()->create([
        'company_id' => test()->company->id,
        'debit_to_id' => test()->debitTo->id,
    ]);

    $invoice->items()->create([
        'item_code' => 'WIDGET',
        'qty' => 2,
        'rate' => 50,
        'income_account_id' => test()->income->id,
    ]);

    $invoice->taxes()->create([
        'account_id' => test()->tax->id,
        'rate' => 10,
        'tax_amount' => 10,
    ]);

    return $invoice->refresh();
}

it('computes net, tax and grand totals', function () {
    $invoice = draftSalesInvoice();

    expect($invoice->net_total)->toBe(100.0)
        ->and($invoice->total_taxes)->toBe(10.0)
        ->and($invoice->grand_total)->toBe(110.0)
        ->and($invoice->outstanding_amount)->toBe(110.0);
});

it('posts a balanced ledger debiting AR and crediting income and tax', function () {
    $invoice = draftSalesInvoice();

    $invoice->submit();

    $gl = app(GeneralLedgerService::class);

    expect($invoice->docstatus)->toBe(DocStatus::Submitted)
        ->and($gl->accountBalance($this->debitTo))->toBe(110.0)
        ->and($gl->accountBalance($this->income))->toBe(-100.0)
        ->and($gl->accountBalance($this->tax))->toBe(-10.0)
        ->and((float) GlEntry::query()->sum('debit'))->toBe((float) GlEntry::query()->sum('credit'));
});

it('reverses the ledger on cancel', function () {
    $invoice = draftSalesInvoice();
    $invoice->submit();
    $invoice->cancel();

    $gl = app(GeneralLedgerService::class);

    expect($gl->accountBalance($this->debitTo))->toBe(0.0)
        ->and($gl->accountBalance($this->income))->toBe(0.0);
});
