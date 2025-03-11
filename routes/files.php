<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::post('/files/{id}/content', [FileController::class, 'updateContent'])->name('files.content.update');