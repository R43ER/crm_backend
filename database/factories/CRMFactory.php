<?php

namespace Database\Factories;

use App\Models\CRM;
use Illuminate\Database\Eloquent\Factories\Factory;

class CRMFactory extends Factory
{
    /**
     * Указывает, с какой моделью работает эта фабрика.
     *
     * @var string
     */
    protected $model = CRM::class;

    /**
     * Определяет дефолтное состояние модели.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->company,
            'subdomain' => $this->faker->unique()->slug,
            'avatar'    => $this->faker->optional()->imageUrl(640, 480, 'business', true),
            'website'   => $this->faker->optional()->url,
        ];
    }
}
