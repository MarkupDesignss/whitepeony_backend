<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\Categorycontroller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\BigSaleController;
use App\Http\Controllers\Admin\FeatureProductController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

Route::get('/run-clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    // Artisan::call('storage:link');

    return 'Config, Cache, Route, View cleared & Storage link created successfully!';
});


 Route::get('/article/{slug}', [ArticleController::class, 'details']);
 

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 
], function () {
    Route::get('login', [AdminController::class, 'index'])->name('login');
    Route::post('login', [AdminController::class, 'login'])->name('login.post');
    
    Route::get('forgot-password', function(){
        return view('admin.auth.forgot-password');
    })->name('forgot-password');
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
    
    Route::group(['middleware' => AuthAdmin::class], function()
    {
        Route::get('logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/update-profile', [AdminController::class, 'updateProfile'])->name('updateProfile');
        Route::post('/change-password', [AdminController::class, 'changePassword'])->name('changePassword');

        // 2fa
        Route::post('/toggle-2fa/{user}', [AdminController::class, 'toggle2FA'])->name('toggle2fa');
        Route::get('2fa', [AdminController::class, 'show2FAForm'])->name('2fa');
        Route::post('2fa', [AdminController::class, 'verify2FA'])->name('2fa.verify');
        Route::post('2fa/resend', [AdminController::class, 'resend2FA'])->name('2fa.resend');
        
        Route::post('/products/reorder', [ProductController::class, 'reorder'])
    ->name('products.reorder');

        // B2B products
        Route::get('/product/b2b', [ProductController::class, 'b2bIndex'])->name('products.b2b.index');
        Route::get('/product/b2b/create', [ProductController::class, 'b2bCreate'])->name('products.b2b.create');
        Route::post('/product/b2b', [ProductController::class, 'b2bStore'])->name('products.b2b.store');

        // B2C products
        Route::get('/product/b2c', [ProductController::class, 'b2cIndex'])->name('products.b2c.index');
        Route::get('/product/b2c/create', [ProductController::class, 'b2cCreate'])->name('products.b2c.create');
        Route::post('/product/b2c', [ProductController::class, 'b2cStore'])->name('products.b2c.store');
         Route::get('products/bulk-upload', [ProductController::class, 'bulkUploadForm'])->name('products.bulk.upload');
        Route::post('products/bulk-upload', [ProductController::class, 'bulkUploadStore'])->name('products.bulk.upload.store');
        
                // Pages
        Route::resource('pages', AdminPageController::class)->names([
            'index' => 'pages.index',
            'create' => 'pages.create',
            'store' => 'pages.store',
            'show' => 'pages.show',
            'edz' => 'pages.edit',
            'update' => 'pages.update',
            'destroy' => 'pages.destroy',
        ]);
        
        // Products
        Route::resource('products', ProductController::class);


        // Orders
        Route::resource('orders', OrderController::class);

        // Events
        Route::resource('events', EventController::class);
        Route::get('/registeredevents',[EventController::class,'registeredEvents'])->name('events.registeredevents');

        
        // Category
        Route::resource('categories',Categorycontroller::class);

        // Banner
        Route::resource('banners', BannerController::class);
        Route::get('/get-banner-products/{type}', [BannerController::class,'getProducts']);


        // Promo codes
        Route::resource('promocodes', PromoCodeController::class);

        // Customer
        Route::get('customers/b2c', [CustomerController::class, 'b2cindex'])->name('customers.b2c');
        Route::get('customers/b2b', [CustomerController::class, 'b2bindex'])->name('customers.b2b');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers/store', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/update/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::get('customers/{id}', [CustomerController::class, 'view'])->name('customers.view');
        Route::post('customers/updatetier', [CustomerController::class, 'updateTier'])->name('customers.updatetier');
        Route::post('/customers/update-vat',[CustomerController::class,'updateB2BVat'])
            ->name('customers.updateVat');
        
        // Analytics and Report
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        // Articles
        Route::get('/article', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/article/{id}', [ArticleController::class, 'show'])->name('articles.show');
        Route::get('/articles', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles/store', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{id}', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
       
        
        Route::get('/bestsellingproduct', [ProductController::class, 'mostSellingProducts'])->name('bestsellingproduct');

        // Big Sales
        Route::get('big-sales', [BigSaleController::class, 'index'])->name('big_sales.index');
        Route::get('big-sales/create', [BigSaleController::class, 'create'])->name('big_sales.create');
        Route::post('big-sales/store', [BigSaleController::class, 'store'])->name('big_sales.store');
        // Route::get('big-sales/edit/{id}', [BigSaleController::class, 'edit'])->name('big_sales.edit');
        // Route::post('big-sales/update/{id}', [BigSaleController::class, 'update'])->name('big_sales.update');
        Route::delete('big-sales/delete/{id}', [BigSaleController::class, 'destroy'])->name('big_sales.delete');
        // ajax – product list by type
        Route::get('get-products/{type}', [BigSaleController::class, 'getProducts'])->name('big_sales.get_products');
        Route::get('big-sales/get-products/{type}', [BigSaleController::class, 'getProducts']);
        //End Big Sales

        // Feature Products
        Route::get('feature-products', [FeatureProductController::class,'index'])->name('feature_products.index');
        Route::get('feature-products/create', [FeatureProductController::class,'create'])->name('feature_products.create');
        Route::post('feature-products/store', [FeatureProductController::class,'store'])->name('feature_products.store');
        Route::get('feature-products/edit/{id}', [FeatureProductController::class,'edit'])->name('feature_products.edit');
        Route::post('feature-products/update/{id}', [FeatureProductController::class,'update'])->name('feature_products.update');
        Route::get('feature-products/delete/{id}', [FeatureProductController::class,'destroy'])->name('feature_products.delete');
        Route::get('get-feature-products/{type}', [FeatureProductController::class,'getProducts']);
        // End Feature Products

    });
});

Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');




