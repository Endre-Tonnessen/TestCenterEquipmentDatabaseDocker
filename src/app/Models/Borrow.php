<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    protected $table = 'borrow';



    public static function getAllBorrowedEquipment() {
        return self::query()->get();
    }

    public static function isEquipmentBorrowed($equipmentID): bool
    {
        return self::query()->where('equipmentID', $equipmentID)->exists();
    }

    public static function deleteByEquipmentID($equipmentID) {
        return self::query()->where('equipmentID', $equipmentID)->delete();
    }

    public static function getBorrowed($equipmentID) {
        return self::query()->where('equipmentID', $equipmentID)->get();
    }
}
