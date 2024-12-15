<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Purchase;


class UserController extends Controller
{
    // フロントのnuxt3でnuxt-auth-sanctumモジュールのメソッド使用すると勝手に/userを叩く
    public function getUser(Request $request)
    {
        // 認証されたユーザーを取得
        $user = $request->user();

        $baseUrl = Config::get('app.url') . '/storage/user-icons/'; //envのAPP_URLを利用してパスを設定
        $user->image_path = $user->image_path ? $baseUrl . $user->image_path : null;

        // ユーザー情報をログに記録（デバッグ用）
        \Log::info($user);

        // ユーザー情報を返す
        return response()->json($user,200);
    }

    public function storeProfile(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'image_path' => 'nullable|file|mimes:jpeg,png|max:2048', // JPEG/PNG形式のみ許可、2MBまで
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ]);

        // 現在認証されているユーザーを取得
        $user = Auth::user();

        // ファイルアップロード処理
        if ($request->hasFile('image_path')) {
            $uploadedFile = $request->file('image_path');

            // 一意なファイル名を生成
            $uniqueFileName = time() . '_' . $uploadedFile->getClientOriginalName();

            // ファイルを保存
            $path = $uploadedFile->storeAs('user-icons', $uniqueFileName, 'public'); // storage/app/public/user-icons に保存
            $validatedData['image_path'] = $uniqueFileName;
        }

        // データの更新
        $user->update([
            'name' => $validatedData['name'],
            'image_path' => $validatedData['image_path'] ?? $user->image_path,
            'postal_code' => $validatedData['postal_code'],
            'address' => $validatedData['address'],
            'building_name' => $validatedData['building_name'] ?? $user->building_name,
        ]);

        return response()->json(['message' => 'プロフィールが更新されました。',], 200);
    }

    // マイページで表示する購入itemsと出品itemsを取得
    public function getMyPageItems()
    {
        $userId = Auth::id(); // ログイン中のユーザーIDを取得
        $baseUrl = Config::get('app.url') . '/storage/items/'; // 商品画像のベースURL

        // 購入したアイテムを取得
        $purchasedItems = Order::where('user_id', $userId)
            ->with('item') // アイテムリレーションをロード
            ->get()
            ->map(function ($purchase) use ($baseUrl) {
                // 各購入データに関連するアイテムを取得
                if ($purchase->item) {
                    $item = $purchase->item;
                    $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;
                    $item->is_sold = true;
                    return $item;
                }
                return null;
            })->filter(); // null の値を除外

        // 出品したアイテムを取得
        $listedItems = Item::where('user_id', $userId)
            ->get()
            ->map(function ($item) use ($baseUrl) {
                $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;
                // isSold プロパティを追加（購入済みかどうかを判定）
                $item->is_sold = $item->order()->exists();
                return $item;
            });

        return response()->json([
            'purchased_items' => $purchasedItems,
            'listed_items' => $listedItems,
        ], 200);
    }
}
