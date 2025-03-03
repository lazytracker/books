<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Добавляем новую колонку
        Schema::table('books', function (Blueprint $table) {
            $table->string('subj_hex')->nullable()->after('subj');
        });

        // Заполняем HEX значениями
        DB::statement('UPDATE books SET subj_hex = HEX(subj)');
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('subj_hex');
        });
    }
}; 