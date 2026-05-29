<?php

use App\Presentation\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', CatalogController::class)->name('catalog.index');
