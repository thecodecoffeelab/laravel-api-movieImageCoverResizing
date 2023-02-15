<?php

use App\Http\Controllers\V1\MovieManipulationController;
use App\Http\Controllers\V1\MovieController;
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

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('v1')->group(function () {

        Route::apiResource('movie', MovieController::class);
        Route::get('cover', [MovieManipulationController::class, 'index']);
        Route::get('cover/by-movie/{movie}', [MovieManipulationController::class, 'byMovie']);
        Route::get('cover/{movie}', [MovieManipulationController::class, 'show']);
        Route::post('cover/resize', [MovieManipulationController::class, 'resize']);
        Route::delete('cover/{cover}', [MovieManipulationController::class, 'destroy']);    
    });
});



/* Route::prefix('v1')->group(function() {
    //Version 1
    Route::apiResource('os', OSV1Controller::class);
}); */

//ROUTE TO SEND REQUEST TO THE API | Api Resource gives access to CRUD functionnalities
        /* Route::apiResource('movie', \App\Http\Controllers\MovieController::class); */

//IMPLEMENTING VERSIONNING | VERSION 1 Route
