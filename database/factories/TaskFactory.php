<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\CRM;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        // Получаем случайные даты, но оборачиваем в переменные, чтобы избежать вызова format() на null
        $executionStart = $this->faker->optional()->dateTimeBetween('now', '+1 month');
        $executionEnd   = $this->faker->optional()->dateTimeBetween('+1 month', '+2 months');

        return [
            'crm_id'              => CRM::factory(),
            'responsible_user_id' => User::factory(),
            'contact_id'          => null,
            'company_id'          => null,
            'deal_id'             => null,
            'task_text'           => $this->faker->sentence,
            'result'              => $this->faker->optional()->sentence,
            'type'                => $this->faker->randomElement(['call', 'meeting', 'email']),
            'execution_start'     => $executionStart ? $executionStart->format('Y-m-d H:i:s') : null,
            'execution_end'       => $executionEnd ? $executionEnd->format('Y-m-d H:i:s') : null,
        ];
    }
}
