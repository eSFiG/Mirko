<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'registration']);
Route::post('/authorize', [AuthController::class, 'authorization']);

Route::middleware('auth:api')->group(function ()
{
    Route::get('/information', [UserController::class, 'getInformation']);

    Route::post('/createFile', [FileController::class, 'create']);
    Route::get('/downloadFile/{file}', [FileController::class, 'download']);
    Route::post('/updateFile/{file}', [FileController::class, 'update']);
    Route::delete('/deleteFile/{file}', [FileController::class, 'delete']);
});
