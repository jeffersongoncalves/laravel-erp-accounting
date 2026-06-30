<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentEntry;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentReconciliation;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->receivable = Account::factory()->ofType(RootType::Asset, AccountType::Receivable)->create(['company_id' => $this->company->id]);
    $this->income = Account::factory()->ofType(RootType::Income, AccountType::Income)->create(['company_id' => $this->company->id]);
});

function submittedSalesInvoice(float $rate): SalesInvoice
{
    $invoice = SalesInvoice::factory()->create([
        'company_id' => test()->company->id,
        'debit_to_id' => test()->receivable->id,
    ]);

    $invoice->items()->create([
        'item_code' => 'WIDGET',
        'qty' => 1,
        'rate' => $rate,
        'income_account_id' => test()->income->id,
    ]);

    $invoice->refresh()->submit();

    return $invoice->refresh();
}

it('reduces the invoice outstanding amount on submit', function () {
    $invoice = submittedSalesInvoice(100);
    $payment = PaymentEntry::factory()->create(['company_id' => $this->company->id]);

    expect($invoice->outstanding_amount)->toBe(100.0);

    $reconciliation = PaymentReconciliation::factory()->create([
        'company_id' => $this->company->id,
        'receivable_payable_account_id' => $this->receivable->id,
    ]);

    $reconciliation->allocations()->create([
        'payment_entry_id' => $payment->id,
        'invoice_type' => 'SalesInvoice',
        'invoice_id' => $invoice->id,
        'allocated_amount' => 30,
    ]);

    $reconciliation->submit();

    expect($reconciliation->docstatus)->toBe(DocStatus::Submitted)
        ->and($invoice->refresh()->outstanding_amount)->toBe(70.0);
});

it('clamps the invoice outstanding amount at zero', function () {
    $invoice = submittedSalesInvoice(100);
    $payment = PaymentEntry::factory()->create(['company_id' => $this->company->id]);

    $reconciliation = PaymentReconciliation::factory()->create([
        'company_id' => $this->company->id,
        'receivable_payable_account_id' => $this->receivable->id,
    ]);

    $reconciliation->allocations()->create([
        'payment_entry_id' => $payment->id,
        'invoice_type' => 'SalesInvoice',
        'invoice_id' => $invoice->id,
        'allocated_amount' => 250,
    ]);

    $reconciliation->submit();

    expect($invoice->refresh()->outstanding_amount)->toBe(0.0);
});
