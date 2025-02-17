<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    /**
     * Возвращает список всех компаний.
     */
    public function index()
    {
        $companies = Company::all();
        return response()->json($companies);
    }

    /**
     * Создает новую компанию.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'required|email|unique:companies,email',
            'web'     => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'crm_id'  => 'required|exists:crms,id',
        ]);

        $company = Company::create($validated);

        return response()->json([
            'message' => 'Компания успешно создана',
            'company' => $company,
        ], 201);
    }

    /**
     * Возвращает данные конкретной компании.
     */
    public function show($id)
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    /**
     * Обновляет данные компании.
     */
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'email'   => 'sometimes|required|email|unique:companies,email,' . $company->id,
            'web'     => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'crm_id'  => 'sometimes|required|exists:crms,id',
        ]);

        $company->update($validated);

        return response()->json([
            'message' => 'Компания успешно обновлена',
            'company' => $company,
        ]);
    }

    /**
     * Удаляет компанию.
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return response()->json([
            'message' => 'Компания успешно удалена',
        ]);
    }
}
