<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE products MODIFY COLUMN open_food_facts_data LONGTEXT NULL');
        DB::statement('ALTER TABLE products MODIFY COLUMN ingredients LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY COLUMN open_food_facts_data TEXT NULL');
        DB::statement('ALTER TABLE products MODIFY COLUMN ingredients TEXT NULL');
    }
};
