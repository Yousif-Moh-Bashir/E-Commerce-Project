<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Auth::routes();

# Open Access Route
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('shop', [ShopController::class, 'index'])->name('shop');
Route::get('shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');

# Cart Route
Route::get('cart', [CartController::class, 'index'])->name('cart');
Route::post('cart-add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::put('cart-increase/{rowId}', [CartController::class, 'increase_cart'])->name('cart.increase');
Route::put('cart-decrease/{rowId}', [CartController::class, 'decrease_cart'])->name('cart.decrease');
Route::delete('cart-remove/{rowId}', [CartController::class, 'remove_cart'])->name('cart.remove');
Route::delete('cart-clear', [CartController::class, 'clear_cart'])->name('cart.clear');


# Middleware For User
Route::middleware('auth')->group(function(){
    Route::get('account-dashboard',[UserController::class,'index'])->name('user.index');
});


# Middleware For Admin
Route::middleware(['auth',AuthAdmin::class])->group(function(){

    Route::get('admin',[AdminController::class,'index'])->name('admin.index');
    Route::get('admin/brands',[AdminController::class,'brands'])->name('admin.brands');
    Route::get('admin/add-brand',[AdminController::class,'add_brand'])->name('admin.add.brand');
    Route::get('admin/edit-brand/{id}',[AdminController::class,'edit_brand'])->name('admin.edit.brand');
    Route::post('admin/brands-store',[AdminController::class,'store'])->name('admin.brands.store');
    Route::put('admin/brands-update/{id}',[AdminController::class,'update'])->name('admin.brands.update');
    Route::delete('admin/brands-delete/{id}',[AdminController::class,'delete'])->name('admin.brands.delete');

    Route::get('admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    Route::get('admin/add-categories',[AdminController::class,'add_categories'])->name('admin.add.categories');
    Route::get('admin/edit-categories/{id}',[AdminController::class,'edit_categories'])->name('admin.edit.categories');
    Route::post('admin/categories-store',[AdminController::class,'store_categories'])->name('admin.categories.store');
    Route::put('admin/categories-update/{id}',[AdminController::class,'update_categories'])->name('admin.categories.update');
    Route::delete('admin/categories-delete/{id}',[AdminController::class,'delete_categories'])->name('admin.categories.delete');

    Route::get('admin/products',[AdminController::class,'products'])->name('admin.products');
    Route::get('admin/add-products',[AdminController::class,'add_products'])->name('admin.products.add');
    Route::get('admin/edit-products/{id}',[AdminController::class,'edit_products'])->name('admin.products.edit');
    Route::post('admin/products-store',[AdminController::class,'store_products'])->name('admin.products.store');
    Route::put('admin/products-update/{id}',[AdminController::class,'update_products'])->name('admin.products.update');
    Route::delete('admin/products-delete/{id}',[AdminController::class,'delete_products'])->name('admin.products.delete');

});
