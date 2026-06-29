<?php

namespace JeffersonGoncalves\Erp\Accounting\Enums;

enum RootType: string
{
    case Asset = 'Asset';
    case Liability = 'Liability';
    case Equity = 'Equity';
    case Income = 'Income';
    case Expense = 'Expense';

    public function label(): string
    {
        return __('erp-accounting::erp-accounting.root_type.'.$this->value);
    }
}
