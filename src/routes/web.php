<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LogoutController::class, 'destroy']);

Route::get('/', [ProductController::class, 'index'])->name('product.index');
Route::post('products/search', [ProductController::class, 'search'])->name('product.search');
Route::post('profile/search', [UserController::class, 'search'])->name('profile.search');

Route::group(['prefix' => 'product/{id}'], function() {
  Route::get('', [ProductController::class, 'detail'])->name('product.detail');
  Route::group(['middleware' => 'auth'], function() {
    Route::post('/like', [ProductController::class, 'like'])->name('product.like');
    Route::post('/unlike', [ProductController::class, 'unlike'])->name('product.unlike');
    Route::post('/comment', [ProductController::class, 'storeComment'])->name('store_comment');
    Route::get('/purchase', [ProductController::class, 'purchase'])->name('product.purchase');
    Route::post('/purchase', [ProductController::class, 'purchase'])->name('product.purchase');
  });
  Route::post('/order', [ProductController::class, 'order'])->name('product.order');
});

Route::post('/delete/{commentId}', [ProductController::class, 'commentDelete'])->name('comment.delete');

Route::group(['prefix' => 'sell'], function() {
  Route::group(['middleware' => 'auth'], function() {
    Route::get('/{id?}', [ProductController::class, 'sell'])->name('product.sell');
    Route::post('/save/{id?}', [ProductController::class, 'save'])->name('product.save');
    Route::post('/delete/{id?}', [ProductController::class, 'deleteProduct'])->name('product.delete');
  });
});

Route::group(['prefix' => 'purchase/{id}/address/'], function() {
  Route::group(['middleware' => 'auth'], function() {
    Route::get('', [UserController::class, 'address_edit'])->name('address.edit');
    Route::post('/update', [UserController::class, 'address_update'])->name('address.update');
  });
});

Route::group(['prefix' => 'mypage'], function() {
  Route::group(['middleware' => 'auth'], function() {
    Route::get('', [UserController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [UserController::class,'edit'])->name('profile.edit')->middleware(['verified']);
    Route::PATCH('/profile/update/{profileId?}', [UserController::class,'update'])->name('profile.update');
  });
});