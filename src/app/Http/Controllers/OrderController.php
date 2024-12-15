<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // 購入処理
    public function storeOrder(OrderRequest $request)
    {
        // 商品がすでに購入済みかチェック
        $alreadyOrdered = Order::where('item_id', $request->item_id)->exists();

        if ($alreadyOrdered) {
            return response()->json(['message' => 'この商品はすでに購入されています'], 409);
        }

        // 購入レコード作成
        $order = Order::create([
            'user_id' => Auth::id(), // ログイン中のユーザーID
            'item_id' => $request->item_id,
            'payment_method' => $request->payment_method,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building_name' => $request->building_name,
        ]);

        // 作成したコメントの詳細を返す
        return response()->json([
            'message' => '購入が完了しました',
        ], 201);
    }
}
