<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
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
                    $query->select('id', 'name', 'image_path'); // コメントしたユーザ情報取得
                }
            ])
            ->get(['id', 'user_id', 'comment', 'created_at', 'item_id']); // コメント情報取得

        // 各コメントの user->image_path に $baseUrl を結合
        $comments->each(function ($comment) use ($baseUrl) {
            // ユーザー情報の存在と画像パスを確認
            $user = $comment->user;
            $imagePath = $user->image_path ?? null;
            // フルURLに変換
            if ($imagePath && strpos($imagePath, 'http') !== 0) {
                $user->image_path = $baseUrl . $imagePath;
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
        $validatedData = $request->validate([
            'item_id' => 'required|exists:items,id',
            'comment' => 'required|string|max:255',
        ]);

        // コメント作成
        $comment = Comment::create([
            'user_id' => Auth::id(), // ログイン中のユーザーID
            'item_id' => $validatedData['item_id'],
            'comment' => $validatedData['comment'],
        ]);

        // 作成したコメントの詳細を返す
        return response()->json([
            'message' => 'コメントを追加しました',
        ], 201);
    }
}
