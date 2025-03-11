<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('files', FileController::class);
Route::post('/files/{file}/content', [FileController::class, 'updateContent'])->name('files.content.update');
Route::get('/files/{file}/history', [FileController::class, 'history'])->name('files.history');
Route::get('/files/{file}/write', [FileController::class, 'write'])->name('files.write');
Route::post('/files/compare', [App\Http\Controllers\FileVersionController::class, 'compare'])->name('files.compare');
Route::get('/files/versions/{version}', [App\Http\Controllers\FileVersionController::class, 'show'])->name('files.versions.show');
Route::post('/files/versions/{version}/restore', [App\Http\Controllers\FileVersionController::class, 'restore'])->name('files.versions.restore');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
