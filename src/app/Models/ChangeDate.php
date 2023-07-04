<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeDate extends Model
{
    use HasFactory;

    /**
     * Returns all dates any field related to the specified equipmentID changed. Exception image and borrow/deliver Log.
     *      **Important: This does not include the events where calibration and measuring range & accuracy was deleted.
     *                   Currently, not in use.
     *
     * @param $equipmentID
     * @return Builder[]|Collection
     */
    public static function getDatesChanged($equipmentID) {
        $equipment = Equipment::query()
            ->join('reason_for_changes','ReasonForChangeID','=', 'reason_for_changes.id')
            ->join('users','users.id','=', 'reason_for_changes.UserID')
            ->where('equipment.equipmentID',$equipmentID)
            ->select(
                'equipment.EquipmentID as EquipmentID',
                'equipment.created_at as created_at',
                'equipment.ReasonForChangeID as ReasonForChangeID',
                'reason_for_changes.ReasonText as ReasonText',
                'users.name',
                'users.email'
            );
        $calFreq = CalibrationFrequency::query()
            ->join('reason_for_changes','ReasonForChangeID','=', 'reason_for_changes.id')
            ->join('users','users.id','=', 'reason_for_changes.UserID')
            ->where('calibration_frequencies.equipmentID',$equipmentID)
            ->select(
                'calibration_frequencies.EquipmentID as EquipmentID',
                'calibration_frequencies.created_at as created_at',
                'calibration_frequencies.ReasonForChangeID as ReasonForChangeID',
                'reason_for_changes.ReasonText as ReasonText',
                'users.name',
                'users.email'
            );
        $calRangeAccuracy = CalibrationRangeAndAccuracy::query()
            ->join('reason_for_changes','ReasonForChangeID','=', 'reason_for_changes.id')
            ->join('users','users.id','=', 'reason_for_changes.UserID')
            ->where('calibration_range_and_accuracies.equipmentID',$equipmentID)
            ->select(
                'calibration_range_and_accuracies.EquipmentID as EquipmentID',
                'calibration_range_and_accuracies.created_at as created_at',
                'calibration_range_and_accuracies.ReasonForChangeID as ReasonForChangeID',
                'reason_for_changes.ReasonText as ReasonText',
                'users.name',
                'users.email'
            );
        $mesRangeAccuracy = MeasuringRangeAndAccuracy::query()
            ->join('reason_for_changes','ReasonForChangeID','=', 'reason_for_changes.id')
            ->join('users','users.id','=', 'reason_for_changes.UserID')
            ->where('measuring_range_and_accuracies.equipmentID',$equipmentID)
            ->select(
                'measuring_range_and_accuracies.EquipmentID as EquipmentID',
                'measuring_range_and_accuracies.created_at as created_at',
                'measuring_range_and_accuracies.ReasonForChangeID',
                'reason_for_changes.ReasonText as ReasonText',
                'users.name',
                'users.email'
            );
        $equipmentNotes = EquipmentNote::query()
            ->join('reason_for_changes','ReasonForChangeID','=', 'reason_for_changes.id')
            ->join('users','users.id','=', 'reason_for_changes.UserID')
            ->where('equipment_notes.equipmentID',$equipmentID)
            ->select(
                'equipment_notes.EquipmentID as EquipmentID',
                'equipment_notes.created_at as created_at',
                'equipment_notes.ReasonForChangeID as ReasonForChangeID',
                'reason_for_changes.ReasonText as ReasonText',
                'users.name',
                'users.email'
            );

        return $equipment
            ->union($calFreq)
            ->union($calRangeAccuracy)
            ->union($mesRangeAccuracy)
            ->union($equipmentNotes)
            ->orderByDesc('created_at')
            ->get();
    }
}
