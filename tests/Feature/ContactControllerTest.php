<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\Contact;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $crm;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём тестовую CRM
        $this->crm = CRM::factory()->create();

        // Создаём тестового пользователя, привязанного к этой CRM
        $this->user = User::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        // Аутентифицируем пользователя через Sanctum
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Тест получения списка контактов.
     */
    public function test_can_get_contacts_list()
    {
        // Создаем 3 контакта, привязанных к тестовой CRM
        Contact::factory()->count(3)->create([
            'crm_id' => $this->crm->id,
        ]);

        $response = $this->json('GET', '/api/contacts');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания нового контакта.
     */
    public function test_can_create_contact()
    {
        $data = [
            'first_name' => 'Alice',
            'last_name'  => 'Wonderland',
            'phone'      => '1234567890',
            'email'      => 'alice@example.com',
            'position'   => 'Manager',
            'crm_id'     => $this->crm->id,
        ];

        $response = $this->json('POST', '/api/contacts', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'first_name' => 'Alice',
                     'last_name'  => 'Wonderland',
                     'email'      => 'alice@example.com',
                 ]);

        $this->assertDatabaseHas('contacts', [
            'email'  => 'alice@example.com',
            'crm_id' => $this->crm->id,
        ]);
    }

    /**
     * Тест получения данных конкретного контакта.
     */
    public function test_can_show_contact()
    {
        $contact = Contact::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        $response = $this->json('GET', '/api/contacts/' . $contact->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $contact->id]);
    }

    /**
     * Тест обновления контакта.
     */
    public function test_can_update_contact()
    {
        $contact = Contact::factory()->create([
            'crm_id'     => $this->crm->id,
            'first_name' => 'Bob',
            'last_name'  => 'Jones',
        ]);

        $updateData = [
            'first_name' => 'Robert',
        ];

        $response = $this->json('PUT', '/api/contacts/' . $contact->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['first_name' => 'Robert']);

        $this->assertDatabaseHas('contacts', [
            'id'         => $contact->id,
            'first_name' => 'Robert',
        ]);
    }

    /**
     * Тест удаления контакта.
     */
    public function test_can_delete_contact()
    {
        $contact = Contact::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        $response = $this->json('DELETE', '/api/contacts/' . $contact->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Контакт успешно удален']);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
