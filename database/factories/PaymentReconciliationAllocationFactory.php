<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentEntry;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentReconciliation;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentReconciliationAllocation;

/** @extends Factory<PaymentReconciliationAllocation> */
class PaymentReconciliationAllocationFactory extends Factory
{
    protected $model = PaymentReconciliationAllocation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'payment_reconciliation_id' => PaymentReconciliation::factory(),
            'payment_entry_id' => PaymentEntry::factory(),
            'invoice_type' => 'SalesInvoice',
            'invoice_id' => 1,
            'allocated_amount' => fake()->randomFloat(2, 10, 1000),
        ];
    }
}
