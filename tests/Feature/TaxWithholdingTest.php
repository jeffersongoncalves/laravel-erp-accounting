<?php

use JeffersonGoncalves\Erp\Accounting\Models\TaxWithholdingCategory;
use JeffersonGoncalves\Erp\Accounting\Services\TaxWithholdingService;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->category = TaxWithholdingCategory::factory()->create(['company_id' => $this->company->id]);

    $this->category->rates()->create([
        'from_date' => '2024-01-01',
        'to_date' => '2024-12-31',
        'tax_rate' => 10,
        'single_threshold' => 1000,
    ]);

    $this->category->rates()->create([
        'from_date' => '2025-01-01',
        'to_date' => null,
        'tax_rate' => 5,
        'single_threshold' => 2000,
    ]);
});

it('resolves the rate effective on a given date', function () {
    $service = app(TaxWithholdingService::class);

    expect($service->rateOn($this->category, '2024-06-15')->tax_rate)->toBe(10.0)
        ->and($service->rateOn($this->category, '2025-06-15')->tax_rate)->toBe(5.0);
});

it('returns null when no rate covers the date', function () {
    $service = app(TaxWithholdingService::class);

    expect($service->rateOn($this->category, '2023-06-15'))->toBeNull();
});

it('withholds nothing at or below the single threshold', function () {
    $service = app(TaxWithholdingService::class);

    expect($service->computeWithholding(1000, $this->category, '2024-06-15'))->toBe(0.0)
        ->and($service->computeWithholding(500, $this->category, '2024-06-15'))->toBe(0.0);
});

it('withholds base times rate over the single threshold', function () {
    $service = app(TaxWithholdingService::class);

    expect($service->computeWithholding(5000, $this->category, '2024-06-15'))->toBe(500.0);
});

it('returns zero when no rate is effective', function () {
    $service = app(TaxWithholdingService::class);

    expect($service->computeWithholding(5000, $this->category, '2023-06-15'))->toBe(0.0);
});
