<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\CRM;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Имя модели, для которой создается фабрика.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Определяет дефолтное состояние модели.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'              => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password'          => Hash::make('password'), // пароль по умолчанию
            'remember_token'    => Str::random(10),
            // Связываем пользователя с CRM через фабрику CRM
            'crm_id'            => CRM::factory(),
            'phone'             => $this->faker->optional()->phoneNumber,
            'note'              => $this->faker->optional()->sentence,
            'avatar'            => $this->faker->optional()->imageUrl(200, 200, 'people', true),
        ];
    }
}
