<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/post/create', [PostController::class, 'Create']);
Route::get('/post/read/{id}', [PostController::class, 'Read']);
Route::get('/post/readall', [PostController::class, 'ReadAll']);
Route::get('/post/update/{id}', [PostController::class, 'Update']);
Route::get('/post/multupdate', [PostController::class, 'MultUpdate']);
Route::get('/post/delete/{id}', [PostController::class, 'Delete']);
Route::get('/', function () {
    return view('welcome');
});
