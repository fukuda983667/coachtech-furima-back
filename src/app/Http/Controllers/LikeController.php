<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // 指定されたitem_idのいいね状況といいね件数を取得
    public function getLike($id)
    {
        $userId = Auth::id(); // ログイン中のユーザーIDを取得

        // お気に入り状況（isLiked）を取得
        $isLiked = $userId ? Like::where('user_id', $userId)->where('item_id', $id)->exists() : false;

        // お気に入り件数（like_count）を取得
        $likeCount = Like::where('item_id', $id)->count();

        return response()->json([
            'isLiked' => $isLiked,
            'like_count' => $likeCount
        ], 200);
    }

    // お気に入り登録と解除の切り替え 解除はレコードの物理削除
    public function toggleLike(Request $request)
    {
        $itemId = $request->input('item_id');
        $userId = Auth::id();

        $like = Like::where('user_id', $userId)
                    ->where('item_id', $itemId)
                    ->first();

        if ($like) {
            $like->delete();

            // お気に入り件数を取得
            $likeCount = Like::where('item_id', $itemId)->count();

            return response()->json([
                'message' => 'お気に入りを解除しました',
                'isLiked' => false,
                'like_count' => $likeCount,
            ], 200);
        } else {
            Like::create([
                'user_id' => $userId,
                'item_id' => $itemId,
            ]);

            // お気に入り件数を取得
            $likeCount = Like::where('item_id', $itemId)->count();

            return response()->json([
                'message' => 'お気に入り登録しました',
                'isLiked' => true,
                'like_count' => $likeCount,
            ], 201);
        }
    }
}