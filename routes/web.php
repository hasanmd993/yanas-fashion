<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Yana's Fashion E-Commerce)
|--------------------------------------------------------------------------
*/

// Storefront Home & Catalog
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// Live AJAX Search & Quick-view
Route::get('/api/search-products', [ShopController::class, 'searchApi'])->name('api.search');
Route::get('/api/product/{id}', [ProductController::class, 'quickView'])->name('api.quickview');

// Shopping Cart (Session & AJAX Drawer)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'getCart'])->name('cart.data');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// 1-Page Bangladeshi Fast Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
Route::get('/order-confirmed/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/order/{order_number}/invoice', [CheckoutController::class, 'downloadInvoice'])->name('order.invoice');

// Order Tracking
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('tracking.index');
Route::post('/order-tracking', [OrderTrackingController::class, 'track'])->name('tracking.track');

// Protected Admin Panel Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export/excel', [AdminOrderController::class, 'exportExcel'])->name('orders.export_excel');
    Route::get('/orders/export/courier', [AdminOrderController::class, 'exportCourierCsv'])->name('orders.export_courier');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/orders/{id}/print', [AdminOrderController::class, 'printInvoice'])->name('orders.print');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status');
    Route::delete('/orders/{id}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // Products, Categories, Sliders & Coupons CRUD
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('sliders', AdminSliderController::class);
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
