<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\testing;
use App\Http\Controllers\Auth;
use App\Http\Controllers\EventManager;
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
// reject request by middleware
Route::get('/reject', function (\Illuminate\Http\Request $request)
{
    return json_encode([
        'id' => 0,
        'message' => $request['msg'],
    ]);
});


// EVENT MANAGEMENT
Route::post('/make_new_event', [EventManager::class, 'make_event'])->middleware('auth');
Route::post('/give_points', [EventManager::class, 'give_mark'])->middleware('auth');

// create new event
//Route::post('/create_event', [Auth::class, 'create_event'])

/* testing */
Route::get('/req', [testing::class, 'index']);
Route::post('/post', [testing::class, 'handle_post']);
