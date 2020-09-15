<?php

use Illuminate\Support\Facades\Auth;
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

Auth::routes(['login' => false]);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->get('/customers/average', function () {
    return Inertia::render('Customers/CustomersAverage');
})->name('customers-average');

Route::get('/login', function () {
    return Inertia::render('Profile/Login');
})->name('login');

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
