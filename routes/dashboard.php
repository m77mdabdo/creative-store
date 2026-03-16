<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\AuditLog\AuditLogController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Dashboard\ProductController as DashboardProductController;
use App\Http\Controllers\Dashboard\CategoryController as DashboardCategoryController;
use App\Http\Controllers\Dashboard\ContactMessageController as DashboardContactMessageController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProductVariantController;
use App\Http\Controllers\Dashboard\ProductImageController;
use App\Http\Controllers\Dashboard\VariantImageController;
use App\Http\Controllers\Dashboard\AttributeController;
use App\Http\Controllers\Dashboard\AttributeValueController;
use App\Http\Controllers\Dashboard\ProductReviewController;



Route::prefix('dashboard')
    ->as('dashboard.')
    ->middleware(['auth', 'role:owner,admin,employee'])
    ->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('home');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->middleware('permission:audit.view')
            ->name('audit.index');

        Route::get('/orders', [DashboardOrderController::class, 'index'])
            ->middleware('permission:orders.view')
            ->name('orders.index');

        Route::get('/orders/{order}', [DashboardOrderController::class, 'show'])
            ->middleware('permission:orders.view')
            ->name('orders.show');

     /*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('permission:products.view')
    ->name('products.index');

Route::get('/products/trash', [ProductController::class, 'trash'])
    ->middleware('permission:products.view')
    ->name('products.trash');

Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])
    ->middleware('permission:products.edit')
    ->name('products.bulk-action');

Route::get('/products/create', [ProductController::class, 'create'])
    ->middleware('permission:products.create')
    ->name('products.create');

Route::post('/products', [ProductController::class, 'store'])
    ->middleware('permission:products.create')
    ->name('products.store');

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->middleware('permission:products.view')
    ->name('products.show');

Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
    ->middleware('permission:products.edit')
    ->name('products.edit');

Route::put('/products/{product}', [ProductController::class, 'update'])
    ->middleware('permission:products.edit')
    ->name('products.update');

Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
    ->middleware('permission:products.edit')
    ->name('products.toggle-status');

Route::patch('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])
    ->middleware('permission:products.edit')
    ->name('products.toggle-featured');

Route::post('/products/{id}/restore', [ProductController::class, 'restore'])
    ->middleware('permission:products.edit')
    ->name('products.restore');

Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->middleware('permission:products.delete')
    ->name('products.destroy');

Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])
    ->middleware('permission:products.delete')
    ->name('products.force-delete');

/*
|--------------------------------------------------------------------------
| Product Variants
|--------------------------------------------------------------------------
*/
Route::get('/products/{product}/variants', [ProductVariantController::class, 'index'])
    ->middleware('permission:products.edit')
    ->name('products.variants.index');

Route::post('/products/{product}/variants/generate', [ProductVariantController::class, 'generate'])
    ->middleware('permission:products.edit')
    ->name('products.variants.generate');

Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])
    ->middleware('permission:products.edit')
    ->name('products.variants.store');

Route::put('/products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])
    ->middleware('permission:products.edit')
    ->name('products.variants.update');

Route::patch('/products/{product}/variants/{variant}/toggle-status', [ProductVariantController::class, 'toggleStatus'])
    ->middleware('permission:products.edit')
    ->name('products.variants.toggle-status');

Route::delete('/products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])
    ->middleware('permission:products.edit')
    ->name('products.variants.destroy');

/*
|--------------------------------------------------------------------------
| Product Images
|--------------------------------------------------------------------------
*/
Route::get('/products/{product}/images', [ProductImageController::class, 'index'])
    ->middleware('permission:products.edit')
    ->name('products.images.index');

Route::post('/products/{product}/images', [ProductImageController::class, 'store'])
    ->middleware('permission:products.edit')
    ->name('products.images.store');

Route::patch('/products/{product}/images/{image}/set-primary', [ProductImageController::class, 'setPrimary'])
    ->middleware('permission:products.edit')
    ->name('products.images.set-primary');

Route::patch('/products/{product}/images/{image}/sort', [ProductImageController::class, 'sort'])
    ->middleware('permission:products.edit')
    ->name('products.images.sort');

Route::delete('/products/{product}/images/{image}', [ProductImageController::class, 'destroy'])
    ->middleware('permission:products.edit')
    ->name('products.images.destroy');

/*
|--------------------------------------------------------------------------
| Variant Images
|--------------------------------------------------------------------------
*/
Route::post('/products/{product}/variants/{variant}/images', [VariantImageController::class, 'store'])
    ->middleware('permission:products.edit')
    ->name('products.variants.images.store');

Route::patch('/products/{product}/variants/{variant}/images/{image}/set-primary', [VariantImageController::class, 'setPrimary'])
    ->middleware('permission:products.edit')
    ->name('products.variants.images.set-primary');

Route::delete('/products/{product}/variants/{variant}/images/{image}', [VariantImageController::class, 'destroy'])
    ->middleware('permission:products.edit')
    ->name('products.variants.images.destroy');
        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */
        Route::get('/categories', [DashboardCategoryController::class, 'index'])
            ->middleware('permission:categories.view')
            ->name('categories.index');

        Route::get('/categories/trash', [DashboardCategoryController::class, 'trash'])
            ->middleware('permission:categories.view')
            ->name('categories.trash');

        Route::post('/categories/bulk-action', [DashboardCategoryController::class, 'bulkAction'])
            ->middleware('permission:categories.edit')
            ->name('categories.bulk-action');

        Route::get('/categories/create', [DashboardCategoryController::class, 'create'])
            ->middleware('permission:categories.create')
            ->name('categories.create');

        Route::post('/categories', [DashboardCategoryController::class, 'store'])
            ->middleware('permission:categories.create')
            ->name('categories.store');

        Route::get('/categories/{category}', [DashboardCategoryController::class, 'show'])
            ->middleware('permission:categories.view')
            ->name('categories.show');

        Route::get('/categories/{category}/edit', [DashboardCategoryController::class, 'edit'])
            ->middleware('permission:categories.edit')
            ->name('categories.edit');

        Route::put('/categories/{category}', [DashboardCategoryController::class, 'update'])
            ->middleware('permission:categories.edit')
            ->name('categories.update');

        Route::patch('/categories/{category}/toggle-status', [DashboardCategoryController::class, 'toggleStatus'])
            ->middleware('permission:categories.edit')
            ->name('categories.toggle-status');

        Route::post('/categories/{id}/restore', [DashboardCategoryController::class, 'restore'])
            ->middleware('permission:categories.edit')
            ->name('categories.restore');

        Route::delete('/categories/{category}', [DashboardCategoryController::class, 'destroy'])
            ->middleware('permission:categories.delete')
            ->name('categories.destroy');

        Route::delete('/categories/{id}/force-delete', [DashboardCategoryController::class, 'forceDelete'])
            ->middleware('permission:categories.delete')
            ->name('categories.force-delete');

        /*
        |--------------------------------------------------------------------------
        | Contact Messages
        |--------------------------------------------------------------------------
        */
        Route::get('/contact-messages', [DashboardContactMessageController::class, 'index'])
            ->middleware('permission:contact.view')
            ->name('contact-messages.index');

        Route::get('/contact-messages/{message}', [DashboardContactMessageController::class, 'show'])
            ->middleware('permission:contact.view')
            ->name('contact-messages.show');

            /*
|--------------------------------------------------------------------------
| Attributes
|--------------------------------------------------------------------------
*/
Route::get('/attributes', [AttributeController::class, 'index'])
    ->middleware('permission:attributes.view')
    ->name('attributes.index');

Route::get('/attributes/create', [AttributeController::class, 'create'])
    ->middleware('permission:attributes.create')
    ->name('attributes.create');

Route::post('/attributes', [AttributeController::class, 'store'])
    ->middleware('permission:attributes.create')
    ->name('attributes.store');

Route::get('/attributes/{attribute}', [AttributeController::class, 'show'])
    ->middleware('permission:attributes.view')
    ->name('attributes.show');

Route::get('/attributes/{attribute}/edit', [AttributeController::class, 'edit'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.edit');

Route::put('/attributes/{attribute}', [AttributeController::class, 'update'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.update');

Route::patch('/attributes/{attribute}/toggle-status', [AttributeController::class, 'toggleStatus'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.toggle-status');

Route::delete('/attributes/{attribute}', [AttributeController::class, 'destroy'])
    ->middleware('permission:attributes.delete')
    ->name('attributes.destroy');

/*
|--------------------------------------------------------------------------
| Attribute Values
|--------------------------------------------------------------------------
*/
Route::get('/attributes/{attribute}/values/create', [AttributeValueController::class, 'create'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.values.create');

Route::post('/attributes/{attribute}/values', [AttributeValueController::class, 'store'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.values.store');

Route::get('/attributes/{attribute}/values/{value}/edit', [AttributeValueController::class, 'edit'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.values.edit');

Route::put('/attributes/{attribute}/values/{value}', [AttributeValueController::class, 'update'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.values.update');

Route::patch('/attributes/{attribute}/values/{value}/toggle-status', [AttributeValueController::class, 'toggleStatus'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.values.toggle-status');

Route::delete('/attributes/{attribute}/values/{value}', [AttributeValueController::class, 'destroy'])
    ->middleware('permission:attributes.edit')
    ->name('attributes.values.destroy');



/*
|--------------------------------------------------------------------------
| Product Reviews
|--------------------------------------------------------------------------
*/
Route::get('/reviews', [ProductReviewController::class, 'index'])
    ->middleware('permission:reviews.view')
    ->name('reviews.index');

Route::post('/reviews/bulk-action', [ProductReviewController::class, 'bulkAction'])
    ->middleware('permission:reviews.edit')
    ->name('reviews.bulk-action');

Route::get('/reviews/{review}', [ProductReviewController::class, 'show'])
    ->middleware('permission:reviews.view')
    ->name('reviews.show');

Route::patch('/reviews/{review}/toggle-status', [ProductReviewController::class, 'toggleStatus'])
    ->middleware('permission:reviews.edit')
    ->name('reviews.toggle-status');

Route::delete('/reviews/{review}', [ProductReviewController::class, 'destroy'])
    ->middleware('permission:reviews.delete')
    ->name('reviews.destroy');

Route::get('/products/{product}/reviews', [ProductReviewController::class, 'productReviews'])
    ->middleware('permission:reviews.view')
    ->name('products.reviews.index');

    });
