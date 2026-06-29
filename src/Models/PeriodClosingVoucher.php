<?php

namespace JeffersonGoncalves\Erp\Accounting\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property Carbon $posting_date
 * @property int $fiscal_year_id
 * @property int|null $company_id
 * @property int $closing_account_head_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $closingAccountHead
 */
class PeriodClosingVoucher extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'posting_date',
        'fiscal_year_id',
        'company_id',
        'closing_account_head_id',
        'docstatus',
    ];

    protected $attributes = [
        'docstatus' => 0,
    ];

    protected $casts = [
        'posting_date' => 'date',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-accounting.table_prefix') ?? '').'period_closing_vouchers';
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::fiscalYear(), 'fiscal_year_id');
    }

    public function closingAccountHead(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::account(), 'closing_account_head_id');
    }

    public function postLedgerEntries(): void
    {
        $gl = app(GeneralLedgerService::class);
        $accountModel = ModelResolver::account();

        /** @var Collection<int, Account> $accounts */
        $accounts = $accountModel::query()
            ->where('company_id', $this->company_id)
            ->whereIn('root_type', [RootType::Income->value, RootType::Expense->value])
            ->where('is_group', false)
            ->get();

        $entries = [];
        $closingDebit = 0.0;
        $closingCredit = 0.0;

        foreach ($accounts as $account) {
            $balance = $gl->accountBalance($account);

            if (round($balance, 2) === 0.0) {
                continue;
            }

            if ($balance > 0) {
                $entries[] = ['account_id' => $account->id, 'debit' => 0, 'credit' => $balance];
                $closingDebit += $balance;
            } else {
                $entries[] = ['account_id' => $account->id, 'debit' => abs($balance), 'credit' => 0];
                $closingCredit += abs($balance);
            }
        }

        $entries[] = [
            'account_id' => $this->closing_account_head_id,
            'debit' => $closingDebit,
            'credit' => $closingCredit,
            'remarks' => 'Period closing',
        ];

        $gl->post($this, $entries);
    }

    public function reverseLedgerEntries(): void
    {
        app(GeneralLedgerService::class)->reverse($this);
    }
}
