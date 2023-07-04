<?php

namespace App\Models;

use App\Exceptions\EquipmentNotFoundException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Equipment extends Model
{
    use HasFactory;

    private static function getCalibratedRanges(): \Illuminate\Database\Eloquent\Builder
    {
        return CalibrationRangeAndAccuracy::query()
            ->select('calibration_range_and_accuracies.equipmentID as calEquipmentID', 'calibration_range_and_accuracies.Range_Lower', 'calibration_range_and_accuracies.Range_Upper', 'calibration_range_and_accuracies.SI_Unit', 'calibration_range_and_accuracies.Date_Archived as calibrationDateArchived')
            ->whereNull('calibration_range_and_accuracies.Date_Archived')
            ->distinct('calibration_range_and_accuracies.equipmentID');
    }

    public static function getAllEquipment()
    {
        return self::query()
            ->leftJoinSub(self::getCalibratedRanges(), 'calibratedRanges', function ($join) {
                $join->on('calEquipmentID', '=', 'equipment.equipmentID');
            })
            ->where('equipment.Deleted', '<>', 1)
            ->whereNull('equipment.Date_Archived')
            ->select('equipment.*','calibratedRanges.Range_Lower', 'calibratedRanges.Range_Upper', 'calibratedRanges.SI_Unit')
            ->addSelect(DB::raw("case when exists (select `borrow`.`equipmentid` from `borrow` where `borrow`.`equipmentid` = `equipment`.`equipmentid`) then 1 else 0 end as 'borrowed'")) //Check if borrowed
            ->get()
            ->unique('equipmentID');
    }

    public static function getEquipmentByTag($categoryId)
    {
        return self::query()
            ->where('category_id', '=', $categoryId)
            ->where('Deleted', '<>', 1)
            ->where('Date_Archived',null)
            ->leftJoinSub(self::getCalibratedRanges(), 'calibratedRanges', function ($join) {
                $join->on('calEquipmentID', '=', 'equipment.equipmentID');
            })
            ->select('equipment.*','calibratedRanges.Range_Lower', 'calibratedRanges.Range_Upper', 'calibratedRanges.SI_Unit')
            ->addSelect(DB::raw("case when exists (select `borrow`.`equipmentid` from `borrow` where `borrow`.`equipmentid` = `equipment`.`equipmentid`) then 1 else 0 end as 'borrowed'")) //Check if borrowed
            ->get()
            ->unique('equipmentID');
    }

    public static function getEquipmentByMultiSearch($userInput)
    {
        return self::query()
            ->where('equipment.equipmentID', 'like', '%' . "$userInput" . '%',)
            ->where('Deleted', '<>', 1)
            ->where('Date_Archived',null)
            ->orWhere(function ($query) use ($userInput) {
                return $query
                    ->orWhere('Placement', 'like', '%' . $userInput . '%')
                    ->where('Deleted', '<>', 1)
                    ->where('Date_Archived',null);
            })
            ->orWhere(function ($query) use ($userInput) {
                return $query
                    ->orWhere('Description', 'like', '%' . $userInput . '%')
                    ->where('Deleted', '<>', 1)
                    ->where('Date_Archived',null);
            })
            ->orWhere(function ($query) use ($userInput) {
                return $query
                    ->orWhere('SI_Unit', 'like', '%' . $userInput . '%')
                    ->where('Deleted', '<>', 1)
                    ->where('Date_Archived',null);
            })
            ->leftJoinSub(self::getCalibratedRanges(), 'calibratedRanges', function ($join) {
                $join->on('calEquipmentID', '=', 'equipment.equipmentID');
            })
            ->select('equipment.*','calibratedRanges.Range_Lower', 'calibratedRanges.Range_Upper', 'calibratedRanges.SI_Unit')
            ->addSelect(DB::raw("case when exists (select `borrow`.`equipmentid` from `borrow` where `borrow`.`equipmentid` = `equipment`.`equipmentid`) then 1 else 0 end as 'borrowed'")) //Check if borrowed
            ->get()
            ->unique('equipmentID');
        /*
          return self::query()
             ->where('equipmentID', 'like', '%' . "$userInput" . '%',)
             ->orWhere('Placement', 'like', '%' . $userInput . '%')
             ->orWhere('Description', 'like', '%' . $userInput . '%')
             //->orWhere('SI_Unit', 'like', '%' . $userInput . '%')
             ->where('Deleted', '<>', 1)
             ->where('Date_Archived',null)
             ->addSelect(DB::raw("case when exists (select `borrow`.`equipmentid` from `borrow` where `borrow`.`equipmentid` = `equipment`.`equipmentid`) then 1 else 0 end as 'borrowed'")) //Check if borrowed
             ->select('*')
             ->get();
         */
    }

    public static function getEquipmentBySingleSearch($selectedSearch, $userInput)
    {
        return self::query()
            ->leftJoinSub(self::getCalibratedRanges(), 'calibratedRanges', function ($join) {
                $join->on('calEquipmentID', '=', 'equipment.equipmentID');
            })
            ->where('equipment.Date_Archived',null)
            ->where($selectedSearch, 'like', '%' . $userInput . '%')
            ->where('Deleted', '<>', 1)
            ->select('equipment.*','calibratedRanges.Range_Lower', 'calibratedRanges.Range_Upper', 'calibratedRanges.SI_Unit as SI_Unit')
            ->addSelect(DB::raw("case when exists (select `borrow`.`equipmentid` from `borrow` where `borrow`.`equipmentid` = `equipment`.`equipmentid`) then 1 else 0 end as 'borrowed'")) //Check if borrowed
            ->get()
            ->unique('equipmentID');
    }

    public static function doesEquipmentExists($equipmentID): bool
    {
        return self::query()->where('equipmentID', $equipmentID)->exists();
    }


    /**
     * Retrieves newest record matching equipmentID from equipment table.
     *
     * @param $equipmentID
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getEquipmentById($equipmentID) {
        try {
            return self::query()
                ->where('equipmentID', $equipmentID)
                ->where('Date_Archived',null)
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new EquipmentNotFoundException();
        }
    }

    public static function getEquipmentByIdAndDate($equipmentID, $versionDate) {
        try {
            return self::query()
                ->where('equipmentID', $equipmentID)
                ->where('created_at','<=', $versionDate)
                //->where('Date_Archived','<>',null)
                //->where('Date_Archived', '<=', $versionDate)
                //->orderByDesc(DB::raw("ABS(DATEDIFF(equipment.Date_archived, '$versionDate'))"))
                ->orderByDesc('created_at')
                ->limit(1)
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new EquipmentNotFoundException();
        }
        // SELECT * FROM `equipment` WHERE (`equipmentID`='test3') AND (equipment.Date_Archived!='') AND (equipment.Date_archived < '2022-06-26 12:41:47') ORDER BY ABS(DATEDIFF(equipment.Date_archived, '2022-06-26 12:41:47')) LIMIT 1;
        // SELECT * FROM `equipment` WHERE (`equipmentID`='Test2') AND (equipment.Date_Archived!='') AND (equipment.Date_archived < '2022-06-29 13:00:30') ORDER BY ABS(DATEDIFF(equipment.Date_archived, '2022-06-29 13:00:30'));
        // TODO: THIS MAY WORK: SELECT * FROM `equipment` WHERE (`equipmentID`='Test2') AND (equipment.Date_Archived!='') AND (equipment.Date_archived < '2022-06-29 13:00:30') ORDER BY id DESC;
    }


    public static function getAllDeletedEquipment()
    {
        return self::query()->where('Deleted', 1)->get();
    }

    public static function deleteEquipmentByEquipmentID($equipmentID) {
        return self::query()->where('equipmentID', $equipmentID)->first()->delete();
    }



    public static function markEquipmentAsDeleted($equipmentID): bool
    {
        return self::markEquipmentAsDeletedOrNot($equipmentID, 1);
    }

    public static function markEquipmentAsNotDeleted($equipmentID): bool
    {
        return self::markEquipmentAsDeletedOrNot($equipmentID, 0);
    }

    /**
     * Updates the given equipment if it is marked as deleted or not.
     * @param $equipmentID
     * @param Int $isDeleted
     * @return bool : True on success
     */
    private static function markEquipmentAsDeletedOrNot($equipmentID, Int $isDeleted): bool
    {
        $getEquipment = self::getEquipmentById($equipmentID);
        $getEquipment->Deleted = $isDeleted;
        return $getEquipment->save();
    }
}
