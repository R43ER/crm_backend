<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\Note;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $crm;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем тестовую CRM
        $this->crm = CRM::factory()->create();

        // Создаем тестового пользователя, привязанного к этой CRM
        $this->user = User::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        // Аутентифицируем пользователя через Sanctum
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Тест получения списка примечаний.
     */
    public function test_can_get_notes_list()
    {
        // Создаем 3 заметки, привязанных к данной CRM и пользователю
        Note::factory()->count(3)->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/notes');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания нового примечания.
     */
    public function test_can_create_note()
    {
        $data = [
            'crm_id'     => $this->crm->id,
            'user_id'    => $this->user->id,
            'content'    => 'This is a test note',
            // Опциональные связи можно оставить пустыми:
            'company_id' => null,
            'contact_id' => null,
            'deal_id'    => null,
        ];

        $response = $this->json('POST', '/api/notes', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'content' => 'This is a test note',
                     'crm_id'  => $this->crm->id,
                     'user_id' => $this->user->id,
                 ]);

        $this->assertDatabaseHas('notes', [
            'content' => 'This is a test note',
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Тест получения данных конкретного примечания.
     */
    public function test_can_show_note()
    {
        $note = Note::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/notes/' . $note->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $note->id]);
    }

    /**
     * Тест обновления примечания.
     */
    public function test_can_update_note()
    {
        $note = Note::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
            'content' => 'Original content',
        ]);

        $updateData = [
            'content' => 'Updated content',
        ];

        $response = $this->json('PUT', '/api/notes/' . $note->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['content' => 'Updated content']);

        $this->assertDatabaseHas('notes', [
            'id'      => $note->id,
            'content' => 'Updated content',
        ]);
    }

    /**
     * Тест удаления примечания.
     */
    public function test_can_delete_note()
    {
        $note = Note::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('DELETE', '/api/notes/' . $note->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Примечание успешно удалено']);

        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }
}
