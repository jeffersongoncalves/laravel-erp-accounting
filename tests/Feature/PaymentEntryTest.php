<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\PaymentType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentEntry;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->bank = Account::factory()->ofType(RootType::Asset, AccountType::Bank)->create(['company_id' => $this->company->id]);
    $this->receivable = Account::factory()->ofType(RootType::Asset, AccountType::Receivable)->create(['company_id' => $this->company->id]);
});

it('debits the bank and credits the receivable on a received payment', function () {
    $payment = PaymentEntry::factory()->create([
        'company_id' => $this->company->id,
        'payment_type' => PaymentType::Receive,
        'paid_from_id' => $this->receivable->id,
        'paid_to_id' => $this->bank->id,
        'paid_amount' => 500,
    ]);

    $payment->submit();

    $gl = app(GeneralLedgerService::class);

    expect($gl->accountBalance($this->bank))->toBe(500.0)
        ->and($gl->accountBalance($this->receivable))->toBe(-500.0);
});

it('reverses the bank and receivable on cancel', function () {
    $payment = PaymentEntry::factory()->create([
        'company_id' => $this->company->id,
        'payment_type' => PaymentType::Receive,
        'paid_from_id' => $this->receivable->id,
        'paid_to_id' => $this->bank->id,
        'paid_amount' => 500,
    ]);

    $payment->submit();
    $payment->cancel();

    $gl = app(GeneralLedgerService::class);

    expect($gl->accountBalance($this->bank))->toBe(0.0)
        ->and($gl->accountBalance($this->receivable))->toBe(0.0);
});
