<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ManualOrderController extends Controller
{
    public function index()
    {
        $orders = DB::table('uploaded_orders')->get();
        return view('manual-order', compact('orders'));
    }

    public function upload(Request $request)
    {
        Log::info('=== НАЧАЛО ЗАГРУЗКИ ФАЙЛА ===');
        
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // max 10MB
        ]);

        try {
            $file = $request->file('excel_file');
            Log::info('Файл получен: ' . $file->getClientOriginalName());
            Log::info('Размер файла: ' . $file->getSize() . ' байт');
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            Log::info('Общее количество строк в файле: ' . count($rows));

            // Показываем первые 15 строк для анализа
            for ($i = 0; $i < min(15, count($rows)); $i++) {
                Log::info("Строка $i: " . json_encode($rows[$i], JSON_UNESCAPED_UNICODE));
            }

            // Очищаем таблицу перед загрузкой новых данных
            DB::table('uploaded_orders')->truncate();
            Log::info('Таблица очищена');

            $insertedCount = 0;
            $skippedCount = 0;

            // Пробуем разные варианты начальной строки
            $possibleStartRows = [6, 7, 8, 9, 10]; // Индексы возможных начальных строк
            
            foreach ($possibleStartRows as $startRow) {
                Log::info("=== Проверяем начальную строку: $startRow ===");
                
                if ($startRow >= count($rows)) {
                    Log::info("Строка $startRow не существует");
                    continue;
                }
                
                $testRow = $rows[$startRow];
                Log::info("Содержимое тестовой строки $startRow: " . json_encode($testRow, JSON_UNESCAPED_UNICODE));
                
                // Проверяем, похожа ли строка на заголовок или данные
                if (isset($testRow[0]) && is_numeric($testRow[0])) {
                    Log::info("Найдена возможная начальная строка данных: $startRow");
                    break;
                }
            }

            // Начинаем с найденной строки или с 8 по умолчанию
            $dataStartRow = isset($startRow) ? $startRow : 8;
            Log::info("Используем начальную строку: $dataStartRow");

            for ($i = $dataStartRow; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                Log::info("Обрабатываем строку $i: " . json_encode($row, JSON_UNESCAPED_UNICODE));
                
                // Пропускаем совсем пустые строки
                if (empty(array_filter($row))) {
                    Log::info("Строка $i пустая, пропускаем");
                    $skippedCount++;
                    continue;
                }
                
                // Пропускаем строки с "ИТОГО"
                if (isset($row[0]) && strpos(strtoupper($row[0] ?? ''), 'ИТОГО') !== false) {
                    Log::info("Строка $i содержит ИТОГО, пропускаем");
                    $skippedCount++;
                    continue;
                }

                // Преобразуем цену из российского формата в европейский
                $price = $this->convertPrice($row[9] ?? '0');
                Log::info("Цена после конвертации: $price");

                $orderData = [
                    'ART' => trim($row[1] ?? ''),
                    'seqNum' => trim($row[2] ?? ''),
                    'author' => trim($row[4] ?? ''),
                    'caption' => trim($row[5] ?? ''),
                    'year' => trim($row[6] ?? ''),
                    'quantity' => (int)($row[8] ?? 0),
                    'price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Log::info("Подготовленные данные: " . json_encode($orderData, JSON_UNESCAPED_UNICODE));

                // Проверяем, есть ли хоть какие-то значимые данные
                $hasData = !empty($orderData['ART']) || !empty($orderData['caption']) || !empty($orderData['author']);
                
                if ($hasData) {
                    try {
                        DB::table('uploaded_orders')->insert($orderData);
                        $insertedCount++;
                        Log::info("Строка $i успешно вставлена в БД");
                    } catch (\Exception $dbError) {
                        Log::error("Ошибка вставки строки $i в БД: " . $dbError->getMessage());
                    }
                } else {
                    Log::info("Строка $i не содержит значимых данных, пропускаем");
                    $skippedCount++;
                }
            }

            Log::info("=== РЕЗУЛЬТАТ ОБРАБОТКИ ===");
            Log::info("Вставлено записей: $insertedCount");
            Log::info("Пропущено строк: $skippedCount");

            if ($insertedCount > 0) {
                return redirect()->route('manual-order.index')
                    ->with('success', "Файл успешно загружен! Обработано записей: {$insertedCount}, пропущено: {$skippedCount}");
            } else {
                return redirect()->route('manual-order.index')
                    ->with('error', "В файле не найдено данных для загрузки. Проверьте логи для детальной информации. Пропущено строк: {$skippedCount}");
            }

        } catch (\Exception $e) {
            Log::error('Критическая ошибка при загрузке файла: ' . $e->getMessage());
            Log::error('Трассировка: ' . $e->getTraceAsString());
            return redirect()->route('manual-order.index')
                ->with('error', 'Ошибка при загрузке файла: ' . $e->getMessage());
        }
    }

    public function clear()
    {
        DB::table('uploaded_orders')->truncate();
        return redirect()->route('manual-order.index')->with('success', 'Данные успешно очищены!');
    }

    private function convertPrice($priceString)
    {
        Log::info("Конвертируем цену: '$priceString'");
        
        // Убираем пробелы и заменяем запятую на точку
        $price = str_replace([' ', ','], ['', '.'], trim($priceString));
        
        // Дополнительная очистка от возможных символов
        $price = preg_replace('/[^\d.,]/', '', $price);
        $price = str_replace(',', '.', $price);
        
        Log::info("Цена после обработки: '$price'");
        
        $result = is_numeric($price) ? (float)$price : 0.00;
        Log::info("Финальная цена: $result");
        
        return $result;
    }
}