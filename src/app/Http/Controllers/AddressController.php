<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    // ユーザのデフォルト住所を取得
    public function getDefaultAddress()
    {
        $user = Auth::user();

        // is_default が true のアドレスを取得
        $address = Address::where('user_id', $user->id)->where('is_default', true)->first();

        if (!$address) {
            return response()->json([
                'message' => 'デフォルトの住所が見つかりません。',
            ], 404);
        }

        return response()->json([
            'address' => $address,
        ], 200);
    }

    // 指定の住所情報取得
    public function getAddressById($id)
    {
        // ユーザー情報を取得
        $user = Auth::user();

        // 指定されたIDの住所を取得
        $address = Address::where('user_id', $user->id)->where('id', $id)->first();

        if (!$address) {
            return response()->json([
                'message' => '指定された住所が見つかりません。',
            ], 404);
        }

        return response()->json([
            'address' => $address,
        ], 200);
    }


    // 商品送付先住所作成
    public function storeAddress(AddressRequest $request)
    {
        // 現在認証されているユーザーを取得
        $user = Auth::user();

        // 送付先住所作成
        $address = Address::create([
            'user_id' => $user->id,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'building_name' => $request->building_name,
            'is_default' => false, // デフォルト住所ではない
        ]);


        return response()->json([
            'message' => '送付先住所を登録しました',
            'address' => $address,
        ], 201);
    }
}
