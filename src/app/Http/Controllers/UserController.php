<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;
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

    public function storeProfile(ProfileRequest $request)
    {
        // 現在認証されているユーザーを取得
        $user = Auth::user();

        // ファイルアップロード処理
        if ($request->hasFile('image_path')) {
            $uploadedFile = $request->file('image_path');

            // 一意なファイル名を生成
            $uniqueFileName = time() . '_' . $uploadedFile->getClientOriginalName();

            // ファイルを保存
            $path = $uploadedFile->storeAs('user-icons', $uniqueFileName, 'public'); // storage/app/public/user-icons に保存
            $imagePath = $uniqueFileName;
        } else {
            $imagePath = $user->image_path; // 既存の画像パスを使用
        }

        // 名前と画像パスの更新
        $user->update([
            'name' => $request->input('name'),
            'image_path' => $imagePath,
        ]);

        // 既存のデフォルト住所を解除
        Address::where('user_id', $user->id)->where('is_default', true)->update(['is_default' => false]);

        // 新しい住所を追加（デフォルト設定）
        Address::create([
            'user_id' => $user->id,
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building_name' => $request->input('building_name'),
            'is_default' => true,
        ]);

            return response()->json(['message' => 'プロフィールが更新されました。',], 200);
        }

    // マイページで表示する購入itemsと出品itemsを取得
    public function getMyPageItems()
    {
        $userId = Auth::id(); // ログイン中のユーザーIDを取得
        $baseUrl = Config::get('app.url') . '/storage/items/'; // 商品画像のベースURL

        // 購入したアイテムを取得
        $purchasedItems = Purchase::where('user_id', $userId)
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
                $item->is_sold = $item->purchase()->exists();
                return $item;
            });

        return response()->json([
            'purchased_items' => $purchasedItems,
            'listed_items' => $listedItems,
        ], 200);
    }
}
