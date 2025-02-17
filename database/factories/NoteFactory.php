<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\CRM;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition()
    {
        return [
            // Обязательные связи:
            'crm_id'     => CRM::factory(),
            'user_id'    => User::factory(), // автор заметки
            // Опциональные связи (по умолчанию null, можно переопределить):
            'company_id' => null,
            'contact_id' => null,
            'deal_id'    => null,
            // Текст заметки:
            'content'    => $this->faker->paragraph,
        ];
    }
}
