<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\CRM;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    /**
     * Указывает, с какой моделью работает эта фабрика.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Определение дефолтного состояния модели.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'phone'      => $this->faker->optional()->phoneNumber,
            'email'      => $this->faker->unique()->safeEmail,
            'position'   => $this->faker->optional()->jobTitle,
            // Поле company_id оставляем null, его можно переопределить при необходимости:
            'company_id' => null,
            // Обязательное поле crm_id создается через фабрику CRM
            'crm_id'     => CRM::factory(),
        ];
    }
}
