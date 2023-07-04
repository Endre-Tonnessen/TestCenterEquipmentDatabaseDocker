<?php

namespace App\Models;

use App\Exceptions\EquipmentNotFoundException;
use App\Exceptions\MeasuringRangeAccuracyException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasuringRangeAndAccuracy extends Model
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
    public static function getEquipmentMeasurementAndAccuracy($equipmentID) {
        try {
            return self::query()
                ->where('equipmentID', $equipmentID)
                ->where('Date_Archived',null)
                ->get();
        } catch (\Exception $e) {
            throw new MeasuringRangeAccuracyException();
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
    public static function getEquipmentMeasurementAndAccuracyByDate($equipmentID, $date) {
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
        } catch (\Exception $e) {
            throw new MeasuringRangeAccuracyException();
        }
    }
}
