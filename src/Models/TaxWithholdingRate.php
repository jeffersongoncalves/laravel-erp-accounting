<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tax_withholding_category_id
 * @property Carbon $from_date
 * @property Carbon|null $to_date
 * @property float $tax_rate
 * @property float $single_threshold
 * @property float $cumulative_threshold
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TaxWithholdingCategory|null $category
 */
class TaxWithholdingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_withholding_category_id',
        'from_date',
        'to_date',
        'tax_rate',
        'single_threshold',
        'cumulative_threshold',
    ];

    protected $attributes = [
        'tax_rate' => 0,
        'single_threshold' => 0,
        'cumulative_threshold' => 0,
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'tax_rate' => 'float',
        'single_threshold' => 'float',
        'cumulative_threshold' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'tax_withholding_rates';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TaxWithholdingCategory::class, 'tax_withholding_category_id');
    }
}
