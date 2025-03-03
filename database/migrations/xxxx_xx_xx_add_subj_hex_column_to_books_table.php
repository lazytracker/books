<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('books', 'subj_hex')) {
            Schema::table('books', function (Blueprint $table) {
                $table->string('subj_hex')->nullable()->after('subj');
            });

            // Заполняем HEX значениями из существующего столбца subj
            DB::statement('UPDATE books SET subj_hex = HEX(subj)');
        }
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('subj_hex');
        });
    }
}; 