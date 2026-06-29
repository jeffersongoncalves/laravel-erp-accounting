<?php

namespace JeffersonGoncalves\Erp\Accounting\Enums;

enum PaymentType: string
{
    case Receive = 'Receive';
    case Pay = 'Pay';
    case InternalTransfer = 'Internal Transfer';

    public function label(): string
    {
        return __('erp-accounting::erp-accounting.payment_type.'.$this->value);
    }
}
