<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\CRM;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            // Обязательная связь с CRM
            'crm_id' => CRM::factory(),
            // Обязательный автор сообщения
            'user_id' => User::factory(),
            // Опциональные связи, можно оставить null или переопределить при необходимости
            'company_id'       => null,
            'contact_id'       => null,
            'deal_id'          => null,
            'receiver_user_id' => null,
            // Текст сообщения
            'content' => $this->faker->paragraph,
        ];
    }
}
