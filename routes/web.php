<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogueController;

Route::get('/catalogue/{group?}', [CatalogueController::class, 'index'])
    ->name('catalogue.index')
    ->where('group', '[0-9]+');

Route::get('/product/{product}', [CatalogueController::class, 'product'])
    ->name('product.show');

Route::get('/', function () {
    return redirect('/catalogue');
});
