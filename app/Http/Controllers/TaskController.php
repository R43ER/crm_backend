<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Возвращает список всех задач.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    /**
     * Создает новую задачу.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crm_id'               => 'required|exists:crms,id',
            'responsible_user_id'  => 'required|exists:users,id',
            'contact_id'           => 'nullable|exists:contacts,id',
            'company_id'           => 'nullable|exists:companies,id',
            'deal_id'              => 'nullable|exists:deals,id',
            'task_text'            => 'required|string',
            'result'               => 'nullable|string',
            'type'                 => 'required|string|max:255',
            'execution_start'      => 'nullable|date_format:Y-m-d H:i:s',
            'execution_end'        => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $task = Task::create($validated);

        return response()->json([
            'message' => 'Задание успешно создано',
            'task'    => $task,
        ], 201);
    }

    /**
     * Возвращает данные конкретной задачи.
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        return response()->json($task);
    }

    /**
     * Обновляет данные задачи.
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'crm_id'               => 'sometimes|required|exists:crms,id',
            'responsible_user_id'  => 'sometimes|required|exists:users,id',
            'contact_id'           => 'nullable|exists:contacts,id',
            'company_id'           => 'nullable|exists:companies,id',
            'deal_id'              => 'nullable|exists:deals,id',
            'task_text'            => 'sometimes|required|string',
            'result'               => 'nullable|string',
            'type'                 => 'sometimes|required|string|max:255',
            'execution_start'      => 'nullable|date_format:Y-m-d H:i:s',
            'execution_end'        => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'Задание успешно обновлено',
            'task'    => $task,
        ]);
    }

    /**
     * Удаляет задачу.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            'message' => 'Задание успешно удалено',
        ]);
    }
}
