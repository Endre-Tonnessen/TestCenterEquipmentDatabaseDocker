<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';



    public static function getAllCategories()
    {
        return self::query()->select()->get();
    }


    public static function getCategoryName($id): String
    {
        return self::query()->where('id',$id)->first()->category_name;
    }
}
