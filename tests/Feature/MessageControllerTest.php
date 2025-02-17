<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\Message;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $crm;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем тестовую CRM
        $this->crm = CRM::factory()->create();

        // Создаем тестового пользователя, привязанного к данной CRM
        $this->user = User::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        // Аутентифицируем пользователя через Sanctum
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Тест получения списка сообщений.
     */
    public function test_can_get_messages_list()
    {
        // Создаем 3 сообщения, привязанных к данной CRM и пользователю-автору
        Message::factory()->count(3)->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/messages');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания нового сообщения.
     */
    public function test_can_create_message()
    {
        $data = [
            'crm_id'           => $this->crm->id,
            'user_id'          => $this->user->id,
            'content'          => 'This is a test message',
            // Опциональные связи оставляем пустыми (null)
            'company_id'       => null,
            'contact_id'       => null,
            'deal_id'          => null,
            'receiver_user_id' => null,
        ];

        $response = $this->json('POST', '/api/messages', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'content' => 'This is a test message',
                     'crm_id'  => $this->crm->id,
                     'user_id' => $this->user->id,
                 ]);

        $this->assertDatabaseHas('messages', [
            'content' => 'This is a test message',
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Тест получения данных конкретного сообщения.
     */
    public function test_can_show_message()
    {
        $message = Message::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/messages/' . $message->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $message->id]);
    }

    /**
     * Тест обновления сообщения.
     */
    public function test_can_update_message()
    {
        $message = Message::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
            'content' => 'Original message',
        ]);

        $updateData = [
            'content' => 'Updated message',
        ];

        $response = $this->json('PUT', '/api/messages/' . $message->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['content' => 'Updated message']);

        $this->assertDatabaseHas('messages', [
            'id'      => $message->id,
            'content' => 'Updated message',
        ]);
    }

    /**
     * Тест удаления сообщения.
     */
    public function test_can_delete_message()
    {
        $message = Message::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('DELETE', '/api/messages/' . $message->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Сообщение успешно удалено']);

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id,
        ]);
    }
}
