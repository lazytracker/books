<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('uploaded_orders', function (Blueprint $table) {
            $table->boolean('is_verified')->nullable()->default(null)->after('year');
            // Или если хочешь default 0:
            // $table->boolean('is_verified')->nullable()->default(0)->after('year');
        });
    }

    public function down(): void
    {
        Schema::table('uploaded_orders', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
};
