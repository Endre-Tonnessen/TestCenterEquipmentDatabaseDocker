<?php

namespace App\Models;

use App\Exceptions\EquipmentNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalibrationRangeAndAccuracy extends Model
{
    use HasFactory;

    public static function getById($id) {
        return self::query()->findOrFail($id);
    }

    /**
     * Retrieves newest records matching equipmentID from calibration&Accuracy table.
     *
     * @param $equipmentID
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getEquipmentCalibrationAndAccuracy($equipmentID) {
        try {
            return self::query()
                ->where('equipmentID', $equipmentID)
                ->where('Date_Archived',null)
                ->get();
        } catch (\Exception $e) {
            throw new EquipmentNotFoundException();
        }
    }

    /**
     * Returns all rows that existed at the given date.
     *
     * @param $equipmentID
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws EquipmentNotFoundException
     */
    public static function getEquipmentCalibrationAndAccuracyByDate($equipmentID, $date) {
        try {
            return self::query()
                ->where('equipmentID', $equipmentID)
                ->whereNull('Date_Archived')
                ->where('created_at', '<=', $date)
                ->orWhere(function ($query) use ($equipmentID, $date) {
                    return $query
                        ->where('equipmentID', $equipmentID)
                        ->where('Date_Archived', '>=', $date)
                        ->where('created_at', '<=', $date);
                })
                ->get();

                /*
                return self::query()
                    ->where('equipmentID', $equipmentID)
                    ->where('Date_Archived', '<=', $date)
                    ->orWhere(function ($query) use ($equipmentID, $date) {
                        $query->where('created_at', '<=', $date)
                            ->whereNull('Date_Archived', '=',null)
                            ->where('equipmentID','=', $equipmentID);
                    })
                */
        } catch (\Exception $e) {
            throw new EquipmentNotFoundException();
        }
    }
}
