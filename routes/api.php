<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PageController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductReviewController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\PromocodeController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\BigSaleController;
use App\Http\Controllers\API\FeatureProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
    //     return $request->user();
    // })->middleware('auth:sanctum');
    // Register
    Route::post('/register/request-otp', [AuthController::class, 'requestRegistrationOtp']);
    Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOtp']);
    
    
    //Create Payment Intent
    Route::get('/stripe/status', [OrderController::class, 'stripeStatus'])->name('api.stripe.status');

    Route::get('/stripe/event-status', [EventController::class, 'stripeEventStatus'])->name('api.stripe.event.status');

    // Payment Success & Failed URLs    
    Route::get('/payment-success', [OrderController::class, 'success'])->name('payment.success');

    Route::get('/payment-failed', [OrderController::class, 'failed'])->name('payment.failed');
    
    Route::get('/event-payment-success', [EventController::class, 'success'])->name('event-payment.success');

    Route::get('/event-payment-failed', [EventController::class, 'failed'])->name('event-payment.failed');


    // Login
    Route::get('/login', function(){
        return response()->json(['success' => false, 'message' => 'Please login to access this API!'],401);
    })->name('login');
    Route::post('/login/request-otp', [AuthController::class, 'requestLoginOtp']);
    Route::post('/login/verify-otp', [AuthController::class, 'verifyLoginOtp']);

    // Banners
    Route::get('/banners',[BannerController::class,'index']);
    
    // Articles
    Route::get('/articles',[ArticleController::class,'index']);
    Route::get('/article/{id}',[ArticleController::class,'show']);
    Route::get('/article-by-slug/{slug}',[ArticleController::class,'showbyslug']);

    // Category
    Route::get('/category',[CategoryController::class,'index']);
    Route::get('products/category/{id}', [CategoryController::class, 'productsByCategory']);

    // Search
    Route::get('/search', [SearchController::class, 'globalSearch']);
    Route::get('/products/recommended', [SearchController::class, 'recommendedProductsForUser']);
    Route::get('/products/filter', [SearchController::class, 'filterProducts']);
    Route::get('/products/sort', [SearchController::class, 'sortProducts']);    
    
    // Porducts
    Route::get('/products', [ProductController::class, 'index']); 
    Route::get('/products/{id}', [ProductController::class, 'show']); 
    Route::get('/b2b/products', [ProductController::class, 'b2bProducts']); 
    Route::get('/b2c/products', [ProductController::class, 'b2cProducts']); 
    Route::get('/b2b/products/{id}', [ProductController::class, 'b2bshow']); 
    Route::get('/b2c/products/{id}', [ProductController::class, 'b2cshow']); 
    Route::get('/search-product', [ProductController::class, 'search']);

    // Pages
    Route::get('pages/{slug}', [PageController::class, 'showBySlug']);
    Route::get('slug', [PageController::class, 'Slug']);
    Route::get('static-pages', [PageController::class, 'getPrivacyTermsPages']);
    Route::get('getVersion', [PageController::class, 'getVersion']);

    // Big Sales
    Route::get('/big-sales', [BigSaleController::class, 'index']);

    // Featured Products
    Route::get('/featured-products', [FeatureProductController::class, 'index']);
       
    // Events
    Route::get('/events', [EventController::class, 'index']); // Get all events
    Route::get('/events/{id}', [EventController::class, 'show']); // Get single event
    Route::get('/events/qr/{registrationId}', [EventController::class, 'qr'])->name('api.event.qr');

    Route::get('/mostsellingproduct', [ProductController::class, 'mostSellingProducts']); 

    // Authenticated Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Logout
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Promo code and checkout
        Route::post('/promo-code',[CartController::class,'applyPromoCode']);
        // Route::post('/checkout',[CartController::class,'directCheckout']);
        Route::get('/promocode',[PromocodeController::class,'getPromo']);
        
        // Place Order
        Route::get('/order',[OrderController::class,'orders']);
        Route::get('/shiping',[OrderController::class,'showShiping']);
        Route::post('/placeorder',[OrderController::class,'placeOrder']);        
        
    // Cart
    Route::post('/addtocart', [CartController::class, 'addToCart']);
    Route::post('/updatecart', [CartController::class, 'updateCart']);
    Route::get('/cart', [CartController::class, 'fetchCart']);
    Route::delete('/cart/product/{productId}', [CartController::class, 'deleteCartItem']); 
    
    // Wishlist
    Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist']);
    Route::delete('/wishlist/product/{productId}', [WishlistController::class, 'removeFromWishlist']);
    Route::get('/wishlist', [WishlistController::class, 'fetchWishlist']);
        
        // Address
        Route::get('/addaddress',[AddressController::class,'getAddresses']);
        Route::post('/addaddress',[AddressController::class,'addAddress']);
        Route::put('/updateaddress/{addressId}',[AddressController::class,'updateAddress']);
        Route::delete('/deleteaddresses/{id}', [AddressController::class, 'deleteAddress']);
        
        // Events
        Route::post('/events/{id}/register', [EventController::class, 'register']); // Register
        Route::post('/events/{id}/cancel', [EventController::class, 'cancel']); // Cancel
        Route::get('/reigsteredevents', [EventController::class, 'registeredEvents']); 
         Route::post('/nearbyevents', [EventController::class, 'nearbyEvents']); 
        
        // Profile update
        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::post('/update/profile', [ProfileController::class, 'completeProfile']); // Get single event
        
        // Notification
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        
        // Product Review
        Route::get('products/{productId}/reviews', [ProductReviewController::class, 'index']);
        Route::post('products/{productId}/review', [ProductReviewController::class, 'store']);

        // Delete Account
        Route::delete('/account/delete', [AuthController::class, 'destroy']);
    });
    Route::post('/stripe/webhook', [OrderController::class, 'stripeWebhook']);