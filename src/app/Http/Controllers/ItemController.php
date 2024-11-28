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
        if (!is_null($userId)) {
            $query->where('user_id', '!=', $userId);
        }

        // アイテムを取得し、image_path プロパティと isLiked プロパティを追加
        $items = $query->get()->map(function ($item) use ($baseUrl, $userId) {
            $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;

            // isLiked プロパティを追加（ログインしている場合のみ判定）
            if (!is_null($userId)) {
                $item->isLiked = $item->likes()->where('user_id', $userId)->exists();
            } else {
                $item->isLiked = false; // ゲストユーザーはすべてfalse
            }

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

        // 指定された ID のアイテムを取得し、関連するカテゴリも取得
        $item = Item::with('categories')->find($id);

        // アイテムが存在しない場合は 404 エラーを返す
        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // 画像パスを構築
        $item->image_path = $item->image_path ? $baseUrl . $item->image_path : null;

        // condition のテキストを追加 1~4で管理してるやつ
        $item->condition_text = $item->condition_text;


        // アイテム詳細を JSON 形式で返す
        return response()->json(compact('item'), 200);
    }
}
