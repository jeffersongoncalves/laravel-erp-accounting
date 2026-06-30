<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\Dunning;
use JeffersonGoncalves\Erp\Accounting\Models\GlEntry;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->debitTo = Account::factory()->ofType(RootType::Asset, AccountType::Receivable)->create(['company_id' => $this->company->id]);
    $this->income = Account::factory()->ofType(RootType::Income, AccountType::Income)->create(['company_id' => $this->company->id]);
});

function dunningWithInterest(float $interest): Dunning
{
    return Dunning::factory()->create([
        'company_id' => test()->company->id,
        'total_interest' => $interest,
        'dunning_amount' => $interest,
        'income_account_id' => test()->income->id,
        'debit_to_account_id' => test()->debitTo->id,
    ]);
}

it('posts a balanced interest ledger on submit', function () {
    $dunning = dunningWithInterest(50);

    $dunning->submit();

    $gl = app(GeneralLedgerService::class);

    expect($dunning->docstatus)->toBe(DocStatus::Submitted)
        ->and($gl->accountBalance($this->debitTo))->toBe(50.0)
        ->and($gl->accountBalance($this->income))->toBe(-50.0)
        ->and((float) GlEntry::query()->sum('debit'))->toBe((float) GlEntry::query()->sum('credit'));
});

it('reverses the interest ledger on cancel', function () {
    $dunning = dunningWithInterest(50);
    $dunning->submit();
    $dunning->cancel();

    $gl = app(GeneralLedgerService::class);

    expect($dunning->docstatus)->toBe(DocStatus::Cancelled)
        ->and($gl->accountBalance($this->debitTo))->toBe(0.0)
        ->and($gl->accountBalance($this->income))->toBe(0.0);
});

it('posts nothing when there is no interest', function () {
    $dunning = dunningWithInterest(0);

    $dunning->submit();

    expect($dunning->docstatus)->toBe(DocStatus::Submitted)
        ->and(GlEntry::query()->count())->toBe(0);
});
