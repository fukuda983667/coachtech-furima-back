<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入処理
    public function storePurchase(PurchaseRequest $request)
    {
        // 商品がすでに購入済みかチェック
        $alreadyPurchased = Purchase::where('item_id', $request->item_id)->exists();

        if ($alreadyPurchased) {
            return response()->json(['message' => 'この商品はすでに購入されています'], 409);
        }

        // 購入レコード作成
        $Purchase = Purchase::create([
            'user_id' => Auth::id(), // ログイン中のユーザーID
            'item_id' => $request->item_id,
            'address_id' => $request->address_id,
            'payment_method' => $request->payment_method,
        ]);

        // 作成したコメントの詳細を返す
        return response()->json([
            'message' => '購入が完了しました',
        ], 201);
    }
}
