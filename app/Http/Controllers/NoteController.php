<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Возвращает список всех примечаний.
     */
    public function index()
    {
        $notes = Note::all();
        return response()->json($notes);
    }

    /**
     * Создает новое примечание.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crm_id'      => 'required|exists:crms,id',
            'user_id'     => 'required|exists:users,id', // автор примечания
            'company_id'  => 'nullable|exists:companies,id',
            'contact_id'  => 'nullable|exists:contacts,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'content'     => 'required|string',
        ]);

        $note = Note::create($validated);

        return response()->json([
            'message' => 'Примечание успешно создано',
            'note'    => $note,
        ], 201);
    }

    /**
     * Возвращает данные конкретного примечания.
     */
    public function show($id)
    {
        $note = Note::findOrFail($id);
        return response()->json($note);
    }

    /**
     * Обновляет данные примечания.
     */
    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);

        $validated = $request->validate([
            'crm_id'      => 'sometimes|required|exists:crms,id',
            'user_id'     => 'sometimes|required|exists:users,id',
            'company_id'  => 'nullable|exists:companies,id',
            'contact_id'  => 'nullable|exists:contacts,id',
            'deal_id'     => 'nullable|exists:deals,id',
            'content'     => 'sometimes|required|string',
        ]);

        $note->update($validated);

        return response()->json([
            'message' => 'Примечание успешно обновлено',
            'note'    => $note,
        ]);
    }

    /**
     * Удаляет примечание.
     */
    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return response()->json([
            'message' => 'Примечание успешно удалено',
        ]);
    }
}
