<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property string $name
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, TaxWithholdingRate> $rates
 */
class TaxWithholdingCategory extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'tax_withholding_categories';
    }

    public function rates(): HasMany
    {
        return $this->hasMany(TaxWithholdingRate::class, 'tax_withholding_category_id');
    }
}
