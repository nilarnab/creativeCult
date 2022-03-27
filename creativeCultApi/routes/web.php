<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\testing;
use App\Http\Controllers\Auth;
use App\Http\Controllers\EventManager;
use App\Http\Controllers\RequestManager;
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
Route::post('/show_all_events', [EventManager::class, 'show_events'])->middleware('auth');
Route::post('/show_event_details', [EventManager::class, 'show_event_details']);

// REQUEST MANAGEMENT
//======================================
// create a new request
Route::post('/create_request', [RequestManager::class, 'add_request'])->middleware('auth');
// update request status
// untested
Route::post('/update_request_status', [RequestManager::class, 'update_request_status'])->middleware('auth');
// add pic to request
// untested
Route::post('/add_pic_to_request', [RequestManager::class, 'add_pic'])->middleware('auth');
Route::post('/insert_entry', [EventManager::class, 'insert_entry']);

// accept a request
Route::post('/accept_request', [RequestManager::class, 'accept_request'])->middleware('auth');

// show all the requests of client
Route::post('/show_all_requests', [RequestManager::class, 'show_requests']);
//Route::post('/show_all_requests', [RequestManager::class, 'show_requests'])->middleware('auth');
Route::post('/show_entry_details', [EventManager::class, 'entry_details']);

Route::post('/show_one_request', [RequestManager::class, 'show_one_request']);
Route::post('/show_attended_events', [EventManager::class, 'show_attended_events']);
Route::post('/set_price', [RequestManager::class, 'set_price']);

Route::post('/logout', [Auth::class, 'logout']);


/* testing */

Route::get('/req', [testing::class, 'index']);
Route::post('/post', [testing::class, 'handle_post']);
