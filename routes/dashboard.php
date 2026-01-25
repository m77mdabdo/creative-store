<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\CategoryController as DashboardCategoryController;
use App\Http\Controllers\Dashboard\ContactMessageController as DashboardContactMessageController;
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
use App\Http\Controllers\Dashboard\ShipmentTrackingController;
use App\Http\Controllers\Dashboard\DeliveryMonitoringController;
use App\Http\Controllers\Dashboard\AddressController;
use App\Http\Controllers\Dashboard\WishlistController;
use App\Http\Controllers\Dashboard\AbandonedCartRecoveryController;
use App\Http\Controllers\Dashboard\BlogCategoryController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\BlogCommentController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\AuditLogController;
use App\Http\Controllers\Dashboard\ErrorLogController;
use App\Http\Controllers\Dashboard\BackupController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\ShipmentCarrierController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;






Route::group([
    'prefix' => LaravelLocalization::setLocale() . '/dashboard',
    'as' => 'dashboard.',
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
        'auth',
        'role:owner,admin,employee'
    ],
], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('home');

    // The signed-in user's own notifications (no extra permission needed)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])
        ->middleware('throttle:120,1')
        ->name('notifications.feed');
    Route::get('/notifications/{id}/open', [NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */
        Route::get('/users/trash', [UserController::class, 'trash'])
            ->middleware('permission:users.view')
            ->name('users.trash');

        Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])
            ->middleware('permission:users.edit')
            ->name('users.bulk-action');

        Route::patch('/users/{user}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users.edit')
            ->name('users.restore');

        Route::delete('/users/{user}/force-delete', [UserController::class, 'forceDelete'])
            ->middleware('permission:users.delete')
            ->name('users.force-delete');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->middleware('permission:users.create')
            ->name('users.store');

        Route::post('/users/{user}/invitation', [UserController::class, 'resendInvitation'])
            ->middleware(['permission:users.edit', 'throttle:6,1'])
            ->name('users.invitation');

        Route::get('/users/{user}', [UserController::class, 'show'])
            ->middleware('permission:users.view')
            ->name('users.show');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit')
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.edit')
            ->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users.delete')
            ->name('users.destroy');

        /*
        |--------------------------------------------------------------------------
        | Audit Logs
        |--------------------------------------------------------------------------
        */
        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->middleware('permission:audit.view')
            ->name('audit.index');

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */
        Route::get('/payments', [PaymentController::class, 'index'])
            ->middleware('permission:payments.view')
            ->name('payments.index');

        Route::get('/payments/{payment}', [PaymentController::class, 'show'])
            ->middleware('permission:payments.view')
            ->name('payments.show');

        /*
        |--------------------------------------------------------------------------
        | Refunds
        |--------------------------------------------------------------------------
        */
        Route::get('/refunds', [RefundController::class, 'index'])
            ->middleware('permission:refunds.view')
            ->name('refunds.index');

        Route::get('/refunds/{refund}', [RefundController::class, 'show'])
            ->middleware('permission:refunds.view')
            ->name('refunds.show');

        /*
        |--------------------------------------------------------------------------
        | Returns
        |--------------------------------------------------------------------------
        */
        Route::get('/returns', [ReturnController::class, 'index'])
            ->middleware('permission:returns.view')
            ->name('returns.index');

        Route::get('/returns/{returnRequest}', [ReturnController::class, 'show'])
            ->middleware('permission:returns.view')
            ->name('returns.show');

        /*
        |--------------------------------------------------------------------------
        | Shipping Methods
        |--------------------------------------------------------------------------
        */
        Route::get('/shipping-methods', [ShippingMethodController::class, 'index'])
            ->middleware('permission:shipping-methods.view')
            ->name('shipping-methods.index');

        Route::get('/shipping-methods/create', [ShippingMethodController::class, 'create'])
            ->middleware('permission:shipping-methods.create')
            ->name('shipping-methods.create');

        Route::post('/shipping-methods', [ShippingMethodController::class, 'store'])
            ->middleware('permission:shipping-methods.create')
            ->name('shipping-methods.store');

        Route::get('/shipping-methods/{shippingMethod}/edit', [ShippingMethodController::class, 'edit'])
            ->middleware('permission:shipping-methods.edit')
            ->name('shipping-methods.edit');

        Route::put('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'update'])
            ->middleware('permission:shipping-methods.edit')
            ->name('shipping-methods.update');

        Route::patch('/shipping-methods/{shippingMethod}/toggle-status', [ShippingMethodController::class, 'toggleStatus'])
            ->middleware('permission:shipping-methods.edit')
            ->name('shipping-methods.toggle-status');

        Route::delete('/shipping-methods/{shippingMethod}', [ShippingMethodController::class, 'destroy'])
            ->middleware('permission:shipping-methods.delete')
            ->name('shipping-methods.destroy');

        /*
        |--------------------------------------------------------------------------
        | Shipping Zones
        |--------------------------------------------------------------------------
        */
        Route::get('/shipping-zones', [ShippingZoneController::class, 'index'])
            ->middleware('permission:shipping-zones.view')
            ->name('shipping-zones.index');

        Route::get('/shipping-zones/create', [ShippingZoneController::class, 'create'])
            ->middleware('permission:shipping-zones.create')
            ->name('shipping-zones.create');

        Route::post('/shipping-zones', [ShippingZoneController::class, 'store'])
            ->middleware('permission:shipping-zones.create')
            ->name('shipping-zones.store');

        Route::get('/shipping-zones/{shippingZone}/edit', [ShippingZoneController::class, 'edit'])
            ->middleware('permission:shipping-zones.edit')
            ->name('shipping-zones.edit');

        Route::put('/shipping-zones/{shippingZone}', [ShippingZoneController::class, 'update'])
            ->middleware('permission:shipping-zones.edit')
            ->name('shipping-zones.update');

        Route::patch('/shipping-zones/{shippingZone}/toggle-status', [ShippingZoneController::class, 'toggleStatus'])
            ->middleware('permission:shipping-zones.edit')
            ->name('shipping-zones.toggle-status');

        Route::delete('/shipping-zones/{shippingZone}', [ShippingZoneController::class, 'destroy'])
            ->middleware('permission:shipping-zones.delete')
            ->name('shipping-zones.destroy');

        /*
        |--------------------------------------------------------------------------
        | Shipping Rates
        |--------------------------------------------------------------------------
        */
        Route::get('/shipping-rates', [ShippingRateController::class, 'index'])
            ->middleware('permission:shipping-rates.view')
            ->name('shipping-rates.index');

        Route::get('/shipping-rates/create', [ShippingRateController::class, 'create'])
            ->middleware('permission:shipping-rates.create')
            ->name('shipping-rates.create');

        Route::post('/shipping-rates', [ShippingRateController::class, 'store'])
            ->middleware('permission:shipping-rates.create')
            ->name('shipping-rates.store');

        Route::get('/shipping-rates/{shippingRate}/edit', [ShippingRateController::class, 'edit'])
            ->middleware('permission:shipping-rates.edit')
            ->name('shipping-rates.edit');

        Route::put('/shipping-rates/{shippingRate}', [ShippingRateController::class, 'update'])
            ->middleware('permission:shipping-rates.edit')
            ->name('shipping-rates.update');

        Route::patch('/shipping-rates/{shippingRate}/toggle-status', [ShippingRateController::class, 'toggleStatus'])
            ->middleware('permission:shipping-rates.edit')
            ->name('shipping-rates.toggle-status');

        Route::delete('/shipping-rates/{shippingRate}', [ShippingRateController::class, 'destroy'])
            ->middleware('permission:shipping-rates.delete')
            ->name('shipping-rates.destroy');

        /*
        |--------------------------------------------------------------------------
        | Shipments
        |--------------------------------------------------------------------------
        */
        Route::get('/shipments', [ShipmentController::class, 'index'])
            ->middleware('permission:shipments.view')
            ->name('shipments.index');

        Route::get('/shipments/create', [ShipmentController::class, 'create'])
            ->middleware('permission:shipments.create')
            ->name('shipments.create');

        Route::post('/shipments', [ShipmentController::class, 'store'])
            ->middleware('permission:shipments.create')
            ->name('shipments.store');

        Route::post('/shipments/{shipment}/waybill', [ShipmentCarrierController::class, 'book'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.waybill');

        Route::post('/shipments/{shipment}/sync', [ShipmentCarrierController::class, 'sync'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.sync');

        Route::get('/shipments/{shipment}/label', [ShipmentCarrierController::class, 'label'])
            ->middleware('permission:shipments.view')
            ->name('shipments.label');

        Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])
            ->middleware('permission:shipments.view')
            ->name('shipments.show');

        Route::get('/shipments/{shipment}/edit', [ShipmentController::class, 'edit'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.edit');

        Route::put('/shipments/{shipment}', [ShipmentController::class, 'update'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.update');

        Route::patch('/shipments/{shipment}/toggle-status', [ShipmentController::class, 'toggleStatus'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.toggle-status');

        Route::delete('/shipments/{shipment}', [ShipmentController::class, 'destroy'])
            ->middleware('permission:shipments.delete')
            ->name('shipments.destroy');

        /*
        |--------------------------------------------------------------------------
        | Shipment Tracking
        |--------------------------------------------------------------------------
        */
        Route::post('/shipments/{shipment}/tracking', [ShipmentTrackingController::class, 'store'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.tracking.store');

        /*
        |--------------------------------------------------------------------------
        | Delivery Monitoring
        |--------------------------------------------------------------------------
        */
        Route::post('/shipments/{shipment}/delivery-attempts', [DeliveryMonitoringController::class, 'storeAttempt'])
            ->middleware('permission:shipments.edit')
            ->name('shipments.delivery-attempts.store');


        /*
|--------------------------------------------------------------------------
| Addresses
|--------------------------------------------------------------------------
*/
        Route::get('/addresses', [AddressController::class, 'index'])
            ->middleware('permission:addresses.view')
            ->name('addresses.index');

        Route::get('/addresses/{address}', [AddressController::class, 'show'])
            ->middleware('permission:addresses.view')
            ->name('addresses.show');

        Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])
            ->middleware('permission:addresses.edit')
            ->name('addresses.edit');

        Route::put('/addresses/{address}', [AddressController::class, 'update'])
            ->middleware('permission:addresses.edit')
            ->name('addresses.update');

        Route::patch('/addresses/{address}/toggle-default', [AddressController::class, 'toggleDefault'])
            ->middleware('permission:addresses.edit')
            ->name('addresses.toggle-default');

        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])
            ->middleware('permission:addresses.delete')
            ->name('addresses.destroy');

        /*
|--------------------------------------------------------------------------
| Wishlists
|--------------------------------------------------------------------------
*/
        Route::get('/wishlists', [WishlistController::class, 'index'])
            ->middleware('permission:wishlists.view')
            ->name('wishlists.index');

        Route::get('/wishlists/{wishlist}', [WishlistController::class, 'show'])
            ->middleware('permission:wishlists.view')
            ->name('wishlists.show');

        Route::delete('/wishlists/{wishlist}', [WishlistController::class, 'destroy'])
            ->middleware('permission:wishlists.delete')
            ->name('wishlists.destroy');

        Route::delete('/wishlists/{wishlist}/items/{item}', [WishlistController::class, 'destroyItem'])
            ->middleware('permission:wishlists.edit')
            ->name('wishlists.items.destroy');
        Route::post('/wishlists/{wishlist}/move-all-to-cart', [WishlistController::class, 'moveAllToCart'])
            ->middleware('permission:wishlists.edit')
            ->name('wishlists.move-all-to-cart');

        Route::post('/wishlists/{wishlist}/items/{item}/move-to-cart', [WishlistController::class, 'moveItemToCart'])
            ->middleware('permission:wishlists.edit')
            ->name('wishlists.items.move-to-cart');


        /*
|--------------------------------------------------------------------------
| Carts
|--------------------------------------------------------------------------
*/
        Route::get('/carts', [\App\Http\Controllers\Dashboard\CartController::class, 'index'])
            ->middleware('permission:carts.view')
            ->name('carts.index');

        Route::get('/carts/abandoned', [\App\Http\Controllers\Dashboard\CartController::class, 'abandoned'])
            ->middleware('permission:carts.view')
            ->name('carts.abandoned');

        Route::get('/carts/{cart}', [\App\Http\Controllers\Dashboard\CartController::class, 'show'])
            ->middleware('permission:carts.view')
            ->name('carts.show');

        Route::delete('/carts/{cart}', [\App\Http\Controllers\Dashboard\CartController::class, 'destroy'])
            ->middleware('permission:carts.delete')
            ->name('carts.destroy');

        Route::delete('/carts/{cart}/items/{item}', [\App\Http\Controllers\Dashboard\CartController::class, 'destroyItem'])
            ->middleware('permission:carts.edit')
            ->name('carts.items.destroy');

        /*
|--------------------------------------------------------------------------
| Abandoned Cart Recoveries
|--------------------------------------------------------------------------
*/
        Route::get('/abandoned-cart-recoveries', [AbandonedCartRecoveryController::class, 'index'])
            ->middleware('permission:abandoned-cart-recoveries.view')
            ->name('abandoned-cart-recoveries.index');

        Route::get('/abandoned-cart-recoveries/{recovery}', [AbandonedCartRecoveryController::class, 'show'])
            ->middleware('permission:abandoned-cart-recoveries.view')
            ->name('abandoned-cart-recoveries.show');

        /*
|--------------------------------------------------------------------------
| Blog Categories
|--------------------------------------------------------------------------
*/
        Route::get('/blog-categories', [BlogCategoryController::class, 'index'])
            ->middleware('permission:blog-categories.view')
            ->name('blog-categories.index');

        Route::get('/blog-categories/create', [BlogCategoryController::class, 'create'])
            ->middleware('permission:blog-categories.create')
            ->name('blog-categories.create');

        Route::post('/blog-categories', [BlogCategoryController::class, 'store'])
            ->middleware('permission:blog-categories.create')
            ->name('blog-categories.store');

        Route::get('/blog-categories/{blog_category}/edit', [BlogCategoryController::class, 'edit'])
            ->middleware('permission:blog-categories.edit')
            ->name('blog-categories.edit');

        Route::put('/blog-categories/{blog_category}', [BlogCategoryController::class, 'update'])
            ->middleware('permission:blog-categories.edit')
            ->name('blog-categories.update');

        Route::delete('/blog-categories/{blog_category}', [BlogCategoryController::class, 'destroy'])
            ->middleware('permission:blog-categories.delete')
            ->name('blog-categories.destroy');

        /*
|--------------------------------------------------------------------------
| Blogs
|--------------------------------------------------------------------------
*/
        Route::get('/blogs', [BlogController::class, 'index'])
            ->middleware('permission:blogs.view')
            ->name('blogs.index');

        Route::get('/blogs/create', [BlogController::class, 'create'])
            ->middleware('permission:blogs.create')
            ->name('blogs.create');

        Route::post('/blogs', [BlogController::class, 'store'])
            ->middleware('permission:blogs.create')
            ->name('blogs.store');

        Route::get('/blogs/{blog}', [BlogController::class, 'show'])
            ->middleware('permission:blogs.view')
            ->name('blogs.show');

        Route::get('/blogs/{blog}/edit', [BlogController::class, 'edit'])
            ->middleware('permission:blogs.edit')
            ->name('blogs.edit');

        Route::put('/blogs/{blog}', [BlogController::class, 'update'])
            ->middleware('permission:blogs.edit')
            ->name('blogs.update');

        Route::patch('/blogs/{blog}/toggle-status', [BlogController::class, 'toggleStatus'])
            ->middleware('permission:blogs.edit')
            ->name('blogs.toggle-status');

        Route::patch('/blogs/{blog}/toggle-featured', [BlogController::class, 'toggleFeatured'])
            ->middleware('permission:blogs.edit')
            ->name('blogs.toggle-featured');

        Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])
            ->middleware('permission:blogs.delete')
            ->name('blogs.destroy');

        /*
|--------------------------------------------------------------------------
| Blog Comments
|--------------------------------------------------------------------------
*/
        Route::get('/blog-comments', [BlogCommentController::class, 'index'])
            ->middleware('permission:blog-comments.view')
            ->name('blog-comments.index');

        Route::get('/blog-comments/{blogComment}', [BlogCommentController::class, 'show'])
            ->middleware('permission:blog-comments.view')
            ->name('blog-comments.show');

        Route::delete('/blog-comments/{blogComment}', [BlogCommentController::class, 'destroy'])
            ->middleware('permission:blog-comments.delete')
            ->name('blog-comments.destroy');

        /*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/
        Route::get('/pages', [PageController::class, 'index'])
            ->middleware('permission:pages.view')
            ->name('pages.index');

        Route::get('/pages/create', [PageController::class, 'create'])
            ->middleware('permission:pages.create')
            ->name('pages.create');

        Route::post('/pages', [PageController::class, 'store'])
            ->middleware('permission:pages.create')
            ->name('pages.store');

        Route::get('/pages/{page}', [PageController::class, 'show'])
            ->middleware('permission:pages.view')
            ->name('pages.show');

        Route::get('/pages/{page}/edit', [PageController::class, 'edit'])
            ->middleware('permission:pages.edit')
            ->name('pages.edit');

        Route::put('/pages/{page}', [PageController::class, 'update'])
            ->middleware('permission:pages.edit')
            ->name('pages.update');

        Route::patch('/pages/{page}/toggle-status', [PageController::class, 'toggleStatus'])
            ->middleware('permission:pages.edit')
            ->name('pages.toggle-status');

        Route::delete('/pages/{page}', [PageController::class, 'destroy'])
            ->middleware('permission:pages.delete')
            ->name('pages.destroy');

        /*
|--------------------------------------------------------------------------
| Contact Messages
|--------------------------------------------------------------------------
*/
        Route::get('/contact-messages', [DashboardContactMessageController::class, 'index'])
            ->middleware('permission:contact.view')
            ->name('contact-messages.index');

        Route::post('/contact-messages/bulk-action', [DashboardContactMessageController::class, 'bulkAction'])
            ->middleware('permission:contact.edit')
            ->name('contact-messages.bulk-action');

        Route::get('/contact-messages/{message}', [DashboardContactMessageController::class, 'show'])
            ->middleware('permission:contact.view')
            ->name('contact-messages.show');

        Route::patch('/contact-messages/{message}/mark-read', [DashboardContactMessageController::class, 'markAsRead'])
            ->middleware('permission:contact.edit')
            ->name('contact-messages.mark-read');

        Route::patch('/contact-messages/{message}/mark-unread', [DashboardContactMessageController::class, 'markAsUnread'])
            ->middleware('permission:contact.edit')
            ->name('contact-messages.mark-unread');

        Route::delete('/contact-messages/{message}', [DashboardContactMessageController::class, 'destroy'])
            ->middleware('permission:contact.delete')
            ->name('contact-messages.destroy');


        /*
|--------------------------------------------------------------------------
| Roles
|--------------------------------------------------------------------------
*/
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('permission:roles.view')
            ->name('roles.index');

        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create')
            ->name('roles.create');

        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('permission:roles.create')
            ->name('roles.store');

        Route::get('/roles/{role}', [RoleController::class, 'show'])
            ->middleware('permission:roles.view')
            ->name('roles.show');

        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.edit')
            ->name('roles.edit');

        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.edit')
            ->name('roles.update');

        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.delete')
            ->name('roles.destroy');

        /*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
*/
        Route::get('/permissions', [PermissionController::class, 'index'])
            ->middleware('permission:permissions.view')
            ->name('permissions.index');

        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
            ->middleware('permission:permissions.view')
            ->name('permissions.show');



        Route::get('/audit', [AuditLogController::class, 'index'])
            ->middleware('permission:audit.view')
            ->name('audit.index');

        Route::get('/audit/export', [AuditLogController::class, 'export'])
            ->middleware('permission:audit.view')
            ->name('audit.export');

        Route::get('/audit/print', [AuditLogController::class, 'print'])
            ->middleware('permission:audit.view')
            ->name('audit.print');

        Route::get('/audit/{auditLog}', [AuditLogController::class, 'show'])
            ->whereNumber('auditLog')
            ->middleware('permission:audit.view')
            ->name('audit.show');




        Route::get('/error-logs', [ErrorLogController::class, 'index'])
            ->middleware('permission:error-logs.view')
            ->name('error-logs.index');

        Route::get('/error-logs/{errorLog}', [ErrorLogController::class, 'show'])
            ->middleware('permission:error-logs.view')
            ->name('error-logs.show');

        Route::patch('/error-logs/{errorLog}/resolve', [ErrorLogController::class, 'resolve'])
            ->middleware('permission:error-logs.edit')
            ->name('error-logs.resolve');

        Route::delete('/error-logs/clear-resolved', [ErrorLogController::class, 'clearResolved'])
            ->middleware('permission:error-logs.edit')
            ->name('error-logs.clear-resolved');

        Route::patch('/error-logs/bulk-resolve', [ErrorLogController::class, 'bulkResolve'])
            ->middleware('permission:error-logs.edit')
            ->name('error-logs.bulk-resolve');

        Route::delete('/error-logs/bulk-delete', [ErrorLogController::class, 'bulkDelete'])
            ->middleware('permission:error-logs.edit')
            ->name('error-logs.bulk-delete');


        //backups




        Route::get('/backups', [BackupController::class, 'index'])
            ->middleware('permission:backups.view')
            ->name('backups.index');

        Route::post('/backups/create', [BackupController::class, 'create'])
            ->middleware('permission:backups.create')
            ->name('backups.create');

        Route::delete('/backups/bulk-delete', [BackupController::class, 'bulkDelete'])
            ->middleware('permission:backups.delete')
            ->name('backups.bulk-delete');

        Route::get('/backups/{systemBackup}/download', [BackupController::class, 'download'])
            ->whereNumber('systemBackup')
            ->middleware('permission:backups.view')
            ->name('backups.download');

        Route::get('/backups/{systemBackup}', [BackupController::class, 'show'])
            ->whereNumber('systemBackup')
            ->middleware('permission:backups.view')
            ->name('backups.show');

        Route::delete('/backups/{systemBackup}', [BackupController::class, 'destroy'])
            ->whereNumber('systemBackup')
            ->middleware('permission:backups.delete')
            ->name('backups.destroy');
    });
