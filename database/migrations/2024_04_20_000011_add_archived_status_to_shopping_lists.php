<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE shopping_lists MODIFY COLUMN status ENUM('active','completed','cancelled','archived') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE shopping_lists MODIFY COLUMN status ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active'");
    }
};
