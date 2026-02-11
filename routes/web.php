<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('site.home');
});

Route::get('/cart', function () {
    return view('site.app.cart');
})->name('cart.index');

Route::get('checkout', function () {
    return view('site.app.cart');
})->name('checkout.index');
