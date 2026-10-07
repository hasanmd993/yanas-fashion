<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CourierController as AdminCourierController;
use App\Http\Controllers\Admin\SmsController as AdminSmsController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Yanas Fashion E-Commerce)
|--------------------------------------------------------------------------
*/

// Authentication Routes (Vue 3 / Inertia)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Public Storefront Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{id}/review', [ProductController::class, 'storeReview'])->name('product.review.store')->middleware('throttle:5,1');

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
Route::post('/cart/sync', [CartController::class, 'sync'])->name('cart.sync');

// 1-Page Bangladeshi Fast Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon')->middleware('throttle:15,1');
Route::get('/order-confirmed/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/order/{order_number}/invoice', [CheckoutController::class, 'downloadInvoice'])->name('order.invoice');

// Order Tracking
Route::get('/order-tracking', [OrderTrackingController::class, 'index'])->name('tracking.index');
Route::post('/order-tracking', [OrderTrackingController::class, 'track'])->name('tracking.track')->middleware('throttle:10,1');

// Protected Admin Panel Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export/excel', [AdminOrderController::class, 'exportExcel'])->name('orders.export_excel');
    Route::get('/orders/export/pdf', [AdminOrderController::class, 'exportPdf'])->name('orders.export_pdf');
    Route::get('/orders/export/courier', [AdminOrderController::class, 'exportCourierCsv'])->name('orders.export_courier');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/orders/{id}/download-invoice', [AdminOrderController::class, 'downloadInvoice'])->name('orders.download_invoice');
    Route::get('/orders/{id}/stream', [AdminOrderController::class, 'streamInvoice'])->name('orders.stream');
    Route::get('/orders/{id}/print', [AdminOrderController::class, 'printInvoice'])->name('orders.print');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status');
    Route::post('/orders/{id}/dispatch-courier', [AdminCourierController::class, 'dispatchOrder'])->name('orders.dispatch_courier');
    Route::get('/orders/{id}/track-courier', [AdminCourierController::class, 'trackOrder'])->name('orders.track_courier');
    Route::post('/orders/{id}/manual-courier', [AdminCourierController::class, 'manualCourier'])->name('orders.manual_courier');
    Route::get('/courier/balance', [AdminCourierController::class, 'getBalance'])->name('courier.balance');
    Route::post('/orders/{id}/send-sms', [AdminSmsController::class, 'sendOrderSms'])->name('orders.send_sms');
    Route::post('/sms/test', [AdminSmsController::class, 'testSms'])->name('sms.test');
    Route::get('/sms/balance', [AdminSmsController::class, 'getBalance'])->name('sms.balance');
    Route::get('/sms/logs', [AdminSmsController::class, 'getLogs'])->name('sms.logs');
    Route::delete('/orders/{id}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // Products, Categories, Sliders & Coupons CRUD
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('sliders', AdminSliderController::class);
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // System Backups
    Route::get('/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/create-db', [\App\Http\Controllers\Admin\BackupController::class, 'createDb'])->name('backups.create_db');
    Route::post('/backups/create-full', [\App\Http\Controllers\Admin\BackupController::class, 'createFull'])->name('backups.create_full');
    Route::get('/backups/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
    Route::delete('/backups/destroy', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');

    // Notifications
    Route::post('/notifications/clear', function () {
        session(['notifications_cleared_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Notifications cleared']);
    })->name('notifications.clear');
});

