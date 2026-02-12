<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('site.home');
})->name('home');

Route::get('/cart', function () {
    return view('site.app.cart');
})->name('cart.index');

Route::get('checkout', function () {
    return view('site.app.cart');
})->name('checkout.index');


// auth login and rigister


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('recover-password', function () {
    return view('auth.recover-password');
})->name('password.request');

Route::get('forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


Route::post('/register', \App\Http\Controllers\Auth\RegisterController::class)->name('register.submit');

