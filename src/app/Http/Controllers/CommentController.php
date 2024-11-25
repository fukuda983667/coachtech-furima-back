<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class CommentController extends Controller
{
    // 指定のitem_idに紐づくコメントを取得
    public function getComments($id)
    {
        $baseUrl = Config::get('app.url') . '/storage/user-icons/'; //envのAPP_URLを利用してパスを設定
        Carbon::setLocale('ja'); // Carbonを日本語に設定

        // 指定された item_id に紐づくコメントを取得
        $comments = Comment::where('item_id', $id)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'image_path'); // 必要なフィールドを選択
                }
            ])
            ->get(['id', 'user_id', 'comment', 'created_at', 'item_id']); // 必要なコメントフィールドを選択

        // 各コメントの user->image_path に $baseUrl を結合
        $comments->each(function ($comment) use ($baseUrl) {
            if ($comment->user && $comment->user->image_path) {
                // もしimage_pathがすでにフルURLの場合、baseUrlを結合しない
                if (strpos($comment->user->image_path, 'http') !== 0) {
                    $comment->user->image_path = $baseUrl . $comment->user->image_path;
                }
            }

            // コメントの作成日時を相対的な時間として取得
            $comment->created_at_relative = Carbon::parse($comment->created_at)->diffForHumans();
        });

        // コメント件数を取得
        $comment_count = $comments->count();

        // コメントが存在しない場合でも空配列を返す
        return response()->json(compact(['comments', 'comment_count']), 200);
    }

    // コメント送信機能
    public function storeComment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'comment' => 'required|string|max:255',
        ]);

        // コメント作成
        $comment = Comment::create([
            'user_id' => $validated['user_id'],
            'item_id' => $validated['item_id'],
            'comment' => $validated['comment'],
        ]);

        // 作成したコメントの詳細を返す
        return response()->json([
            'message' => 'コメントを追加しました',
        ], 201);
    }
}
