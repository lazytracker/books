<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        require_once(base_path('irbis_class.inc'));

        $irbis = new \irbis64('127.0.0.1', 6666, '1', '1', 'IBIS');

        if (!$irbis->connect()) {
            die('Ошибка подключения к серверу');
        }

        if (!$irbis->login()) {
            die('Ошибка авторизации: ' . $irbis->error());
        }

        // Функция чтения записи по MFN
        function readRecordByMfn($irbis, $mfn, $lock = false)
        {
            $record = $irbis->record_read($mfn, $lock);

            if ($record === false) {
                echo "Ошибка при чтении записи с MFN = $mfn\n";
                return false;
            }

            // Предполагается, что $record — объект с полем ver
            echo "Версия записи с MFN = $mfn: " . $record->ver . "\n";
            return $record;
        }

        try {
            // Вызываем чтение записи с MFN=89
            readRecordByMfn($irbis, 89, false);
        } catch (\Exception $e) {
            echo "Исключение при чтении записи: " . $e->getMessage() . "\n";
        }

        $irbis->logout();
    }
}
