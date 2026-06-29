<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int $fiscal_year_id
 * @property int|null $cost_center_id
 * @property int|null $company_id
 * @property bool $applicable_on_material_request
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CostCenter|null $costCenter
 * @property-read Collection<int, BudgetAccount> $accounts
 */
class Budget extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'fiscal_year_id',
        'cost_center_id',
        'company_id',
        'applicable_on_material_request',
    ];

    protected $casts = [
        'applicable_on_material_request' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'budgets';
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::fiscalYear(), 'fiscal_year_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::costCenter(), 'cost_center_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(ModelResolver::budgetAccount(), 'budget_id');
    }
}
