<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property string $account_name
 * @property int $bank_id
 * @property string|null $account_no
 * @property int|null $account_id
 * @property int|null $company_id
 * @property bool $is_default
 * @property bool $is_company_account
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bank $bank
 * @property-read Account|null $account
 */
class BankAccount extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'account_name',
        'bank_id',
        'account_no',
        'account_id',
        'company_id',
        'is_default',
        'is_company_account',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_company_account' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'bank_accounts';
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::bank(), 'bank_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'account_id');
    }
}
