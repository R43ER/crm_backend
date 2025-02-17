<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\Deal;
use App\Models\Company;

class DealControllerTest extends TestCase
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
     * Тест получения списка сделок.
     */
    public function test_can_get_deals_list()
    {
        // Создаем 3 сделки, привязанных к нашей CRM и ответственному пользователю
        Deal::factory()->count(3)->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/deals');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания новой сделки.
     */
    public function test_can_create_deal()
    {
        // Опционально создадим компанию для привязки
        $company = Company::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        $data = [
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
            'company_id'          => $company->id,
            'budget'              => 5000.50,
            'title'               => 'Test Deal',
            'status'              => 'new',
        ];

        $response = $this->json('POST', '/api/deals', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'title'  => 'Test Deal',
                     'status' => 'new',
                     'crm_id' => $this->crm->id,
                     'responsible_user_id' => $this->user->id,
                 ]);

        $this->assertDatabaseHas('deals', [
            'title'  => 'Test Deal',
            'crm_id' => $this->crm->id,
        ]);
    }

    /**
     * Тест получения данных конкретной сделки.
     */
    public function test_can_show_deal()
    {
        $deal = Deal::factory()->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/deals/' . $deal->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $deal->id]);
    }

    /**
     * Тест обновления сделки.
     */
    public function test_can_update_deal()
    {
        $deal = Deal::factory()->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
            'title'               => 'Old Title',
            'status'              => 'new',
        ]);

        $updateData = [
            'title'  => 'Updated Title',
            'status' => 'in progress',
        ];

        $response = $this->json('PUT', '/api/deals/' . $deal->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'title'  => 'Updated Title',
                     'status' => 'in progress',
                 ]);

        $this->assertDatabaseHas('deals', [
            'id'    => $deal->id,
            'title' => 'Updated Title',
        ]);
    }

    /**
     * Тест удаления сделки.
     */
    public function test_can_delete_deal()
    {
        $deal = Deal::factory()->create([
            'crm_id'              => $this->crm->id,
            'responsible_user_id' => $this->user->id,
        ]);

        $response = $this->json('DELETE', '/api/deals/' . $deal->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Сделка успешно удалена']);

        $this->assertDatabaseMissing('deals', [
            'id' => $deal->id,
        ]);
    }
}
