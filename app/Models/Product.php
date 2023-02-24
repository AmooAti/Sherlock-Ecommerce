<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function categories()
    {
//        return $this->belongsToMany(Category::class, 'products_categories');
    }


    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    protected $fillable = [
        "name",
        "slug",
        "sku",
        "short_description",
        "description",
        "position",
        "status",
        "meta_title",
        "meta_description",
        "meta_keywords",
        "price",
        "sale_price",
        "sale_start",
        "sale_end",
    ];


}
