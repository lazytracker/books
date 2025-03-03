<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Получаем список уникальных предметов
        $subjects = DB::table('books')
            ->select('subj')
            ->distinct()
            ->orderBy('subj')
            ->pluck('subj');

        // Присваиваем каждому предмету числовой идентификатор
        foreach ($subjects as $index => $subject) {
            $subj_id = $index + 1; // Начинаем с 1
            
            // Обновляем все книги с данным предметом
            DB::table('books')
                ->where('subj', $subject)
                ->update(['subj_hex' => sprintf('%02d', $subj_id)]); // Формат: 01, 02, 03...
        }
    }

    public function down()
    {
        // Возвращаем HEX значения
        DB::statement('UPDATE books SET subj_hex = HEX(subj)');
    }
}; 