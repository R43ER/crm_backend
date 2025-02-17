<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест регистрации пользователя.
     */
    public function test_user_can_register_successfully()
    {
        // Создаём тестовую CRM
        $crm = CRM::factory()->create();

        // Данные для регистрации
        $data = [
            'name'                  => 'John Doe',
            'email'                 => 'johndoe@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'crm_id'                => $crm->id,
        ];

        // Отправляем POST-запрос на эндпоинт /api/register
        $response = $this->json('POST', '/api/register', $data);

        // Проверяем статус и структуру ответа
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'crm_id'],
                'token',
            ]);

        // Проверяем, что пользователь создан в базе данных
        $this->assertDatabaseHas('users', [
            'email'  => 'johndoe@example.com',
            'crm_id' => $crm->id,
        ]);
    }

    /**
     * Тест входа пользователя.
     */
    public function test_user_can_login_successfully()
    {
        $crm = CRM::factory()->create();
        // Создаем пользователя с известным паролем
        $user = User::factory()->create([
            'crm_id'   => $crm->id,
            'password' => Hash::make('secret123'),
        ]);

        $data = [
            'email'    => $user->email,
            'password' => 'secret123',
        ];

        $response = $this->json('POST', '/api/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'crm_id'],
                'token',
            ]);
    }

    /**
     * Тест получения профиля текущего пользователя.
     */
    public function test_user_can_get_profile()
    {
        $crm = CRM::factory()->create();
        $user = User::factory()->create(['crm_id' => $crm->id]);

        // Авторизуем пользователя через Sanctum
        $this->actingAs($user, 'sanctum');

        $response = $this->json('GET', '/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'id'      => $user->id,
                'email'   => $user->email,
                'crm_id'  => $crm->id,
            ]);
    }

    /**
     * Тест создания нового пользователя (администратор).
     */
    public function test_admin_can_store_new_user()
    {
        $crm = CRM::factory()->create();
        // Создаем "администратора" для аутентификации
        $admin = User::factory()->create(['crm_id' => $crm->id]);
        $this->actingAs($admin, 'sanctum');

        $data = [
            'name'     => 'Jane Smith',
            'email'    => 'janesmith@example.com',
            'password' => 'password123',
            'crm_id'   => $crm->id,
        ];

        $response = $this->json('POST', '/api/users', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name'    => 'Jane Smith',
                'email'   => 'janesmith@example.com',
                'crm_id'  => $crm->id,
            ]);

        $this->assertDatabaseHas('users', [
            'email'   => 'janesmith@example.com',
            'crm_id'  => $crm->id,
        ]);
    }

    /**
     * Тест обновления данных пользователя.
     */
    public function test_user_can_update_their_information()
    {
        $crm = CRM::factory()->create();
        $user = User::factory()->create(['crm_id' => $crm->id]);
        $this->actingAs($user, 'sanctum');

        $updateData = [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->json('PUT', '/api/users/' . $user->id, $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name'  => 'Updated Name',
                'email' => 'updated@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Тест удаления пользователя.
     */
    public function test_user_can_be_deleted()
    {
        $crm = CRM::factory()->create();
        $user = User::factory()->create(['crm_id' => $crm->id]);
        $this->actingAs($user, 'sanctum');

        $response = $this->json('DELETE', '/api/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Пользователь успешно удалён']);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
