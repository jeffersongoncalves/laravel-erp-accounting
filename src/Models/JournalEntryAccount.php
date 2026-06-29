<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property int $journal_entry_id
 * @property int $account_id
 * @property float $debit
 * @property float $credit
 * @property int|null $cost_center_id
 * @property string|null $party_type
 * @property int|null $party_id
 * @property string|null $against_account
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read JournalEntry|null $journalEntry
 * @property-read Account $account
 */
class JournalEntryAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'account_id',
        'debit',
        'credit',
        'cost_center_id',
        'party_type',
        'party_id',
        'against_account',
    ];

    protected $attributes = [
        'debit' => 0,
        'credit' => 0,
    ];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
    ];

    protected static function booted(): void
    {
        static::saved(fn (JournalEntryAccount $row) => $row->syncParentTotals());
        static::deleted(fn (JournalEntryAccount $row) => $row->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'journal_entry_accounts';
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::journalEntry(), 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'account_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->journalEntry;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->calculateTotals();
        $parent->save();
    }
}
