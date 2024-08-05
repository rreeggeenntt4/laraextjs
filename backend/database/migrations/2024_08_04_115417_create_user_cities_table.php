<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('user_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // Добавляем временные метки
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_cities');
    }
}
