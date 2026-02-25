<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Account\MyAccountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyAccountController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\AuditLog\AuditLogController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\About\AboutController;
use App\Http\Controllers\Contact\ContactController;


Route::get('/', function () {
    return view('site.home');
})->name('home');

// Route::get('/dashboard', function () {
//     return view('dashboard.home');
// })->name('dashboard');

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


Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


// forgot password and reset password

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

Route::post('/forgot-password', ForgotPasswordController::class)
    ->name('forgot-password.submit');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', compact('token'));
})->name('password.reset');

Route::post('/reset-password', ResetPasswordController::class)
    ->name('password.update');


//my account


Route::middleware('auth')->group(function () {

    Route::get('/myaccount', [MyAccountController::class, 'index'])->name('myaccount');

    Route::post('/myaccount/profile', [MyAccountController::class, 'updateProfile'])->name('myaccount.profile');

    Route::post('/myaccount/password', [MyAccountController::class, 'updatePassword'])->name('myaccount.password');

});


Route::post('/register', RegisterController::class)->name('register.submit');
Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');


    //verification code otp

    Route::view("/verify-email/{email}", 'auth.verify-email')->name('email.verify');
    Route::post('/verify-email', VerifyAccountController::class)->name('email.verify.submit');




    //google auth

    Route::get('/auth/google/redirect',[GoogleAuthController::class,'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback',[GoogleAuthController::class,'handleGoogleCallback'])->name('google.callback');


    //categories


    //products

    Route::get('/products', function () {
        return view('site.product.product');
    })->name('products.index');



    //blog

    Route::get('/blog',[BlogController::class,'index'])->name('blog.index');


    //about

    Route::get('/about',[AboutController::class,'index'])->name('about.index');


    //contact

    Route::get('/contact',[ContactController::class,'index'])->name('contact.index');



    //authorization



// Dashboard: ممنوع على customer
Route::middleware(['auth', 'role:owner,admin,employee'])
    ->prefix('dashboard')
    ->group(function () {

        Route::get('/dashboard', fn () => view('dashboard.home'))->name('dashboard');

        // Audit logs (Admin/Owner)
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->middleware('permission:audit.view')
            ->name('audit.index');

        // Users management (موظفين) — Admin/Owner
        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users.index');

        // ... كمل نفس النمط لباقي الأقسام حسب permissions اللي عندك
    });






