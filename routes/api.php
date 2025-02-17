<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\DealController;

Route::group(['middleware' => ['subdomain']], function () {

    // Открытые маршруты для регистрации и логина
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/crms', [CRMController::class, 'store']);

    // Защищенные маршруты, доступные только с валидным API-токеном Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);

        // Возвращает профиль текущего аутентифицированного пользователя
        Route::get('/users', [UserController::class, 'users']);

        // Возвращает профиль текущего аутентифицированного пользователя
        Route::get('/profile', [UserController::class, 'profile']);

        // Возвращает данные пользователя по ID (если реализован метод show)
        Route::get('/users/{id}', [UserController::class, 'show']);

        // Создание нового пользователя
        Route::post('/users', [UserController::class, 'store']);

        // Обновление данных пользователя по ID
        Route::put('/users/{id}', [UserController::class, 'update']);

        // Удаление пользователя по ID
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::apiResource('contacts', ContactController::class);
        Route::apiResource('companies', CompanyController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('notes', NoteController::class);
        Route::apiResource('messages', MessageController::class);
        Route::apiResource('files', FileController::class);
        Route::apiResource('deals', DealController::class);

        Route::get('/crms', [CRMController::class, 'index']);
        Route::get('/crms/{id}', [CRMController::class, 'show']);
        Route::put('/crms/{id}', [CRMController::class, 'update']);
        Route::delete('/crms/{id}', [CRMController::class, 'destroy']);
    });
});
