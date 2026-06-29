<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property int|null $company_id
 * @property string $name
 * @property string $title
 * @property bool $is_sales
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, TaxTemplateTax> $taxes
 */
class TaxTemplate extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'title',
        'is_sales',
    ];

    protected $casts = [
        'is_sales' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'tax_templates';
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(ModelResolver::taxTemplateTax(), 'tax_template_id');
    }
}
