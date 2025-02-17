<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\Company;

class CompanyControllerTest extends TestCase
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
     * Тест получения списка компаний.
     */
    public function test_can_get_companies_list()
    {
        Company::factory()->count(3)->create([
            'crm_id' => $this->crm->id,
        ]);

        $response = $this->json('GET', '/api/companies');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания новой компании.
     */
    public function test_can_create_company()
    {
        $data = [
            'name'    => 'Test Company',
            'phone'   => '1234567890',
            'email'   => 'company@example.com',
            'web'     => 'https://example.com',
            'address' => '123 Main St, City',
            'crm_id'  => $this->crm->id,
        ];

        $response = $this->json('POST', '/api/companies', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name'   => 'Test Company',
                     'email'  => 'company@example.com',
                     'crm_id' => $this->crm->id,
                 ]);

        $this->assertDatabaseHas('companies', [
            'email'  => 'company@example.com',
            'crm_id' => $this->crm->id,
        ]);
    }

    /**
     * Тест получения данных конкретной компании.
     */
    public function test_can_show_company()
    {
        $company = Company::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        $response = $this->json('GET', '/api/companies/' . $company->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $company->id]);
    }

    /**
     * Тест обновления данных компании.
     */
    public function test_can_update_company()
    {
        $company = Company::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        $updateData = [
            'name' => 'Updated Company Name',
        ];

        $response = $this->json('PUT', '/api/companies/' . $company->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Company Name']);

        $this->assertDatabaseHas('companies', [
            'id'   => $company->id,
            'name' => 'Updated Company Name',
        ]);
    }

    /**
     * Тест удаления компании.
     */
    public function test_can_delete_company()
    {
        $company = Company::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        $response = $this->json('DELETE', '/api/companies/' . $company->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Компания успешно удалена']);

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
        ]);
    }
}
