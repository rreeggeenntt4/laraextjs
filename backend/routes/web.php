<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// api
Route::get('api/users', [UserController::class, 'index']);
Route::get('api/users/{id}/education', [UserController::class, 'updateEducation']);
