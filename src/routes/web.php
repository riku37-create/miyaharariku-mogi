<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionChatController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LogoutController::class, 'destroy']);

Route::get('/', [ProductController::class, 'index'])->name('product.index');
Route::get('products/search', [ProductController::class, 'search'])->name('product.search');
Route::get('product/{id}', [ProductController::class, 'detail'])->name('product.detail');

Route::get('/email/verify', function () {
  return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();

  return redirect()->route('profile.edit');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();

  return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::group(['middleware' => 'auth'], function() {
  Route::group(['middleware' => 'verified'], function() {
    Route::group(['prefix' => 'product/{id}'], function() {
      Route::post('/like', [ProductController::class, 'like'])->name('product.like');
      Route::post('/unlike', [ProductController::class, 'unlike'])->name('product.unlike');
      Route::post('/comment', [ProductController::class, 'storeComment'])->name('store_comment');
      Route::post('/delete/{commentId}', [ProductController::class, 'commentDelete'])->name('comment.delete');
      Route::get('/purchase', [ProductController::class, 'purchase'])->name('product.purchase');
      Route::post('/purchase', [ProductController::class, 'purchase'])->name('product.purchase');
    });

    Route::group(['prefix' => 'sell'], function() {
      Route::get('/{id?}', [ProductController::class, 'sell'])->name('product.sell');
      Route::post('/save/{id?}', [ProductController::class, 'save'])->name('product.save');
      Route::post('/delete/{id?}', [ProductController::class, 'deleteProduct'])->name('product.delete');
    });

    Route::group(['prefix' => 'purchase/{id}/address/'], function() {
      Route::get('', [UserController::class, 'address_edit'])->name('address.edit');
      Route::post('/update', [UserController::class, 'address_update'])->name('address.update');
    });

    Route::group(['prefix' => 'mypage'], function() {
      Route::get('', [UserController::class, 'index'])->name('profile.index');
      Route::post('/profile/search', [UserController::class, 'search'])->name('profile.search');
      Route::get('/profile/edit', [UserController::class,'edit'])->name('profile.edit');
      Route::PATCH('/profile/update/{profileId?}', [UserController::class,'update'])->name('profile.update');
    });

    Route::post('/checkout/{id}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/success', [PaymentController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');

    Route::group(['prefix' => '/transaction/{transaction}'], function() {
      Route::get('/chat', [TransactionChatController::class, 'show'])->name('transactions.chat');
      Route::post('/chat', [TransactionChatController::class, 'store'])->name('transactions.chat.store');
    });

    Route::put('/chats/{chat}', [TransactionChatController::class, 'update'])->name('chats.update');
    Route::delete('/chats/{chat}', [TransactionChatController::class, 'destroy'])->name('chats.destroy');
  });
});