<?php

namespace JeffersonGoncalves\Erp\Accounting\Enums;

enum AccountType: string
{
    case Bank = 'Bank';
    case Cash = 'Cash';
    case Receivable = 'Receivable';
    case Payable = 'Payable';
    case Tax = 'Tax';
    case Stock = 'Stock';
    case StockReceivedButNotBilled = 'Stock Received But Not Billed';
    case CostOfGoodsSold = 'Cost of Goods Sold';
    case FixedAsset = 'Fixed Asset';
    case Depreciation = 'Depreciation';
    case ExpensesIncludedInValuation = 'Expenses Included In Valuation';
    case Income = 'Income';
    case ExpenseAccount = 'Expense Account';
    case Chargeable = 'Chargeable';
    case RoundOff = 'Round Off';
    case Equity = 'Equity';
    case Temporary = 'Temporary';

    public function label(): string
    {
        return __('erp-accounting::erp-accounting.account_type.'.$this->value);
    }
}
