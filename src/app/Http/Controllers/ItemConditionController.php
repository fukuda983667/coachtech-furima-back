<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemCondition;

class ItemConditionController extends Controller
{
    public function getConditions()
    {
        $conditions = ItemCondition::all(); // 全てのコンディションを取得
        // コンディション選択肢を JSON 形式で返す
        return response()->json(compact('conditions'), 200);
    }
}
