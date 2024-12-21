<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;


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

Route::get('/', function () {
    return view('welcome');
});


// テスト用
Route::get('web/helloworld', function () {
    return response()->json(['message' => 'Hello, World [web]']);
});


// 認証メールのリンクとび先
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // メール確認を完了

    // フロントエンドのプロフィール編集画面にリダイレクト
    return redirect('http://localhost:3000/mypage/profile');

})->middleware(['auth', 'signed'])->name('verification.verify');
