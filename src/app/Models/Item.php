<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'user_id',
        'image_path',
        'condition_id',
        'brand',
    ];

    protected $appends = ['is_liked', 'is_sold'];


    // カテゴリと多対多
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function condition()
    {
        return $this->belongsTo(ItemCondition::class, 'condition_id');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }


    // image_pathのフルURLを返す
    public function getImagePathAttribute($value)
    {
        $baseUrl = Config::get('app.url') . '/storage/items/';
        return $value ? $baseUrl . $value : null;
    }

    // is_liked プロパティの追加
    public function getIsLikedAttribute()
    {
        $userId = auth()->id();
        return $userId ? $this->likes()->where('user_id', $userId)->exists() : false;
    }

    // is_sold プロパティの追加
    public function getIsSoldAttribute()
    {
        return $this->purchase()->exists();
    }
}

