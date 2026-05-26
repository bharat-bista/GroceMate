<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDemoData extends Command
{
    protected $signature   = 'db:reset-demo {--force : Skip confirmation prompt}';
    protected $description = 'Truncate all transactional data. Keeps users, businesses, categories, brands, taxes, delivery settings, and sessions.';

    // Tables to wipe — order matters: children before parents to satisfy FKs
    // (FK checks are disabled inside the command so order is a safety measure only)
    private array $truncate = [
        // Ecommerce
        'order_refunds',
        'order_items',
        'orders',

        // POS / Financial
        'invoice_items',
        'invoices',
        'supplier_payments',
        'incomes',
        'expenses',
        'customers',

        // Inventory — stock first, then products
        'stock_batches',
        'stock',
        'purchase_item_taxes',
        'purchase_items',
        'purchases',
        'ecommerce_product_images',
        'ecommerce_products',
        'products',
        'suppliers',
        'sliders',

        // Misc
        'contact_messages',
        'otp_resets',
        'password_reset_tokens',
        'jobs',
        'job_batches',
        'failed_jobs',
        'cache',
        'cache_locks',
    ];

    // Tables that exist only when Telescope is installed
    private array $telescopeTables = [
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ];

    public function handle(): int
    {
        $this->warn('This will delete ALL transactional data.');
        $this->line('Keeping: users, businesses, categories, brands, taxes, delivery_fee_settings, sessions.');
        $this->newLine();

        if (! $this->option('force') && ! $this->confirm('Are you sure you want to continue?')) {
            $this->info('Aborted — nothing changed.');
            return self::SUCCESS;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->info('Truncating tables...');

        foreach ($this->truncate as $table) {
            DB::table($table)->truncate();
            $this->line("  ✓ {$table}");
        }

        // Telescope — skip gracefully if not installed
        foreach ($this->telescopeTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  ✓ {$table}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Reset business balances to 0 (they are derived from incomes/expenses)
        DB::table('businesses')->update(['balance' => 0]);
        $this->line('  ✓ businesses.balance reset to 0');

        // Reset supplier total_due to opening_due
        DB::statement('UPDATE suppliers SET total_due = opening_due');
        $this->line('  ✓ suppliers.total_due reset to opening_due');

        // Reset customer total_due to opening_due
        DB::statement('UPDATE customers SET total_due = opening_due');
        $this->line('  ✓ customers.total_due reset to opening_due');

        $this->newLine();
        $this->info('Done. Database is clean and ready for a fresh demo.');
        $this->line('Your users, businesses, categories, brands, taxes, and delivery settings are untouched.');

        return self::SUCCESS;
    }
}
