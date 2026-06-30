<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Services\DunningService;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property int|null $company_id
 * @property string $party_type
 * @property int|null $party_id
 * @property string|null $customer_name
 * @property Carbon $posting_date
 * @property int $dunning_level
 * @property float $rate_of_interest
 * @property float $total_interest
 * @property float $dunning_amount
 * @property int $income_account_id
 * @property int $debit_to_account_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $incomeAccount
 * @property-read Account $debitToAccount
 * @property-read Collection<int, DunningOverdueInvoice> $overdueInvoices
 */
class Dunning extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'company_id',
        'party_type',
        'party_id',
        'customer_name',
        'posting_date',
        'dunning_level',
        'rate_of_interest',
        'total_interest',
        'dunning_amount',
        'income_account_id',
        'debit_to_account_id',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'dunning_level' => 1,
        'rate_of_interest' => 0,
        'total_interest' => 0,
        'dunning_amount' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'posting_date' => 'date',
        'dunning_level' => 'integer',
        'rate_of_interest' => 'float',
        'total_interest' => 'float',
        'dunning_amount' => 'float',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'dunnings';
    }

    public function incomeAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'income_account_id');
    }

    public function debitToAccount(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'debit_to_account_id');
    }

    public function overdueInvoices(): HasMany
    {
        return $this->hasMany(DunningOverdueInvoice::class, 'dunning_id');
    }

    public function postLedgerEntries(): void
    {
        app(DunningService::class)->post($this);
    }

    public function reverseLedgerEntries(): void
    {
        app(DunningService::class)->reverse($this);
    }
}
