<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    public function getAddress()
    {
        // 現在認証されているユーザーを取得
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
}
