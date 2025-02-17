<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class FileController extends Controller
{
    /**
     * Вывод списка всех файлов.
     */
    public function index()
    {
        $files = File::all();
        return response()->json($files);
    }

    /**
     * Создает новый файл и сохраняет загруженный файл.
     */
    public function store(Request $request)
    {
        // Валидируем обязательные поля и сам файл
        $validated = $request->validate([
            'crm_id'      => 'required|exists:crms,id',
            'user_id'     => 'required|exists:users,id', // автор файла
            'company_id'  => 'nullable|exists:companies,id',
            'contact_id'  => 'nullable|exists:contacts,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'note_id'     => 'nullable|exists:notes,id',
            'file'        => 'required|file|max:10240', // Ограничение 10 МБ
        ]);

        // Проверяем, что файл был загружен, и сохраняем его на диск
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            // Сохраняем файл в каталоге 'uploads' на публичном диске
            $path = $uploadedFile->store('uploads', 'public');

            // Добавляем информацию о файле в валидированные данные
            $validated['file_path'] = $path;
            $validated['file_name'] = $uploadedFile->getClientOriginalName();
        } else {
            return response()->json(['message' => 'Файл не был загружен'], 422);
        }

        $file = File::create($validated);

        return response()->json([
            'message' => 'Файл успешно создан и сохранён',
            'data'    => $file,
        ], 201);
    }

    /**
     * Выводит данные конкретного файла.
     */
    public function show($id)
    {
        $file = File::findOrFail($id);
        return response()->json($file);
    }

    /**
     * Обновляет данные файла.
     * Если новый файл передан, заменяет старый.
     */
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);

        $validated = $request->validate([
            'crm_id'      => 'sometimes|required|exists:crms,id',
            'user_id'     => 'sometimes|required|exists:users,id',
            'company_id'  => 'nullable|exists:companies,id',
            'contact_id'  => 'nullable|exists:contacts,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'note_id'     => 'nullable|exists:notes,id',
            'file'        => 'nullable|file|max:10240', // Ограничение 10 МБ
        ]);

        // Если новый файл загружается, сохраняем его и обновляем поля file_path и file_name
        if ($request->hasFile('file')) {
            // При желании можно удалить старый файл:
            if ($file->file_path) {
                Storage::disk('public')->delete($file->file_path);
            }
            $uploadedFile = $request->file('file');
            $path = $uploadedFile->store('uploads', 'public');

            $validated['file_path'] = $path;
            $validated['file_name'] = $uploadedFile->getClientOriginalName();
        }

        $file->update($validated);

        return response()->json([
            'message' => 'Файл успешно обновлён',
            'data'    => $file,
        ]);
    }

    /**
     * Удаляет файл.
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        // Удаляем физический файл, если существует
        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return response()->json([
            'message' => 'Файл успешно удалён',
        ]);
    }
}
