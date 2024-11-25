<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'user_id',
        'image_path'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // カテゴリーとの多対多リレーションを定義
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }


    // 商品状態のテキストを返すアクセサ
    public function getConditionTextAttribute()
    {
        switch ($this->condition) {
            case 1:
                return '良好';
            case 2:
                return '目立った傷や汚れなし';
            case 3:
                return 'やや傷や汚れあり';
            case 4:
                return '状態が悪い';
            default:
                return '不明'; // 条件外の場合
        }
    }
}
