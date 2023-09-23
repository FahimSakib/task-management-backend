<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\TaskController;
use App\Models\User;
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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/get-all-users', function () {
        return User::all();
    });
    Route::post('/store-task', [TaskController::class, 'store']);
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/view-task/{id}', [TaskController::class, 'view']);
    Route::get('/edit-task/{id}', [TaskController::class, 'edit']);
    Route::put('/update-task/{id}', [TaskController::class, 'update']);
    Route::post('/add-comment', [CommentController::class, 'store']);
    Route::delete('delete-task/{id}', [TaskController::class, 'delete']);
});
