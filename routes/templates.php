<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemplateController;

Route::post('/templates/{id}/content', [TemplateController::class, 'updateContent'])->name('templates.content.update');