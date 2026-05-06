<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'batches_consumed')) {
                $table->json('batches_consumed')->nullable()->after('expiry_date');
            }
        });

        if (!$this->indexExistsForColumn('invoice_items', 'product_id')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->index('product_id', 'invoice_items_product_id_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ($this->indexNameExists('invoice_items', 'invoice_items_product_id_index')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropIndex('invoice_items_product_id_index');
            });
        }

        if (Schema::hasColumn('invoice_items', 'batches_consumed')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('batches_consumed');
            });
        }
    }

    private function indexExistsForColumn(string $table, string $column): bool
    {
        $driver = DB::getDriverName();
        $tableName = DB::getTablePrefix() . $table;

        if ($driver === 'mysql') {
            return count(DB::select("SHOW INDEX FROM {$tableName} WHERE Column_name = ?", [$column])) > 0;
        }

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$tableName}')");
            foreach ($indexes as $index) {
                $name = $index->name ?? $index->index_name ?? null;
                if ($name === null) {
                    continue;
                }

                $columns = DB::select("PRAGMA index_info('{$name}')");
                foreach ($columns as $col) {
                    $colName = $col->name ?? $col->column_name ?? null;
                    if ($colName === $column) {
                        return true;
                    }
                }
            }

            return false;
        }

        if ($driver === 'pgsql') {
            return count(DB::select(
                'SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexdef ILIKE ?',
                [$table, "%({$column})%"]
            )) > 0;
        }

        return false;
    }

    private function indexNameExists(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();
        $tableName = DB::getTablePrefix() . $table;

        if ($driver === 'mysql') {
            return count(DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = ?", [$indexName])) > 0;
        }

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list('{$tableName}')");
            foreach ($indexes as $index) {
                $name = $index->name ?? $index->index_name ?? null;
                if ($name === $indexName) {
                    return true;
                }
            }

            return false;
        }

        if ($driver === 'pgsql') {
            return count(DB::select(
                'SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?',
                [$table, $indexName]
            )) > 0;
        }

        return false;
    }
};
