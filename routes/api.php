<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::post('/register', [StudentController::class, 'register']);
Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/export', [StudentController::class, 'export']);
Route::put('/students/{id}', [StudentController::class,'update']);
Route::delete('/students/{id}', [StudentController::class,'destroy']);
