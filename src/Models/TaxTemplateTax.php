<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;

/**
 * @property int $id
 * @property int $tax_template_id
 * @property int $account_id
 * @property float $rate
 * @property string|null $description
 * @property string $charge_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TaxTemplate $taxTemplate
 * @property-read Account $account
 */
class TaxTemplateTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_template_id',
        'account_id',
        'rate',
        'description',
        'charge_type',
    ];

    protected $casts = [
        'rate' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'tax_template_taxes';
    }

    public function taxTemplate(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::taxTemplate(), 'tax_template_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'account_id');
    }
}
