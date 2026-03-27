<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;




Route::post('/register', [RegisteredUserController::class, 'apiRegister']);
// Route::post('/login', [AuthController::class, 'login']);

Route::post('/login', [AuthenticatedSessionController::class, 'apiLogin']);


Route::middleware('auth:sanctum')->group(function () {                //Sanctum = Secure API access using tokens
Route::get('/std', [PageController::class, 'fetchData']);
Route::post('/store', [PageController::class, 'store']);
Route::get('/edit/{id}', [PageController::class, 'edit']);
Route::post('/update/{id}', [PageController::class, 'update']);
Route::get('/delete/{id}', [PageController::class, 'delete']);
   
Route::get('/events', [PageController::class, 'getEvents']);
Route::post('/store-event', [PageController::class, 'storeEvent']);
Route::post('/update-event', [PageController::class, 'updateEvent']);
Route::post('/delete-event', [PageController::class, 'deleteEvent']);

});

