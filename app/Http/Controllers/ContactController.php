<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Возвращает список контактов.
     */
    public function index(Request $request)
    {
        // Можно фильтровать контакты по crm_id, если требуется.
        $contacts = Contact::all();
        return response()->json($contacts);
    }

    /**
     * Создает новый контакт.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email',
            'position'   => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        $crmId = auth()->check() ? auth()->user()->crm_id : session()->get('crm_id');
        if (!$crmId) {
            return response()->json(['error' => 'CRM не определена'], 400);
        }
        $validated['crm_id'] = $crmId;

        $contact = Contact::create($validated);

        return response()->json([
            'message' => 'Контакт успешно создан',
            'contact' => $contact,
        ], 201);
    }

    /**
     * Возвращает данные конкретного контакта.
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json($contact);
    }

    /**
     * Обновляет данные контакта.
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name'  => 'sometimes|required|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email',
            'position'   => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Контакт успешно обновлен',
            'contact' => $contact,
        ]);
    }

    /**
     * Удаляет контакт.
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'message' => 'Контакт успешно удален',
        ]);
    }
}
