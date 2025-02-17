<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Вывод списка всех сообщений.
     */
    public function index()
    {
        $messages = Message::all();
        return response()->json($messages);
    }

    /**
     * Создание нового сообщения.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crm_id'           => 'required|exists:crms,id',
            'user_id'          => 'required|exists:users,id',  // автор сообщения
            'company_id'       => 'nullable|exists:companies,id',
            'contact_id'       => 'nullable|exists:contacts,id',
            'deal_id'          => 'nullable|exists:deals,id',
            'receiver_user_id' => 'nullable|exists:users,id',  // получатель, если сообщение адресовано
            'content'          => 'required|string',
        ]);

        $message = Message::create($validated);

        return response()->json([
            'message' => 'Сообщение успешно создано',
            'data'    => $message,
        ], 201);
    }

    /**
     * Вывод данных конкретного сообщения.
     */
    public function show($id)
    {
        $message = Message::findOrFail($id);
        return response()->json($message);
    }

    /**
     * Обновление сообщения.
     */
    public function update(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        $validated = $request->validate([
            'crm_id'           => 'sometimes|required|exists:crms,id',
            'user_id'          => 'sometimes|required|exists:users,id',
            'company_id'       => 'nullable|exists:companies,id',
            'contact_id'       => 'nullable|exists:contacts,id',
            'deal_id'          => 'nullable|exists:deals,id',
            'receiver_user_id' => 'nullable|exists:users,id',
            'content'          => 'sometimes|required|string',
        ]);

        $message->update($validated);

        return response()->json([
            'message' => 'Сообщение успешно обновлено',
            'data'    => $message,
        ]);
    }

    /**
     * Удаление сообщения.
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return response()->json([
            'message' => 'Сообщение успешно удалено',
        ]);
    }
}
