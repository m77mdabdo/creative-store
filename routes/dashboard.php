<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\AuditLog\AuditLogController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Dashboard\ProductController as DashboardProductController;
use App\Http\Controllers\Dashboard\CategoryController as DashboardCategoryController;
use App\Http\Controllers\Dashboard\ContactMessageController as DashboardContactMessageController;

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
        Route::get('/products', [DashboardProductController::class, 'index'])
            ->middleware('permission:products.view')
            ->name('products.index');

        Route::get('/products/trash', [DashboardProductController::class, 'trash'])
            ->middleware('permission:products.view')
            ->name('products.trash');

        Route::post('/products/bulk-action', [DashboardProductController::class, 'bulkAction'])
            ->middleware('permission:products.edit')
            ->name('products.bulk-action');

        Route::get('/products/create', [DashboardProductController::class, 'create'])
            ->middleware('permission:products.create')
            ->name('products.create');

        Route::post('/products', [DashboardProductController::class, 'store'])
            ->middleware('permission:products.create')
            ->name('products.store');

        Route::get('/products/{product}', [DashboardProductController::class, 'show'])
            ->middleware('permission:products.view')
            ->name('products.show');

        Route::get('/products/{product}/edit', [DashboardProductController::class, 'edit'])
            ->middleware('permission:products.edit')
            ->name('products.edit');

        Route::put('/products/{product}', [DashboardProductController::class, 'update'])
            ->middleware('permission:products.edit')
            ->name('products.update');

        Route::patch('/products/{product}/toggle-status', [DashboardProductController::class, 'toggleStatus'])
            ->middleware('permission:products.edit')
            ->name('products.toggle-status');

        Route::patch('/products/{product}/toggle-featured', [DashboardProductController::class, 'toggleFeatured'])
            ->middleware('permission:products.edit')
            ->name('products.toggle-featured');

        Route::post('/products/{id}/restore', [DashboardProductController::class, 'restore'])
            ->middleware('permission:products.edit')
            ->name('products.restore');

        Route::delete('/products/{product}', [DashboardProductController::class, 'destroy'])
            ->middleware('permission:products.delete')
            ->name('products.destroy');

        Route::delete('/products/{id}/force-delete', [DashboardProductController::class, 'forceDelete'])
            ->middleware('permission:products.delete')
            ->name('products.force-delete');

        /*
        |--------------------------------------------------------------------------
        | Product Variants
        |--------------------------------------------------------------------------
        */
        Route::get('/products/{product}/variants', [DashboardProductController::class, 'variants'])
            ->middleware('permission:products.edit')
            ->name('products.variants.index');

        Route::post('/products/{product}/variants/generate', [DashboardProductController::class, 'generateVariants'])
            ->middleware('permission:products.edit')
            ->name('products.variants.generate');

        Route::post('/products/{product}/variants', [DashboardProductController::class, 'storeVariant'])
            ->middleware('permission:products.edit')
            ->name('products.variants.store');

        Route::put('/products/{product}/variants/{variant}', [DashboardProductController::class, 'updateVariant'])
            ->middleware('permission:products.edit')
            ->name('products.variants.update');

        Route::patch('/products/{product}/variants/{variant}/toggle-status', [DashboardProductController::class, 'toggleVariantStatus'])
            ->middleware('permission:products.edit')
            ->name('products.variants.toggle-status');

        Route::delete('/products/{product}/variants/{variant}', [DashboardProductController::class, 'destroyVariant'])
            ->middleware('permission:products.edit')
            ->name('products.variants.destroy');

        /*
        |--------------------------------------------------------------------------
        | Product Images
        |--------------------------------------------------------------------------
        */
        Route::get('/products/{product}/images', [DashboardProductController::class, 'images'])
            ->middleware('permission:products.edit')
            ->name('products.images.index');

        Route::post('/products/{product}/images', [DashboardProductController::class, 'storeImage'])
            ->middleware('permission:products.edit')
            ->name('products.images.store');

        Route::patch('/products/{product}/images/{image}/set-primary', [DashboardProductController::class, 'setPrimaryImage'])
            ->middleware('permission:products.edit')
            ->name('products.images.set-primary');

        Route::patch('/products/{product}/images/{image}/sort', [DashboardProductController::class, 'sortImage'])
            ->middleware('permission:products.edit')
            ->name('products.images.sort');

        Route::delete('/products/{product}/images/{image}', [DashboardProductController::class, 'destroyImage'])
            ->middleware('permission:products.edit')
            ->name('products.images.destroy');

        /*
        |--------------------------------------------------------------------------
        | Variant Images (Color / Variant Specific)
        |--------------------------------------------------------------------------
        */
        Route::post('/products/{product}/variants/{variant}/images', [DashboardProductController::class, 'storeVariantImage'])
            ->middleware('permission:products.edit')
            ->name('products.variants.images.store');

        Route::patch('/products/{product}/variants/{variant}/images/{image}/set-primary', [DashboardProductController::class, 'setPrimaryVariantImage'])
            ->middleware('permission:products.edit')
            ->name('products.variants.images.set-primary');

        Route::delete('/products/{product}/variants/{variant}/images/{image}', [DashboardProductController::class, 'destroyVariantImage'])
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

            
    });
