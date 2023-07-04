<?php

namespace App\Models;

use App\Exceptions\EquipmentNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentNote extends Model
{
    use HasFactory;

    /**
     * If exists, returns the newest notes record.
     *
     * @param $equipmentID
     * @return Builder|Model|object|null
     */
    public static function getEquipmentNoteByEquipmentId($equipmentID) {
        return self::query()
            ->where('equipmentID', $equipmentID)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     *
     *
     * @param $equipmentID
     * @param $date
     * @return Builder|Model|object|null
     */
    public static function getEquipmentNoteByDate($equipmentID, $date) {
        return self::query()
            ->where('equipmentID', $equipmentID)
            ->where('created_at', '<=', $date)
            ->orderByDesc('created_at')
            ->first();
    }

}
