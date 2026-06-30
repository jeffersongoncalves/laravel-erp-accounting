<?php

use JeffersonGoncalves\Erp\Accounting\Models\AccountingDimension;
use JeffersonGoncalves\Erp\Accounting\Models\AccountingDimensionValue;

it('creates a dimension with its defaults', function () {
    $dimension = AccountingDimension::factory()->create([
        'label' => 'Project',
        'reference_document' => 'Project',
    ]);

    expect($dimension->is_mandatory)->toBeFalse()
        ->and($dimension->disabled)->toBeFalse()
        ->and($dimension->reference_document)->toBe('Project');
});

it('has many values', function () {
    $dimension = AccountingDimension::factory()->create();

    $dimension->values()->create(['value' => 'Alpha', 'description' => 'First']);
    $dimension->values()->create(['value' => 'Beta']);

    expect($dimension->values)->toHaveCount(2)
        ->and($dimension->values->first())->toBeInstanceOf(AccountingDimensionValue::class)
        ->and($dimension->values->first()->dimension->id)->toBe($dimension->id);
});
