<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TemplateVersionController;
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

Route::get('/students/import', [StudentImportController::class, 'showImportForm'])->name('students.import')->middleware(['auth', 'role:tutor|admin']);
Route::post('/students/import', [StudentImportController::class, 'import'])->name('students.import.process')->middleware(['auth', 'role:tutor|admin']);
Route::post('/students/import/confirm', [StudentImportController::class, 'confirmImport'])->name('students.import.confirm')->middleware(['auth', 'role:tutor|admin']);
Route::resource('students', StudentsController::class)->middleware(['auth', 'role:tutor']);

Route::resource('files', FileController::class);
Route::get('/files-review', [FileController::class, 'review'])->name('files.review')->middleware(['auth', 'role:tutor']);
Route::patch('/files/{file}/mark-reviewed', [FileController::class, 'markReviewed'])->name('files.mark-reviewed')->middleware(['auth', 'role:tutor']);
Route::put('/files/{file}/observations', [FileController::class, 'updateObservations'])->name('files.update-observations')->middleware(['auth', 'role:tutor']);
Route::post('/files/{file}/content', [FileController::class, 'updateContent'])->name('files.content.update');
Route::get('/files/{file}/history', [FileController::class, 'history'])->name('files.history');
Route::get('/files/{file}/write', [FileController::class, 'write'])->name('files.write');
Route::post('/files/compare', [App\Http\Controllers\FileVersionController::class, 'compare'])->name('files.compare');
Route::get('/files/versions/{version}', [App\Http\Controllers\FileVersionController::class, 'show'])->name('files.versions.show');
Route::post('/files/versions/{version}/restore', [App\Http\Controllers\FileVersionController::class, 'restore'])->name('files.versions.restore');

// Templates routes - Protected for admin role only
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('templates', TemplateController::class);
    Route::post('/templates/{template}/content', [TemplateController::class, 'updateContent'])->name('templates.content.update');
    Route::get('/templates/{template}/history', [TemplateController::class, 'history'])->name('templates.history');
    Route::get('/templates/{template}/write', [TemplateController::class, 'write'])->name('templates.write');
    Route::post('/templates/compare', [TemplateVersionController::class, 'compare'])->name('templates.compare');
    Route::get('/templates/versions/{version}', [TemplateVersionController::class, 'show'])->name('templates.versions.show');
    Route::post('/templates/versions/{version}/restore', [TemplateVersionController::class, 'restore'])->name('templates.versions.restore');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
