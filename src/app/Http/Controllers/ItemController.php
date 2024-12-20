<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;
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

        // アイテムを取得し、image_path プロパティと is_liked プロパティを追加
        $items = $query->get()->map(function ($item) use ($baseUrl, $userId) {
            $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;

            // isLiked プロパティを追加（ログインしている場合）
            if ($userId) {
                $item->is_liked = $item->likes()->where('user_id', $userId)->exists();
            }

            // isSold プロパティを追加（購入済みかどうかを判定）
            $item->is_sold = $item->purchase()->exists();

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
        $item->is_sold = $item->purchase()->exists();

        // アイテム詳細を JSON 形式で返す
        return response()->json(compact('item'), 200);
    }


    // item作成処理
    public function storeItem(ItemRequest $request)
    {
        // ファイルアップロード処理
        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $uploadedFile = $request->file('image_path');

            // 一意なファイル名を生成
            $uniqueFileName = time() . '_' . $uploadedFile->getClientOriginalName();

            // ファイルを storage/app/public/items に保存
            $uploadedFile->storeAs('items', $uniqueFileName, 'public');
            $imagePath = $uniqueFileName;
        }

        // item作成
        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->input('name'),
            'brand' => $request->input('brand'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'condition_id' => $request->input('condition_id'),
            'image_path' => $imagePath,  // アップロードされた画像のパスを保存
        ]);

        // 中間テーブル(categry_itemテーブル)へのレコード作成
        $item->categories()->sync($request->input('categories'));

        return response()->json([
            'message' => '商品作成処理に成功しました',
            'item' => $item,
        ], 201);
    }
}
