## Задание:

1. Создать три таблицы в БД:
1.1. В одной хранятся имена пользователей.
1.2. Во второй - связь между пользователями и их образованием (среднее, бакалавр, магистр, еще что-то).
1.3. В третьей - связь между пользователями и некими городами, у каждого пользователя может быть 1 или более городов.
2. Написать приложение, которое будет:
2.1. Выводить список (grid) этих пользователей, в зависимости от отмеченных галочек (для каждой таблички свой набор галочек). Список должен обновляться без перезагрузки страницы. Поля: пользователь, образование, город.
2.2. Предоставлять возможность смены образования в этой таблице. При этом изменения должны отправляться на сервер и сохраняться в базе данных.
3. Приложение должно быть написано с использованием следующего инструментария:
3.1. Серверная часть должна быть написана на PHP. Можно использовать любой удобный framework (хотя Zend, конечно, предпочтительней).
3.2. Клиентская часть должна быть написана на ExtJS (версия по выбору).
4. В работе должно использоваться ООП, должен использоваться шаблон проектирования MVC или MVVM, сложная выборка должна реализовываться средствами SQL.
5. Приветствуются функции "от себя", показывающие уровень владения предметом.
6. Результат выполнения задания должен быть представлен в виде приложения и дампа базы, чтобы можно было «поднять» и посмотреть на его работу.
7. Решение предпочтительно выкладывать в GitHub.
---

## Решение:

### Проектирование базы данных
1.1. Создание трех таблиц:

Таблица `users`:

id (int, primary key, auto-increment)
name (string)

Таблица `educations`:

id (int, primary key, auto-increment)
user_id (int, foreign key to users.id)
degree (enum, например: 'среднее', 'бакалавр', 'магистр', 'другое')

Таблица `cities`:

id (int, primary key, auto-increment)
name (string)

Таблица `user_cities`: (для связи пользователей и городов)

id (int, primary key, auto-increment)
user_id (int, foreign key to users.id)
city_id (int, foreign key to cities.id)

---

## 1. Настройка клиентской части (ExtJS)
Установите ExtJS через npm:
```sh
npm install -g @sencha/ext-gen
```
Создайте новое приложение:
```sh
ext-gen app -a -t moderndesktop -n extjs
cd extjs
```
Запустите приложение, чтобы увидеть его в действии:
```sh
npm start
```


### 2. Настройка сервера (Laravel)
2.1. Установка Laravel и создание проекта:
```bash
composer create-project --prefer-dist laravel/laravel backend
cd backend
```
2.2. Создание миграций для таблиц:
В ларавел create_users_table уже существует. Поэтому ее не создаем.
Создайте миграции:
```bash
php artisan make:migration create_educations_table
php artisan make:migration create_cities_table
php artisan make:migration create_user_cities_table
```

Определите структуру таблиц в файлах миграций:
---
database/migrations/xxxx_xx_xx_create_educations_table.php:
```php
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
```
database/migrations/xxxx_xx_xx_create_cities_table.php:
```php
public function up()
{
    Schema::create('cities', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('cities');
}
```
database/migrations/xxxx_xx_xx_create_user_cities_table.php:
```php
public function up()
{
    Schema::create('user_cities', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('city_id');
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('user_cities');
}
```
Запустите миграции:
```bash
php artisan migrate
```
2.3. Создание моделей для таблиц
Создайте модели:
```bash
php artisan make:model Education
php artisan make:model City
php artisan make:model UserCity
```
Настройте модели:
* Модель User в Laravel уже есть, добавьте в нее следующее
app/Models/User.php:
```php
    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'user_cities');
    }
```
app/Models/Education.php:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'degree'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```
app/Models/City.php:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_cities');
    }
}
```
app/Models/UserCity.php:
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCity extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'city_id'];
}
```
2.4. Создание контроллеров для работы с пользователями и их образованием
Создайте контроллеры:
```bash
php artisan make:controller UserController
php artisan make:controller EducationController
php artisan make:controller CityController
```
Настройте контроллеры:
app/Http/Controllers/UserController.php:
```php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['educations', 'cities'])->get();
        return response()->json($users);
    }

    public function updateEducation(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->educations()->update(['degree' => $request->input('degree')]);
            return response()->json(['message' => 'Education updated successfully']);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
}
```
app/Http/Controllers/EducationController.php:
```php
namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    // Добавьте необходимые методы, если потребуется
}
```
app/Http/Controllers/CityController.php:
```php
namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // Добавьте необходимые методы, если потребуется
}
```
2.5. Настройка маршрутов API
Определите маршруты в файле routes/web.php:
```php
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserController::class, 'index']);
Route::post('/users/{id}/education', [UserController::class, 'updateEducation']);
```
Настройка CORS (при необходимости):
Если ваше приложение ExtJS будет запускаться на другом домене, убедитесь, что CORS настроен правильно.

Запуск сервера
проверьте что находитесь в папке backend (cd backend):
```sh
npm install
npm update
npm run dev
php artisan serve
```

## 3. Шаги по заполнению базы данных фейковыми данными.

Для заполнения базы данных фейковыми данными в Laravel, вы можете воспользоваться функцией сидирования (seeding). Laravel предоставляет удобные инструменты для создания и наполнения таблиц фейковыми данными. Для этого вы будете использовать фабрики и сидеры.


Создайте фабрики для ваших моделей:

Laravel предоставляет фабрики для удобного создания фейковых данных. Предположим, у вас есть модели User, Education и City.

Создайте фабрики для каждой модели:
```sh
php artisan make:factory UserFactory --model=User
php artisan make:factory EducationFactory --model=Education
php artisan make:factory CityFactory --model=City
```

Определите фабрики:
В файлах фабрик определите, как должны выглядеть фейковые данные.
Пример UserFactory: Оставляем ту что есть в laravel.

Пример EducationFactory:
```php
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
```
Пример CityFactory.php в директории database/factories:
```php
namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city,
        ];
    }
}
```
Создадим сидер, который будет использовать фабрики для заполнения базы данных фейковыми данными.

Создайте или обновите файл DatabaseSeeder.php в директории database/seeders
```sh
php artisan make:seeder DatabaseSeeder
```
В DatabaseSeeder вы можете вызвать фабрики для создания данных:
```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Education;
use App\Models\City;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Создаем пользователей
        User::factory()
            ->count(10) // Количество создаваемых пользователей
            ->has(Education::factory()->count(1), 'educations') // Каждый пользователь имеет одно образование
            ->create()
            ->each(function ($user) {
                // Присваиваем пользователям города
                $cities = City::factory()->count(3)->create();
                $user->cities()->attach($cities->pluck('id'));
            });
    }
}
```
Запустите сидеры для заполнения базы данных:
```sh
php artisan db:seed
```

Если нужно очистить базу данных:
```sh
php artisan migrate:fresh
```

## 4 Настройте клиентскую часть ExtJS

Скопируем имеющуюся папку laraextjs\extjs\app\desktop\src\view\personnel в laraextjs\extjs\app\desktop\src\view\user

Переименуем имеющиеся фвайлы в папке  laraextjs\extjs\app\desktop\src\view\user, добавим к ним просто символ 2 и отредактируем:

PersonnelView2.js:
```js
Ext.define('extjs.view.user.PersonnelView2', {
    extend: 'Ext.grid.Grid',
    xtype: 'personnelview2',
    cls: 'personnelview2',
    requires: ['Ext.grid.rowedit.Plugin'],
    controller: {type: 'personnelviewcontroller2'},
    viewModel: {type: 'personnelviewmodel2'},
    store: {type: 'personnelviewstore2'},
    grouped: true,
    groupFooter: {
        xtype: 'gridsummaryrow'
    },
    plugins: {
        rowedit: {
            autoConfirm: false
        }
    },
    columns: [
        {
            text: 'ID',
            dataIndex: 'id',
            width: 50
        },
        {
            text: 'Name',
            dataIndex: 'name',
            editable: true,
            width: 100,
            cell: {userCls: 'bold'}
        },
        {text: 'Email', dataIndex: 'email', editable: true, width: 230},
        {
            text: 'Education',
            dataIndex: 'education',
            editable: true,
            width: 150
        },
        {
            text: 'Cities',
            dataIndex: 'cities',
            width: 150
        }
    ],
    listeners: {
        edit: 'onEditComplete'
    }
});
```

PersonnelViewController2.js:
```js
Ext.define('extjs.view.user.PersonnelViewController2', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.personnelviewcontroller2',

    onEditComplete: function(editor, context) {
        var userId = context.record.get('id');
        var newDegree = context.record.get('education');

        Ext.Ajax.request({
            url: 'http://127.0.0.1:8000/api/users/' + userId + '/education',
            method: 'GET',
            params: {
                degree: newDegree
            },
            success: function(response) {
                Ext.Msg.alert('Success', 'Education updated successfully.');
                context.record.commit();  // Confirm changes in the grid
            },
            failure: function(response) {
                Ext.Msg.alert('Failure', 'Failed to update education.');
                context.record.reject();  // Revert changes in the grid
            }
        });
    }
});
```

PersonnelViewModel2.js:

```js
Ext.define('extjs.view.user.PersonnelViewModel2', {
	extend: 'Ext.app.ViewModel',
	alias: 'viewmodel.personnelviewmodel2',
	data: {
		name: 'extjs'
	}
});
```

PersonnelViewStore2.js:
```js
Ext.define('extjs.view.user.PersonnelViewStore2', {
    extend: 'Ext.data.Store',
    alias: 'store.personnelviewstore2',
    fields: [
        'id', 'name', 'email', {
            name: 'education',
            mapping: 'educations[0].degree'
        }, {
            name: 'cities',
            convert: function(value, record) {
                var cities = record.get('cities');
                return cities ? cities.map(city => city.name).join(', ') : '';
            }
        }
    ],
    proxy: {
        type: 'ajax',
        url: 'http://127.0.0.1:8000/api/users',
        reader: {
            type: 'json',
            rootProperty: ''
        }
    },
    autoLoad: true
});
```
Добавим в меню laraextjs\extjs\resources\desktop\menu.json:

```json
{
  "leaf": false,
  "children": [
    { "text": "User", "iconCls": "x-fa fa-table", "xtype": "personnelview2","leaf": true },
    { "text": "Home", "iconCls": "x-fa fa-home", "xtype": "homeview", "leaf": true },
    { "text": "Personnel", "iconCls": "x-fa fa-table", "xtype": "personnelview","leaf": true }
  ]
}
```

Запустите клиентскую часть:
```sh
npm start
```

Теперь у вас есть клиентская часть ExtJS 7.8.0 в корне проекта и есть серверная часть на laravel 11 в папке backend.

---

# Как скачать и запустить проект
Скачайте или склонируйте проект https://github.com/rreeggeenntt4/laraextjs
```sh
git clone https://github.com/rreeggeenntt4/laraextjs
cd laraextjs
```
Установиет необходимые модули на клиентской части Ext JS
```sh
npm install
```
Перейдите в папку backend и установите зависимости laravel
```sh
cd backend
composer install
npm install
```
* Переместите базу данных `laraextjs\backend\settings\database.sqlite` в папку `laraextjs\backend\database\database.sqlite` и удалите папку `backend\settings`
* Скопируйте и переименуйте `laraextjs\backend\.env.example` в `laraextjs\backend\.env`

Запустите серверную часть находясь в папке backend
```sh
npm run dev
php artisan serve
```
Запустите клиентскую часть находясь в папке extjs
```sh
cd extjs
npm start
```
---
## Видео на YouTube

[![Watch the video](https://img.youtube.com/vi/dfq-E7bDz24/0.jpg)](https://www.youtube.com/watch?v=dfq-E7bDz24)
