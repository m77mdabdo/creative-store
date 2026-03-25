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
use App\Http\Controllers\Dashboard\CouponController;
use App\Http\Controllers\Dashboard\PaymentController;
use App\Http\Controllers\Dashboard\RefundController;
use App\Http\Controllers\Dashboard\ReturnController;
use App\Http\Controllers\Dashboard\ShippingMethodController;
use App\Http\Controllers\Dashboard\ShippingZoneController;
use App\Http\Controllers\Dashboard\ShippingRateController;
use App\Http\Controllers\Dashboard\ShipmentController;




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

        Route::get('/orders/{order}/invoice', [DashboardOrderController::class, 'invoice'])
            ->middleware('permission:orders.view')
            ->name('orders.invoice');

        Route::patch('/orders/{order}/statuses', [DashboardOrderController::class, 'updateStatuses'])
            ->middleware('permission:orders.edit')
            ->name('orders.update-statuses');

        Route::patch('/orders/{order}/quick-action', [DashboardOrderController::class, 'quickAction'])
            ->middleware('permission:orders.edit')
            ->name('orders.quick-action');

        Route::post('/orders/{order}/cancel', [DashboardOrderController::class, 'cancel'])
            ->middleware('permission:orders.edit')
            ->name('orders.cancel');

        Route::post('/orders/{order}/shipment', [DashboardOrderController::class, 'upsertShipment'])
            ->middleware('permission:orders.edit')
            ->name('orders.shipment.upsert');
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

            /*
|--------------------------------------------------------------------------
| Coupons
|--------------------------------------------------------------------------
*/
Route::get('/coupons', [CouponController::class, 'index'])
    ->middleware('permission:coupons.view')
    ->name('coupons.index');

Route::get('/coupons/trash', [CouponController::class, 'trash'])
    ->middleware('permission:coupons.view')
    ->name('coupons.trash');

Route::patch('/coupons/bulk-action', [CouponController::class, 'bulkAction'])
    ->middleware('permission:coupons.edit')
    ->name('coupons.bulk-action');

Route::get('/coupons/create', [CouponController::class, 'create'])
    ->middleware('permission:coupons.create')
    ->name('coupons.create');

Route::post('/coupons', [CouponController::class, 'store'])
    ->middleware('permission:coupons.create')
    ->name('coupons.store');

Route::get('/coupons/{coupon}', [CouponController::class, 'show'])
    ->middleware('permission:coupons.view')
    ->name('coupons.show');

Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])
    ->middleware('permission:coupons.edit')
    ->name('coupons.edit');

Route::put('/coupons/{coupon}', [CouponController::class, 'update'])
    ->middleware('permission:coupons.edit')
    ->name('coupons.update');

Route::patch('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])
    ->middleware('permission:coupons.edit')
    ->name('coupons.toggle-status');

Route::post('/coupons/{id}/restore', [CouponController::class, 'restore'])
    ->middleware('permission:coupons.edit')
    ->name('coupons.restore');

Route::delete('/coupons/{coupon}', [CouponController::class, 'destroy'])
    ->middleware('permission:coupons.delete')
    ->name('coupons.destroy');

Route::delete('/coupons/{id}/force-delete', [CouponController::class, 'forceDelete'])
    ->middleware('permission:coupons.delete')
    ->name('coupons.force-delete');
    });


    /*
|--------------------------------------------------------------------------
| Payments
|--------------------------------------------------------------------------
*/
Route::get('/payments', [PaymentController::class, 'index'])
    ->middleware('permission:payments.view')
    ->name('dashboard.payments.index');

Route::get('/payments/{payment}', [PaymentController::class, 'show'])
    ->middleware('permission:payments.view')
    ->name('dashboard.payments.show');


    /*
|--------------------------------------------------------------------------
| Refunds
|--------------------------------------------------------------------------
*/
Route::get('/refunds', [RefundController::class, 'index'])
    ->middleware('permission:refunds.view')
    ->name('dashboard.refunds.index');

Route::get('/refunds/{refund}', [RefundController::class, 'show'])
    ->middleware('permission:refunds.view')
    ->name('dashboard.refunds.show');
/*
|--------------------------------------------------------------------------
| Returns
|--------------------------------------------------------------------------
*/
Route::get('/returns', [ReturnController::class, 'index'])
    ->middleware('permission:returns.view')
    ->name('dashboard.returns.index');

Route::get('/returns/{returnRequest}', [ReturnController::class, 'show'])
    ->middleware('permission:returns.view')
    ->name('dashboard.returns.show');


    /*
|--------------------------------------------------------------------------
| Shipping Methods
|--------------------------------------------------------------------------
*/
Route::get('/shipping-methods', [ShippingMethodController::class, 'index'])
    ->middleware('permission:shipping-methods.view')
    ->name('dashboard.shipping-methods.index');

Route::get('/shipping-methods/create', [ShippingMethodController::class, 'create'])
    ->middleware('permission:shipping-methods.create')
    ->name('dashboard.shipping-methods.create');

Route::post('/shipping-methods', [ShippingMethodController::class, 'store'])
    ->middleware('permission:shipping-methods.create')
    ->name('dashboard.shipping-methods.store');

Route::get('/shipping-methods/{shippingMethod}/edit', [ShippingMethodController::class, 'edit'])
    ->middleware('permission:shipping-methods.edit')
    ->name('dashboard.shipping-methods.edit');

Route::put('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'update'])
    ->middleware('permission:shipping-methods.edit')
    ->name('dashboard.shipping-methods.update');

Route::patch('/shipping-methods/{shippingMethod}/toggle-status', [ShippingMethodController::class, 'toggleStatus'])
    ->middleware('permission:shipping-methods.edit')
    ->name('dashboard.shipping-methods.toggle-status');

Route::delete('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'destroy'])
    ->middleware('permission:shipping-methods.delete')
    ->name('dashboard.shipping-methods.destroy');

    /*
|--------------------------------------------------------------------------
| Shipping Zones
|--------------------------------------------------------------------------
*/
Route::get('/shipping-zones', [ShippingZoneController::class, 'index'])
    ->middleware('permission:shipping-zones.view')
    ->name('dashboard.shipping-zones.index');

Route::get('/shipping-zones/create', [ShippingZoneController::class, 'create'])
    ->middleware('permission:shipping-zones.create')
    ->name('dashboard.shipping-zones.create');

Route::post('/shipping-zones', [ShippingZoneController::class, 'store'])
    ->middleware('permission:shipping-zones.create')
    ->name('dashboard.shipping-zones.store');

Route::get('/shipping-zones/{shippingZone}/edit', [ShippingZoneController::class, 'edit'])
    ->middleware('permission:shipping-zones.edit')
    ->name('dashboard.shipping-zones.edit');

Route::put('/shipping-zones/{shippingZone}', [ShippingZoneController::class, 'update'])
    ->middleware('permission:shipping-zones.edit')
    ->name('dashboard.shipping-zones.update');

Route::patch('/shipping-zones/{shippingZone}/toggle-status', [ShippingZoneController::class, 'toggleStatus'])
    ->middleware('permission:shipping-zones.edit')
    ->name('dashboard.shipping-zones.toggle-status');

Route::delete('/shipping-zones/{shippingZone}', [ShippingZoneController::class, 'destroy'])
    ->middleware('permission:shipping-zones.delete')
    ->name('dashboard.shipping-zones.destroy');


    /*
|--------------------------------------------------------------------------
| Shipping Rates
|--------------------------------------------------------------------------
*/
Route::get('/shipping-rates', [ShippingRateController::class, 'index'])
    ->middleware('permission:shipping-rates.view')
    ->name('dashboard.shipping-rates.index');

Route::get('/shipping-rates/create', [ShippingRateController::class, 'create'])
    ->middleware('permission:shipping-rates.create')
    ->name('dashboard.shipping-rates.create');

Route::post('/shipping-rates', [ShippingRateController::class, 'store'])
    ->middleware('permission:shipping-rates.create')
    ->name('dashboard.shipping-rates.store');

Route::get('/shipping-rates/{shippingRate}/edit', [ShippingRateController::class, 'edit'])
    ->middleware('permission:shipping-rates.edit')
    ->name('dashboard.shipping-rates.edit');

Route::put('/shipping-rates/{shippingRate}', [ShippingRateController::class, 'update'])
    ->middleware('permission:shipping-rates.edit')
    ->name('dashboard.shipping-rates.update');

Route::patch('/shipping-rates/{shippingRate}/toggle-status', [ShippingRateController::class, 'toggleStatus'])
    ->middleware('permission:shipping-rates.edit')
    ->name('dashboard.shipping-rates.toggle-status');

Route::delete('/shipping-rates/{shippingRate}', [ShippingRateController::class, 'destroy'])
    ->middleware('permission:shipping-rates.delete')
    ->name('dashboard.shipping-rates.destroy');


    /*
|--------------------------------------------------------------------------
| Shipments
|--------------------------------------------------------------------------
*/
Route::get('/shipments', [ShipmentController::class, 'index'])
    ->middleware('permission:shipments.view')
    ->name('dashboard.shipments.index');

Route::get('/shipments/create', [ShipmentController::class, 'create'])
    ->middleware('permission:shipments.create')
    ->name('dashboard.shipments.create');

Route::post('/shipments', [ShipmentController::class, 'store'])
    ->middleware('permission:shipments.create')
    ->name('dashboard.shipments.store');

Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])
    ->middleware('permission:shipments.view')
    ->name('dashboard.shipments.show');

Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])
    ->middleware('permission:shipments.edit')
    ->name('dashboard.shipments.edit');

Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])
    ->middleware('permission:shipments.edit')
    ->name('dashboard.shipments.update');

Route::patch('/shipments/{shipment}/toggle-status', [ShipmentController::class, 'toggleStatus'])
    ->middleware('permission:shipments.edit')
    ->name('dashboard.shipments.toggle-status');

Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])
    ->middleware('permission:shipments.delete')
    ->name('dashboard.shipments.destroy');
