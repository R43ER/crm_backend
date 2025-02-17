<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\Task;

class TaskControllerTest extends TestCase
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
     * Тест получения списка задач.
     */
    public function test_can_get_tasks_list()
    {
        // Создаем 3 задачи, привязанных к нашей CRM и ответственному пользователю
        Task::factory()->count(3)->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания новой задачи.
     */
    public function test_can_create_task()
    {
        $data = [
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
            'task_text'           => 'Follow up call',
            'type'                => 'call',
            'execution_start'     => '2025-03-07 11:00:00',
            'execution_end'       => '2025-03-07 13:30:00',
        ];

        $response = $this->json('POST', '/api/tasks', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'task_text' => 'Follow up call',
                     'type'      => 'call',
                 ]);

        $this->assertDatabaseHas('tasks', [
            'task_text' => 'Follow up call',
            'crm_id'    => $this->crm->id,
        ]);
    }

    /**
     * Тест получения данных конкретной задачи.
     */
    public function test_can_show_task()
    {
        $task = Task::factory()->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/tasks/' . $task->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $task->id]);
    }

    /**
     * Тест обновления задачи.
     */
    public function test_can_update_task()
    {
        $task = Task::factory()->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
            'task_text'           => 'Initial task',
        ]);

        $updateData = [
            'task_text' => 'Updated task',
        ];

        $response = $this->json('PUT', '/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['task_text' => 'Updated task']);

        $this->assertDatabaseHas('tasks', [
            'id'        => $task->id,
            'task_text' => 'Updated task',
        ]);
    }

    /**
     * Тест удаления задачи.
     */
    public function test_can_delete_task()
    {
        $task = Task::factory()->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
        ]);

        $response = $this->json('DELETE', '/api/tasks/' . $task->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Задание успешно удалено']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
