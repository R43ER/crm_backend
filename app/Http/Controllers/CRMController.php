<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CRM;

class CRMController extends Controller
{
    /**
     * Вывести список всех CRM.
     */
    public function index()
    {
        $crms = CRM::all();
        return response()->json($crms);
    }

    /**
     * Создать новую CRM.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subdomain' => 'required|string|unique:crms,subdomain',
            'avatar'    => 'nullable|string',
            'website'   => 'nullable|url',
        ]);

        $crm = CRM::create($validated);

        return response()->json([
            'message' => 'CRM успешно создана',
            'crm'     => $crm,
        ], 201);
    }

    /**
     * Получить данные конкретной CRM.
     */
    public function show($id)
    {
        $crm = CRM::findOrFail($id);
        return response()->json($crm);
    }

    /**
     * Обновить данные CRM.
     */
    public function update(Request $request, $id)
    {
        $crm = CRM::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'sometimes|required|string|max:255',
            // При обновлении subdomain учитываем, что он должен быть уникальным за исключением текущей CRM
            'subdomain' => 'sometimes|required|string|unique:crms,subdomain,' . $crm->id,
            'avatar'    => 'nullable|string',
            'website'   => 'nullable|url',
        ]);

        $crm->update($validated);

        return response()->json([
            'message' => 'CRM успешно обновлена',
            'crm'     => $crm,
        ]);
    }

    /**
     * Удалить CRM.
     */
    public function destroy($id)
    {
        $crm = CRM::findOrFail($id);
        $crm->delete();

        return response()->json([
            'message' => 'CRM успешно удалена',
        ]);
    }
}
