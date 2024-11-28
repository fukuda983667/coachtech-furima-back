<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Models\User;


class UserController extends Controller
{
    // フロントのnuxt3でnuxt-auth-sanctumモジュールのメソッド使用すると勝手に/userを叩く
    public function getUser(Request $request)
    {
        // 認証されたユーザーを取得
        $user = $request->user();

        $baseUrl = Config::get('app.url') . '/storage/user-icons/'; //envのAPP_URLを利用してパスを設定
        $user->image_path = $user->image_path ? $baseUrl . $user->image_path : null;

        $user->default_address = $user->addresses()->where('is_default', true)->first();

        // ユーザー情報をログに記録（デバッグ用）
        \Log::info($user);

        // ユーザー情報を返す
        return response()->json($user,200);
    }
}
