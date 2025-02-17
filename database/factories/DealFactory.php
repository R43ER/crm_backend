<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\CRM;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    protected $model = Deal::class;

    public function definition()
    {
        return [
            // Создаем CRM через фабрику, если не передано явно
            'crm_id'               => CRM::factory(),
            // Ответственный пользователь
            'responsible_user_id'  => User::factory(),
            // Опциональная связь с компанией: создадим компанию и возьмем её id
            'company_id'           => Company::factory(),
            // Бюджет сделки с 2 знаками после запятой
            'budget'               => $this->faker->randomFloat(2, 1000, 100000),
            // Название сделки
            'title'                => $this->faker->sentence(3),
            // Статус сделки (например, "new", "in progress", "closed")
            'status'               => $this->faker->randomElement(['new', 'in progress', 'closed']),
        ];
    }
}
