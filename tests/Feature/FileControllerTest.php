<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\CRM;
use App\Models\User;
use App\Models\File;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $crm;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем тестовую CRM
        $this->crm = CRM::factory()->create();

        // Создаем тестового пользователя, привязанного к CRM
        $this->user = User::factory()->create([
            'crm_id' => $this->crm->id,
        ]);

        // Аутентифицируем пользователя через Sanctum
        $this->actingAs($this->user, 'sanctum');
    }

    /**
     * Тест получения списка файлов.
     */
    public function test_can_get_files_list()
    {
        // Создаем 3 записи файлов через фабрику
        File::factory()->count(3)->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/files');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * Тест создания файла с загрузкой.
     */
    public function test_can_create_file()
    {
        // Подменяем хранилище, чтобы не сохранять файлы на диск реально
        Storage::fake('public');

        // Создаем тестовый файл
        $uploadedFile = UploadedFile::fake()->image('testfile.jpg');

        $data = [
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
            // Опциональные связи можно не передавать или передать null
            'file'    => $uploadedFile,
        ];

        $response = $this->json('POST', '/api/files', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'file_name' => 'testfile.jpg',
                     'crm_id'    => $this->crm->id,
                     'user_id'   => $this->user->id,
                 ]);

        // Проверяем, что файл сохранен на диске (в каталоге uploads)
        Storage::disk('public')->assertExists('uploads/' . $uploadedFile->hashName());

        // Проверяем, что запись создана в базе данных
        $this->assertDatabaseHas('files', [
            'file_name' => 'testfile.jpg',
            'crm_id'    => $this->crm->id,
            'user_id'   => $this->user->id,
        ]);
    }

    /**
     * Тест получения данных конкретного файла.
     */
    public function test_can_show_file()
    {
        $file = File::factory()->create([
            'crm_id'  => $this->crm->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->json('GET', '/api/files/' . $file->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $file->id]);
    }

    /**
     * Тест обновления файла (замена загруженного файла).
     */
    public function test_can_update_file()
    {
        Storage::fake('public');

        // Создаем первоначальную запись файла с фиктивными данными
        $file = File::factory()->create([
            'crm_id'    => $this->crm->id,
            'user_id'   => $this->user->id,
            'file_path' => 'uploads/oldfile.jpg',
            'file_name' => 'oldfile.jpg',
        ]);

        // Подготавливаем новый тестовый файл
        $uploadedFile = UploadedFile::fake()->image('newfile.jpg');

        $data = [
            'file' => $uploadedFile,
        ];

        $response = $this->json('PUT', '/api/files/' . $file->id, $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['file_name' => 'newfile.jpg']);

        // Проверяем, что новый файл сохранен
        Storage::disk('public')->assertExists('uploads/' . $uploadedFile->hashName());

        // Проверяем, что запись в базе данных обновлена
        $this->assertDatabaseHas('files', [
            'id'        => $file->id,
            'file_name' => 'newfile.jpg',
        ]);
    }

    /**
     * Тест удаления файла.
     */
    public function test_can_delete_file()
    {
        Storage::fake('public');

        // Создаем тестовый файл и сохраняем его на диск
        $uploadedFile = UploadedFile::fake()->image('tobedeleted.jpg');
        $path = $uploadedFile->store('uploads', 'public');

        $file = File::factory()->create([
            'crm_id'    => $this->crm->id,
            'user_id'   => $this->user->id,
            'file_path' => $path,
            'file_name' => 'tobedeleted.jpg',
        ]);

        // Убеждаемся, что файл существует на диске
        Storage::disk('public')->assertExists($path);

        $response = $this->json('DELETE', '/api/files/' . $file->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Файл успешно удалён']);

        // Проверяем, что файл удален с диска
        Storage::disk('public')->assertMissing($path);

        // Проверяем, что запись отсутствует в базе данных
        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }
}
