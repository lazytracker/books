<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Фиксированное соответствие предметов и их номеров
            $subjectMapping = [
                'Английский язык' => '00',
                'Астрономия' => '01',
                'Биология' => '02',
                'География' => '03',
                'ИЗО' => '04',
                'Информатика' => '05',
                'История' => '06',
                'Литература' => '07',
                'Математика' => '08',
                'Музыка' => '09',
                'Немецкий язык' => '10',
                'ОБЖ' => '11',
                'Обществознание' => '12',
                'Окружающий мир' => '13',
                'Русский язык' => '14',
                'Технология' => '15',
                'Физика' => '16',
                'Физкультура' => '17',
                'Французский язык' => '18',
                'Химия' => '19'
            ];

            // Получаем список уникальных предметов с их ID
            $subjects = DB::table('books')
                ->select('subj', 'subj_hex')
                ->distinct()
                ->orderBy('subj_hex')
                ->get();

            // Базовый запрос для фильтрации
            $query = DB::table('books');
            if ($request->has('subject_id')) {
                $query->where('subj_hex', $request->subject_id);
            }

            // Получаем список классов с количеством книг для каждого
            $classesWithCount = DB::table('books')
                ->select('class')
                ->selectRaw('COUNT(*) as book_count')
                ->when($request->has('subject_id'), function($q) use ($request) {
                    return $q->where('subj_hex', $request->subject_id);
                })
                ->groupBy('class')
                ->orderBy('class')
                ->get();

            // Получаем книги с пагинацией
            if ($request->has('class')) {
                $query->where('class', $request->class);
            }
            $books = $query->paginate(20)->withQueryString();
            
            \Log::info('Total books found: ' . $books->total());
            \Log::info('SQL Query: ' . $query->toSql());
            \Log::info('SQL Bindings: ' . json_encode($query->getBindings()));

            return view('books.index', compact('books', 'subjects', 'classesWithCount', 'subjectMapping'));

        } catch (\Exception $e) {
            \Log::error('Error in BookController: ' . $e->getMessage());
            throw $e;
        }
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }
} 