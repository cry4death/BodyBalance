<?php

use App\Http\Controllers\RobotsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('robots.txt', RobotsController::class)->name('robots');
