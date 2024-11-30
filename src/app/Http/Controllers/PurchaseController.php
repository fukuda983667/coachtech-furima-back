<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入処理
    public function storePurchase(Request $request)
    {
        // バリデーション
        $validatedData = $request->validate([
            'item_id' => 'required|exists:items,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'postal_code' => 'required|string|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building_name' => 'required|string|max:255',
        ]);

        // 購入レコード作成
        $purchase = Purchase::create([
            'user_id' => Auth::id(), // ログイン中のユーザーID
            'item_id' => $validatedData['item_id'],
            'payment_method_id' => $validatedData['payment_method_id'],
            'postal_code' => $validatedData['postal_code'],
            'address' => $validatedData['address'],
            'building_name' => $validatedData['building_name'],
        ]);

        // 作成したコメントの詳細を返す
        return response()->json([
            'message' => '購入が完了しました',
        ], 201);
    }
}
