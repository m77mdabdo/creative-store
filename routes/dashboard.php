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

        Route::get('/products', [DashboardProductController::class, 'index'])
            ->middleware('permission:products.view')
            ->name('products.index');

        Route::get('/categories', [DashboardCategoryController::class, 'index'])
            ->middleware('permission:categories.view')
            ->name('categories.index');

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

        Route::delete('/categories/{category}', [DashboardCategoryController::class, 'destroy'])
            ->middleware('permission:categories.delete')
            ->name('categories.destroy');

        Route::get('/contact-messages', [DashboardContactMessageController::class, 'index'])
            ->middleware('permission:contact.view')
            ->name('contact-messages.index');
        Route::get('/contact-messages/{message}', [DashboardContactMessageController::class, 'show'])
            ->middleware('permission:contact.view')
            ->name('contact-messages.show');
    });
