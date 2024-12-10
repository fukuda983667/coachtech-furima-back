<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class ItemController extends Controller
{
    // 全item取得
    public function getItems()
    {
        $userId = Auth::id(); // ログイン中のユーザーIDを取得
        $baseUrl = Config::get('app.url') . '/storage/items/'; //envのAPP_URLを利用して商品画像を保存しているディレクトリのパスを設定

        // ログイン中のユーザーが出品した商品を除外、ゲストユーザ($userIdがnull)はそのまま全商品をフロントに渡す。
        $query = Item::query();
        if ($userId) {
            $query->where('user_id', '!=', $userId);
        }

        // アイテムを取得し、image_path プロパティと isLiked プロパティを追加
        $items = $query->get()->map(function ($item) use ($baseUrl, $userId) {
            $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;

            // isLiked プロパティを追加（ログインしている場合）
            if ($userId) {
                $item->isLiked = $item->likes()->where('user_id', $userId)->exists();
            }

            // isSold プロパティを追加（購入済みかどうかを判定）
            $item->isSold = $item->purchase()->exists();

            return $item;
        });

        // 取得したアイテムを JSON 形式で返す
        return response()->json(compact('items'), 200);
    }


    // 指定のitemの詳細を取得
    public function getItem($id)
    {
        $userId = Auth::id(); // ログイン中のユーザーIDを取得
        $baseUrl = Config::get('app.url') . '/storage/items/';

        // 指定された ID のアイテムを取得し、関連するカテゴリとコンディションも取得
        $item = Item::with(['categories', 'condition'])->find($id);

        // アイテムが存在しない場合は 404 エラーを返す
        if (!$item) {
            return response()->json(['message' => '商品が見つかりませんでした'], 404);
        }

        // 画像パスを構築
        $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;

        // isSold プロパティを追加（購入済みかどうかを判定）
        $item->isSold = $item->purchase()->exists();

        // アイテム詳細を JSON 形式で返す
        return response()->json(compact('item'), 200);
    }


    // item作成処理
    public function storeItem(Request $request)
    {
        // JSON文字列を配列にデコード
        $categories = json_decode($request->input('categories'), true);
        // デコードした配列をリクエストにマージ
        $request->merge(['categories' => $categories]);

        $validatedData = $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id', // 各IDがcategoriesテーブルに存在することを確認
            'condition_id' => 'required|exists:item_conditions,id',
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:1|max:9999999',
            'image_path' => 'required|file|mimes:jpeg,png|max:2048', // JPEG/PNG形式のみ許可、2MBまで
        ]);

        // ファイルアップロード処理
        if ($request->hasFile('image_path')) {
            $uploadedFile = $request->file('image_path');

            // 一意なファイル名を生成
            $uniqueFileName = time() . '_' . $uploadedFile->getClientOriginalName();

            // ファイルを storage/app/public/items に保存
            $path = $uploadedFile->storeAs('items', $uniqueFileName, 'public');
            $validatedData['image_path'] = $uniqueFileName;
        }

        // item作成
        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $validatedData['name'],
            'brand' => $validatedData['brand'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'condition_id' => $validatedData['condition_id'],
            'image_path' => $validatedData['image_path'],
        ]);

        // 中間テーブル(categry_itemテーブル)へのレコード作成
        $item->categories()->sync($validatedData['categories']);


        return response()->json(['message' => '商品作成処理に成功しました'], 201);
    }
}
