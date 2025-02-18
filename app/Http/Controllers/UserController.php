<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CRM; // Добавляем импорт модели CRM
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function users()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Регистрация нового пользователя.
     * Доступна без аутентификации.
     */
    public function register(Request $request)
    {
        // Валидация данных для создания CRM
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'subdomain' => 'required|string|unique:crms,subdomain',
            'avatar'    => 'nullable|string',
            'website'   => 'nullable|url',
        ]);

        // Создаем новую CRM
        $crm = CRM::create([
            'name'      => $validated['name'],
            'subdomain' => $validated['subdomain'],
            'avatar'    => $validated['avatar'],
            'website'   => $validated['website']
        ]);

        // Валидация данных для создания пользователя
        $validated = $request->validate([
            'user_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'phone'         => 'nullable|string|max:20',
            'note'          => 'nullable|string',
            'user_avatar'   => 'nullable|string',
        ]);

        $validated['crm_id'] = $crm->id;

        // Создаем пользователя, связанного с CRM
        $user = User::create([
            'name'     => $validated['user_name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'] ?? null,
            'note'     => $validated['note'] ?? null,
            'avatar'   => $validated['user_avatar'] ?? null,
            'crm_id'   => $validated['crm_id']
        ]);

        // Выдаем токен через Sanctum
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Пользователь успешно зарегистрирован',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Вход пользователя.
     * Доступен без аутентификации.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Успешный вход',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Возвращает профиль текущего аутентифицированного пользователя.
     * Доступно по маршруту /api/profile.
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Создает нового пользователя (например, администратором).
     * Доступно только аутентифицированным пользователям.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'note'     => 'nullable|string',
            'avatar'   => 'nullable|string',
        ]);
        $crmId = auth()->check() ? auth()->user()->crm_id : session()->get('crm_id');
        if (!$crmId) {
            return response()->json(['error' => 'CRM не определена'], 400);
        }
        $validated['crm_id'] = $crmId;

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'] ?? null,
            'note'     => $validated['note'] ?? null,
            'avatar'   => $validated['avatar'] ?? null,
            'crm_id'   => $validated['crm_id'],
        ]);

        return response()->json([
            'message' => 'Пользователь успешно создан',
            'user'    => $user,
        ], 201);
    }

    /**
     * Обновляет данные пользователя по его id.
     * Доступно только аутентифицированным пользователям.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'note'     => 'nullable|string',
            'avatar'   => 'nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Пользователь успешно обновлен',
            'user'    => $user,
        ]);
    }

    /**
     * Удаляет пользователя по его id.
     * Доступно только аутентифицированным пользователям.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Пользователь успешно удалён',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Вы успешно вышли из системы']);
    }
}
