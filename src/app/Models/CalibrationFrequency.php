<?php

namespace App\Models;

use App\Exceptions\EquipmentNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalibrationFrequency extends Model
{
    use HasFactory;

    /**
     * Attepmts finding newest records matching equipmentID from CalibrationFrequency table.
     * If unable, returns empty result.
     *
     * @param $equipmentID
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getCalibrationFrequency($equipmentID) {
        $query = self::query()
            ->where('equipmentID', $equipmentID)
            ->orderByDesc('created_at')
            ->first();
        if (empty($query) || null) {
            $t = collect([
                'id'=>'',
                'equipmentID'=>'',
                'Cal_Interval_Year'=>0,
                'Cal_Interval_Month'=>0,
                'Calibration_location'=>'',
                'Calibration_Provider'=>'',
                'Document_Reference'=>'',
                'Last_Calibration_Date'=>'1900-1-1'
            ]);
            return json_decode($t->toJson()); //Hack. Makes sure we can access information: $data->id. Not needing ->get('id') in blades.
        } else {
            return $query;
        }
    }

    public static function getCalibrationFrequencyByDate($equipmentID, $date) {
        $query = self::query()
            ->where('equipmentID', $equipmentID)
            ->where('created_at', '<=', $date)
            ->orderByDesc('created_at')
            ->first();
        if (empty($query) || null) {
            $t = collect([
                'id'=>'',
                'equipmentID'=>'',
                'Cal_Interval_Year'=>0,
                'Cal_Interval_Month'=>0,
                'Calibration_location'=>'',
                'Calibration_Provider'=>'',
                'Document_Reference'=>'',
                'Last_Calibration_Date'=>'1900-1-1'
            ]);
            return json_decode($t->toJson()); //Hack. Makes sure we can access information: $data->id. Not needing ->get('id') in blades.
        } else {
            return $query;
        }
    }

}
