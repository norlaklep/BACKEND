<?php

use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/users', [UserController::class,'register']);
Route::post('/users/login', [UserController::class,'login']);

Route::middleware([ApiAuthMiddleware::class])->group(function () {
    Route::get('/users/current', [UserController::class,'get']);
    Route::patch('/users/current', [UserController::class,'update']);
    Route::delete('/users/logout', [UserController::class,'logout']);


    Route::get('/places', [PlaceController::class, 'index']);
    Route::post('/places', [PlaceController::class, 'store']); 
    Route::get('/places/{id}', [PlaceController::class, 'show']);
    Route::patch('/places/{id}', [PlaceController::class, 'update']);
    Route::delete('/places/{id}', [PlaceController::class, 'destroy']);
});
