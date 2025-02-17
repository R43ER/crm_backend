<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\CRM;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition()
    {
        return [
            // Обязательные связи
            'crm_id'     => CRM::factory(),
            'user_id'    => User::factory(),
            // Опциональные связи оставляем null
            'company_id' => null,
            'contact_id' => null,
            'deal_id'    => null,
            'note_id'    => null,
            // Для тестов загрузка файла обычно происходит через контроллер,
            // поэтому здесь мы просто генерируем строковые значения.
            'file_path'  => 'uploads/' . $this->faker->lexify('????????') . '.jpg',
            'file_name'  => $this->faker->word . '.jpg',
        ];
    }
}
