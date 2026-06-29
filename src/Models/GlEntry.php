<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Models\Contracts\GlEntryContract;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * An immutable double-entry ledger row.
 *
 * Once written, the monetary impact of a row never changes: only the
 * `is_cancelled` flag and `remarks` may be toggled (during cancellation).
 * Deletes are forbidden outright.
 *
 * @property int $id
 * @property Carbon $posting_date
 * @property int $account_id
 * @property float $debit
 * @property float $credit
 * @property string $voucherable_type
 * @property int $voucherable_id
 * @property string|null $against_account
 * @property string|null $party_type
 * @property int|null $party_id
 * @property int|null $cost_center_id
 * @property int|null $company_id
 * @property string|null $remarks
 * @property bool $is_cancelled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $account
 * @property-read CostCenter|null $costCenter
 */
class GlEntry extends Model implements GlEntryContract
{
    use HasCompany;
    use HasFactory;

    /** Attributes whose values are frozen once the row exists. */
    private const PROTECTED_ATTRIBUTES = [
        'posting_date',
        'account_id',
        'debit',
        'credit',
        'voucherable_type',
        'voucherable_id',
        'company_id',
    ];

    protected $fillable = [
        'posting_date',
        'account_id',
        'debit',
        'credit',
        'voucherable_type',
        'voucherable_id',
        'against_account',
        'party_type',
        'party_id',
        'cost_center_id',
        'company_id',
        'remarks',
        'is_cancelled',
    ];

    protected $casts = [
        'posting_date' => 'date',
        'debit' => 'float',
        'credit' => 'float',
        'is_cancelled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::updating(function (GlEntry $entry): void {
            foreach (self::PROTECTED_ATTRIBUTES as $attribute) {
                if ($entry->isDirty($attribute)) {
                    throw new DomainException('General ledger entries are immutable');
                }
            }
        });

        static::deleting(function (): void {
            throw new DomainException('General ledger entries cannot be deleted');
        });
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'gl_entries';
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'account_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::costCenter(), 'cost_center_id');
    }

    public function voucherable(): MorphTo
    {
        return $this->morphTo();
    }
}
