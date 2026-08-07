<?php

use App\Http\Controllers\Api\HuftApiController;
use App\Http\Controllers\Api\TraffictController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/traffict', [TraffictController::class, 'newTraffict'])->name('api.newTraffict');
Route::post('/traffict-microsite', [TraffictController::class, 'traffictmicrosite'])->name('api.newTraffictMicrosite');
Route::post('/track-click', [HuftApiController::class, 'trackButtonClick'])->name('api.track-click');
Route::post('/{url}', [HuftApiController::class, 'index'])->name('api.index');
