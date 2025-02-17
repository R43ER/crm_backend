<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CRM;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * Имя модели, для которой создается фабрика.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Определение дефолтного состояния модели.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'    => $this->faker->company,
            'phone'   => $this->faker->optional()->phoneNumber,
            'email'   => $this->faker->unique()->safeEmail,
            'web'     => $this->faker->optional()->url,
            'address' => $this->faker->optional()->address,
            // Связываем компанию с CRM через фабрику CRM:
            'crm_id'  => CRM::factory(),
        ];
    }
}
