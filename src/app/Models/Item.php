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
        'image_path',
        'condition_id',
        'brand',
    ];


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
}

