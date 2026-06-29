<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Enums\JournalEntryType;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property Carbon $posting_date
 * @property int|null $company_id
 * @property JournalEntryType $voucher_type
 * @property string|null $user_remark
 * @property float $total_debit
 * @property float $total_credit
 * @property DocStatus $docstatus
 * @property string|null $cheque_no
 * @property Carbon|null $cheque_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, JournalEntryAccount> $accounts
 */
class JournalEntry extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'posting_date',
        'company_id',
        'voucher_type',
        'user_remark',
        'total_debit',
        'total_credit',
        'docstatus',
        'cheque_no',
        'cheque_date',
    ];

    protected $attributes = [
        'docstatus' => 0,
        'total_debit' => 0,
        'total_credit' => 0,
    ];

    protected $casts = [
        'posting_date' => 'date',
        'voucher_type' => JournalEntryType::class,
        'total_debit' => 'float',
        'total_credit' => 'float',
        'docstatus' => DocStatus::class,
        'cheque_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'journal_entries';
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(ModelResolver::journalEntryAccount(), 'journal_entry_id');
    }

    public function calculateTotals(): void
    {
        $this->total_debit = (float) $this->accounts()->sum('debit');
        $this->total_credit = (float) $this->accounts()->sum('credit');
    }

    public function postLedgerEntries(): void
    {
        $entries = $this->accounts->map(fn (JournalEntryAccount $row): array => [
            'account_id' => $row->account_id,
            'debit' => $row->debit,
            'credit' => $row->credit,
            'cost_center_id' => $row->cost_center_id,
            'party_type' => $row->party_type,
            'party_id' => $row->party_id,
            'against' => $row->against_account,
            'remarks' => $this->user_remark,
        ])->all();

        app(GeneralLedgerService::class)->post($this, $entries);
    }

    public function reverseLedgerEntries(): void
    {
        app(GeneralLedgerService::class)->reverse($this);
    }
}
