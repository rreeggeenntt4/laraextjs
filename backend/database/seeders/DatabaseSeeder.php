<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Education;
use App\Models\City;
use App\Models\UserCity; // Не забудьте импортировать модель UserCity

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Создаем города заранее
        $cities = City::factory()->count(5)->create(); // Создаем 5 уникальных городов

        // Создаем пользователей
        User::factory()
            ->count(10) // Количество создаваемых пользователей
            ->has(Education::factory()->count(1), 'educations') // Каждый пользователь имеет одно образование
            ->create()
            ->each(function ($user) use ($cities) {
                // Присваиваем пользователям случайные города
                $randomCities = $cities->random(rand(1, 3)); // Случайное количество городов от 1 до 3

                foreach ($randomCities as $city) {
                    UserCity::create([
                        'user_id' => $user->id,
                        'city_id' => $city->id,
                    ]);
                }
            });
    }
}

