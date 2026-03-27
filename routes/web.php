<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;



Route::get('/', [PageController::class, 'index']);


// =====================
// 🧑‍💼 EMPLOYEE CRUD
// =====================


// Route::get('/std', [PageController::class, 'index']);
// Route::post('/store', [PageController::class, 'store']);
// Route::get('/edit/{id}', [PageController::class, 'edit']);
// Route::post('/update/{id}', [PageController::class, 'update']);
// Route::get('/delete/{id}', [PageController::class, 'delete']);

//---- calendar ----//

// Route::get('/events', [PageController::class, 'getEvents']);
// Route::post('/store-event', [PageController::class, 'storeEvent']);
// Route::post('/update-event', [PageController::class, 'updateEvent']);
// Route::post('/delete-event', [PageController::class, 'deleteEvent']);



Route::get('/', function () {
    return redirect('/login');
});


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


Route::get('/std', [PageController::class, 'index']);
Route::post('/store', [PageController::class, 'store']);
Route::get('/edit/{id}', [PageController::class, 'edit']);
Route::post('/update/{id}', [PageController::class, 'update']);
Route::get('/delete/{id}', [PageController::class, 'delete']);
   
Route::get('/events', [PageController::class, 'getEvents']);
Route::post('/store-event', [PageController::class, 'storeEvent']);
Route::post('/update-event', [PageController::class, 'updateEvent']);
Route::post('/delete-event', [PageController::class, 'deleteEvent']);
});

require __DIR__.'/auth.php';
