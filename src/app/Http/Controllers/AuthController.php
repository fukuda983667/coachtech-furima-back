<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class AuthController extends Controller
{
    // 一般ユーザー登録機能
    public function register(RegisterRequest $request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image_path' => 'default-user.jpeg'
        ]);

        // ユーザーをログインさせる
        // 認証メールのリンクを踏むときにログイン状態にしておく必要がある。
        Auth::login($user);

        // メール認証リンクの送信
        event(new Registered($user));

        return response()->json(['message' => 'ユーザー登録成功'],201);
    }


    //ユーザーログイン機能
    public function login(Request $request) {
        // リクエストに含まれるデータを検証
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        // 該当のカラムがユーザーテーブルに存在していた場合
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            // ログイン成功
            return response()->json([
                'message' => 'ログイン成功',
                'user' => $user  // ユーザー情報を返す
            ], 200);
        }

        // 認証失敗
        return response()->json(['message' => 'ログイン失敗'], 401);
    }


    //ログアウト機能
    public function logout(Request $request) {
        Auth::logout(); // ユーザーのログアウト
        $request->session()->invalidate(); // セッションの無効化
        $request->session()->regenerateToken(); // CSRFトークンの再生成

        return response()->json(['message' => 'ログアウト成功'], 200);
    }
}
