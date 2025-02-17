<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deal;

class DealController extends Controller
{
    /**
     * Возвращает список всех сделок.
     */
    public function index()
    {
        $deals = Deal::all();
        return response()->json($deals);
    }

    /**
     * Создает новую сделку.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'crm_id'               => 'required|exists:crms,id',
            'responsible_user_id'  => 'required|exists:users,id',
            'company_id'           => 'nullable|exists:companies,id',
            'budget'               => 'nullable|numeric',
            'title'                => 'required|string|max:255',
            'status'               => 'required|string|max:255',
        ]);

        $deal = Deal::create($validated);

        return response()->json([
            'message' => 'Сделка успешно создана',
            'deal'    => $deal,
        ], 201);
    }

    /**
     * Возвращает данные конкретной сделки.
     */
    public function show($id)
    {
        $deal = Deal::findOrFail($id);
        return response()->json($deal);
    }

    /**
     * Обновляет данные сделки.
     */
    public function update(Request $request, $id)
    {
        $deal = Deal::findOrFail($id);

        $validated = $request->validate([
            'crm_id'               => 'sometimes|required|exists:crms,id',
            'responsible_user_id'  => 'sometimes|required|exists:users,id',
            'company_id'           => 'nullable|exists:companies,id',
            'budget'               => 'nullable|numeric',
            'title'                => 'sometimes|required|string|max:255',
            'status'               => 'sometimes|required|string|max:255',
        ]);

        $deal->update($validated);

        return response()->json([
            'message' => 'Сделка успешно обновлена',
            'deal'    => $deal,
        ]);
    }

    /**
     * Удаляет сделку.
     */
    public function destroy($id)
    {
        $deal = Deal::findOrFail($id);
        $deal->delete();

        return response()->json([
            'message' => 'Сделка успешно удалена',
        ]);
    }
}
