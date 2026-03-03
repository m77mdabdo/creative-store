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
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Product\ProductController;
 use App\Http\Controllers\Cart\CartController;




Route::get('/',[HomeController::class,'index'])->name('home');
// Route::get('/dashboard', function () {
//     return view('dashboard.home');
// })->name('dashboard');



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


    Route::get('/products',[ProductController::class,'index'])->name('products.index');
    Route::get('/products/{slug}',[ProductController::class,'show'])->name('products.show');




    //blog

    Route::get('/blog',[BlogController::class,'index'])->name('blog.index');


    //about

    Route::get('/about',[AboutController::class,'index'])->name('about.index');


    //contact

    Route::get('/contact',[ContactController::class,'index'])->name('contact.index');


    //cart


Route::prefix('cart')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])->name('index');

    Route::middleware('auth')->group(function () {

        Route::post('/add', [CartController::class, 'add'])->name('add');

        Route::delete('/remove/{key}', [CartController::class, 'remove'])->name('remove');

        Route::post('/update', [CartController::class, 'update'])->name('update');

        Route::post('/clear', [CartController::class, 'clear'])->name('clear');

    });

});
Route::get('/cart/view',fn () => view('site.cart.cart-panel'));

//checkout

Route::get('/checkout', fn() => view('site.checkout.index'))->name('checkout.index');



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






