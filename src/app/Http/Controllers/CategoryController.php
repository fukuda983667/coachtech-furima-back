<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function getCategories()
    {
        $categories = Category::all(); // 全てのカテゴリーを取得
        // カテゴリー選択肢を JSON 形式で返す
        return response()->json(compact('categories'), 200);
    }
}
