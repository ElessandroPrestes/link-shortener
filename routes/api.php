<?php

use App\Http\Controllers\Api\ShortLinkController;
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


Route::group(['prefix' => 'v1'], function () {
    Route::get('/links', [ShortLinkController::class, 'index']);
    Route::post('/links', [ShortLinkController::class, 'store']);
    Route::get('/links/{id}', [ShortLinkController::class, 'show']);
    Route::put('/links/{id}', [ShortLinkController::class, 'update']);
    Route::delete('/links/{id}', [ShortLinkController::class, 'destroy']);
    Route::get('/links/search/{text}', [ShortLinkController::class, 'searchText']);
});

