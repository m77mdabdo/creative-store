<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\About\AboutController;
use App\Http\Controllers\Account\MyAccountController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyAccountController;
use App\Http\Controllers\Blog\BlogCommentController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Cart\CartRecoveryController;
use App\Http\Controllers\Checkout\CheckoutController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Frontend\BlogController as FrontBlogController;
use App\Http\Controllers\Frontend\PageController as FrontPageController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\MoyasarPayment\MoyasarPaymentController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ReviewController;
use App\Http\Controllers\Shipping\AddressController;
use App\Http\Controllers\Shipping\OrderController;
use App\Http\Controllers\Shipping\ShippingController;
use App\Http\Controllers\Tracking\CustomerTrackingController;
use App\Http\Controllers\Wishlist\WishlistController;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
    ],
], function () {

    /*
    |--------------------------------------------------------------------------
    | Home
    |--------------------------------------------------------------------------
    */

    Route::get('/', [HomeController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', RegisterController::class)->name('register.submit');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.submit');

    Route::post('/logout', function () {
        Auth::logout();

        return redirect()->route('home');
    })->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Route::view('/verify-email/{email}', 'auth.verify-email')->name('email.verify');

    Route::post('/verify-email', VerifyAccountController::class)
        ->name('email.verify.submit');

    /*
    |--------------------------------------------------------------------------
    | Google Auth
    |--------------------------------------------------------------------------
    */

    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle'])
        ->name('google.redirect');

    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
        ->name('google.callback');

    /*
    |--------------------------------------------------------------------------
    | My Account
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth')->group(function () {
        Route::get('/myaccount', [MyAccountController::class, 'index'])->name('myaccount');

        Route::post('/myaccount/profile', [MyAccountController::class, 'updateProfile'])
            ->name('myaccount.profile');

        Route::post('/myaccount/password', [MyAccountController::class, 'updatePassword'])
            ->name('myaccount.password');
    });

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

    /*
    |--------------------------------------------------------------------------
    | Blog
    |--------------------------------------------------------------------------
    */

    Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

    Route::post('/blog/{blog}/comment', [BlogCommentController::class, 'store'])
        ->middleware('auth')
        ->name('blog.comment.store');

    // Route::get('/blog/{slug}', [FrontBlogController::class, 'show'])
    //     ->name('blog.show');

    /*
    |--------------------------------------------------------------------------
    | Static Pages
    |--------------------------------------------------------------------------
    */

    Route::get('/about', [AboutController::class, 'index'])->name('about.index');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

    Route::get('/pages/{slug}', [FrontPageController::class, 'show'])
        ->name('frontend.pages.show');

    /*
    |--------------------------------------------------------------------------
    | Cart
    |--------------------------------------------------------------------------
    */

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');

        Route::middleware('auth')->group(function () {
            Route::post('/add', [CartController::class, 'add'])->name('add');

            Route::delete('/remove/{key}', [CartController::class, 'remove'])
                ->where('key', '.*')
                ->name('remove');

            Route::post('/update', [CartController::class, 'update'])->name('update');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');

            Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon');

            Route::delete('/coupon/remove', [CartController::class, 'removeCoupon'])
                ->name('coupon.remove');

            Route::post('/coupon/preview', [CartController::class, 'previewCoupon'])
                ->name('coupon.preview');
        });
    });

    Route::get('/cart/view', fn () => view('site.cart.cart-panel'))->name('cart.view');

    Route::get('/cart/recover/{token}', [CartRecoveryController::class, 'recover'])
        ->name('cart.recover');

    /*
    |--------------------------------------------------------------------------
    | Checkout
    |--------------------------------------------------------------------------
    */

    Route::get('/checkout', fn () => view('site.checkout.index'))->name('checkout.index');

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth')->post('/product/review/store', [ReviewController::class, 'store'])
        ->name('reviews.store');

    /*
    |--------------------------------------------------------------------------
    | Shipping
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth')->prefix('shipping')->name('shipping.')->group(function () {
        Route::get('/', [ShippingController::class, 'index'])->name('index');
        Route::post('/options', [ShippingController::class, 'shippingOptions'])->name('options');
        Route::post('/select-address', [ShippingController::class, 'selectAddress'])->name('select-address');
        Route::post('/select-method', [ShippingController::class, 'selectMethod'])->name('select-method');

        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');

        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])
            ->name('orders.invoice');
    });

    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth')->group(function () {
        Route::get('/wishlist', [WishlistController::class, 'index'])
            ->name('wishlist.index');

        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])
            ->name('wishlist.toggle');

        Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])
            ->name('wishlist.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth')->group(function () {
        Route::post('/shipping/place-order', [CheckoutController::class, 'placeOrder'])
            ->name('shipping.place-order');

        Route::get('/checkout/payment/callback', [MoyasarPaymentController::class, 'callback'])
            ->name('checkout.payment.callback');

        Route::get('/checkout/payment/{order}', [MoyasarPaymentController::class, 'show'])
            ->whereNumber('order')
            ->name('checkout.payment');

        Route::get('/order/success/{order}', [MoyasarPaymentController::class, 'success'])
            ->whereNumber('order')
            ->name('order.success');
    });

    /*
    |--------------------------------------------------------------------------
    | Order Tracking
    |--------------------------------------------------------------------------
    */

    Route::get('/track-order', [CustomerTrackingController::class, 'searchForm'])
        ->name('tracking.search-form');

    Route::post('/track-order', [CustomerTrackingController::class, 'track'])
        ->name('tracking.track');
});

/*
|--------------------------------------------------------------------------
| Non-localized Routes
|--------------------------------------------------------------------------
|
| Keep webhooks and third-party callbacks outside localization prefixes.
|
*/

Route::post('/webhooks/moyasar', [MoyasarPaymentController::class, 'webhook'])
    ->name('webhooks.moyasar');
