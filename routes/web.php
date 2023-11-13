<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PhotoController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/photos', [PhotoController::class, 'index'])->name('photos');
Route::post('/photos/{id}', [PhotoController::class, 'save'])->name('photos_save');
Route::resource('/people', PeopleController::class)->except(['show']);

require __DIR__.'/auth.php';
