<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\GlEntry;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->cash = Account::factory()->create(['company_id' => $this->company->id]);
    $this->sales = Account::factory()->create(['company_id' => $this->company->id]);
});

it('is registered as a singleton', function () {
    expect(app(GeneralLedgerService::class))
        ->toBe(app(GeneralLedgerService::class));
});

it('refuses to mutate the monetary impact of a posted entry', function () {
    $entry = balancedJournalEntry($this->company, $this->cash, $this->sales, 100);
    $entry->submit();

    $row = GlEntry::query()->first();
    $row->debit = 999;

    expect(fn () => $row->save())
        ->toThrow(DomainException::class, 'General ledger entries are immutable');
});

it('refuses to delete a ledger entry', function () {
    $entry = balancedJournalEntry($this->company, $this->cash, $this->sales, 100);
    $entry->submit();

    $row = GlEntry::query()->first();

    expect(fn () => $row->delete())
        ->toThrow(DomainException::class, 'General ledger entries cannot be deleted');
});

it('returns the debit-minus-credit balance of an account', function () {
    balancedJournalEntry($this->company, $this->cash, $this->sales, 100)->submit();
    balancedJournalEntry($this->company, $this->cash, $this->sales, 40)->submit();

    $gl = app(GeneralLedgerService::class);

    expect($gl->accountBalance($this->cash))->toBe(140.0)
        ->and($gl->accountBalance($this->sales))->toBe(-140.0);
});
