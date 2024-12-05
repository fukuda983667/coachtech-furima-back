<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemConditionController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PurchaseController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// テスト用
Route::get('/helloworld', function () {
    return response()->json(['message' => 'Hello, World']);
});


// ユーザー用の登録ルート
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// 全アイテム取得
Route::get('/items', [ItemController::class, 'getItems'])->name('getItems');


// 特定アイテムの詳細取得
Route::get('/items/{id}', [ItemController::class, 'getItem'])->name('getItem');
// 特定アイテムのお気に入り状況と件数取得
Route::get('/likes/{id}', [LikeController::class, 'getLike'])->name('getlLike');
// 特定アイテムに寄せられたコメント取得
Route::get('/comments/{id}', [CommentController::class, 'getComments'])->name('getComments');


// ログイン中かつメール認証済みなら叩ける
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {

    // フロントのnuxt3でnuxt-auth-sanctumモジュールのメソッド使用すると勝手に/userを叩く
    Route::get('/user', [UserController::class, 'getUser'])->name('getUser');


    // プロファイル更新 (ユーザ名、住所、アイコン画像)
    Route::post('/user/profile', [UserController::class, 'storeProfile'])->name('storeProfile');
    // マイページ表示用の購入商品と出品商品取得
    Route::get('/user/my-page', [UserController::class, 'getMyPageItems'])->name('getMyPageItems');


    // カテゴリー選択肢提供
    Route::get('/categories', [CategoryController::class, 'getCategories'])->name('getCategories');
    // コンディション選択肢提供
    Route::get('/conditions', [ItemConditionController::class, 'getConditions'])->name('getConditions');
    // 商品作成処理
    Route::post('/items', [ItemController::class, 'storeItem'])->name('storeItem');


    // お気に入り登録と解除
    Route::post('/likes', [LikeController::class, 'toggleLike'])->name('toggleLike');
    // コメント投稿
    Route::post('/comments', [CommentController::class, 'storeComment'])->name('storeComment');


    // 支払い方法選択肢提供
    Route::get('/payment-methods', [PaymentMethodController::class, 'getPaymentMethods'])->name('getPaymentMethods');
    // 購入処理
    Route::post('/purchases', [PurchaseController::class, 'storePurchase'])->name('storePurchase');
});

