<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function(){
    Route::get('account-dashboard',[UserController::class,'index'])->name('user.index');
});

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

});
