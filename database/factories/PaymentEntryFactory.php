<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\PaymentType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\PaymentEntry;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<PaymentEntry> */
class PaymentEntryFactory extends Factory
{
    protected $model = PaymentEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 100, 10000);

        return [
            'payment_type' => PaymentType::Receive,
            'posting_date' => fake()->date(),
            'company_id' => Company::factory(),
            'paid_from_id' => Account::factory(),
            'paid_to_id' => Account::factory(),
            'paid_amount' => $amount,
            'received_amount' => $amount,
        ];
    }
}
