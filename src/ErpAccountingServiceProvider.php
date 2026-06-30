<?php

namespace JeffersonGoncalves\Erp\Accounting;

use JeffersonGoncalves\Erp\Accounting\Models\Contracts\AccountContract;
use JeffersonGoncalves\Erp\Accounting\Models\Contracts\GlEntryContract;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpAccountingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-accounting';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_accounts_table',
                'create_erp_cost_centers_table',
                'create_erp_payment_terms_table',
                'create_erp_modes_of_payment_table',
                'create_erp_tax_templates_table',
                'create_erp_tax_template_taxes_table',
                'create_erp_banks_table',
                'create_erp_bank_accounts_table',
                'create_erp_budgets_table',
                'create_erp_budget_accounts_table',
                'create_erp_gl_entries_table',
                'create_erp_journal_entries_table',
                'create_erp_journal_entry_accounts_table',
                'create_erp_payment_entries_table',
                'create_erp_sales_invoices_table',
                'create_erp_sales_invoice_items_table',
                'create_erp_sales_invoice_taxes_table',
                'create_erp_purchase_invoices_table',
                'create_erp_purchase_invoice_items_table',
                'create_erp_purchase_invoice_taxes_table',
                'create_erp_period_closing_vouchers_table',
                'create_erp_bank_transactions_table',
                'create_erp_tax_withholding_categories_table',
                'create_erp_tax_withholding_rates_table',
                'create_erp_accounting_dimensions_table',
                'create_erp_accounting_dimension_values_table',
                'create_erp_payment_reconciliations_table',
                'create_erp_payment_reconciliation_allocations_table',
                'create_erp_dunnings_table',
                'create_erp_dunning_overdue_invoices_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(GeneralLedgerService::class);
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            AccountContract::class => 'account',
            GlEntryContract::class => 'gl_entry',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-accounting.models.{$configKey}"));
        }
    }
}
