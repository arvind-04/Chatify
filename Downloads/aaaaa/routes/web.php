<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EnergyController;

Route::get('/', [EnergyController::class, 'index'])->name('energy.index');
Route::get('/forecast', [EnergyController::class, 'forecast']);
Route::get('/about', function () {
    return view('energy.about');
})->name('energy.about');
