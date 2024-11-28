<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethod::all(); // 全ての支払い方法を取得
        // 支払い方法選択肢を JSON 形式で返す
        return response()->json(compact('paymentMethods'), 200);
    }
}
