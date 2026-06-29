<?php

namespace JeffersonGoncalves\Erp\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Enums\JournalEntryType;
use JeffersonGoncalves\Erp\Accounting\Models\JournalEntry;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<JournalEntry> */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'posting_date' => fake()->date(),
            'company_id' => Company::factory(),
            'voucher_type' => JournalEntryType::JournalEntry,
            'user_remark' => fake()->optional()->sentence(),
        ];
    }
}
