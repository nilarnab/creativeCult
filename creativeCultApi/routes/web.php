<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\testing;
use App\Http\Controllers\Auth;
use App\Http\Controllers\register;

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

Route::get('/', function () {
    return view('welcome');
});

/* registration */
Route::post('/register', [Auth::class, 'register']);
/* login */
Route::post('/login', [Auth::class, 'login']);

/* testing */
Route::get('/req', [testing::class, 'index']);
Route::post('/post', [testing::class, 'handle_post']);