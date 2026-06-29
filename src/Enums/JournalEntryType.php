<?php

namespace JeffersonGoncalves\Erp\Accounting\Enums;

enum JournalEntryType: string
{
    case JournalEntry = 'Journal Entry';
    case BankEntry = 'Bank Entry';
    case CashEntry = 'Cash Entry';
    case CreditCardEntry = 'Credit Card Entry';
    case OpeningEntry = 'Opening Entry';
    case Depreciation = 'Depreciation';
    case ExchangeRateRevaluation = 'Exchange Rate Revaluation';

    public function label(): string
    {
        return __('erp-accounting::erp-accounting.journal_entry_type.'.$this->value);
    }
}
