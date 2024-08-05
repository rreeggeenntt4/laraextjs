<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEducationsTable extends Migration
{
    public function up()
    {
        // Удаляем таблицу, если она существует
        Schema::dropIfExists('educations');

        // Создаем таблицу с ограничением CHECK
        DB::statement('CREATE TABLE educations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            degree TEXT CHECK(degree IN ("Среднее", "Бакалавр", "Магистр", "Доктор")),
            created_at TEXT,
            updated_at TEXT,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
        )');
    }

    public function down()
    {
        Schema::dropIfExists('educations');
    }
}

