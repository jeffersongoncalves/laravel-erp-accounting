<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\JournalEntryType;
use JeffersonGoncalves\Erp\Accounting\Enums\PaymentType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;

it('exposes the five root types', function () {
    expect(RootType::cases())->toHaveCount(5)
        ->and(RootType::Asset->value)->toBe('Asset')
        ->and(RootType::Income->label())->toBeString();
});

it('exposes the common account types', function () {
    expect(AccountType::Receivable->value)->toBe('Receivable')
        ->and(AccountType::CostOfGoodsSold->value)->toBe('Cost of Goods Sold')
        ->and(AccountType::StockReceivedButNotBilled->value)->toBe('Stock Received But Not Billed')
        ->and(AccountType::Receivable->label())->toBeString();
});

it('exposes the payment types', function () {
    expect(PaymentType::Receive->value)->toBe('Receive')
        ->and(PaymentType::InternalTransfer->value)->toBe('Internal Transfer');
});

it('exposes the journal entry types', function () {
    expect(JournalEntryType::JournalEntry->value)->toBe('Journal Entry')
        ->and(JournalEntryType::ExchangeRateRevaluation->value)->toBe('Exchange Rate Revaluation');
});
