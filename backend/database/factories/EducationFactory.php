<?php

namespace Database\Factories;

use App\Models\Education;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition()
    {
        $degrees = ['Среднее', 'Бакалавр', 'Магистр', 'Доктор'];
        return [
            'user_id' => User::factory(),
            'degree' => $this->faker->randomElement($degrees),
        ];
    }
}

