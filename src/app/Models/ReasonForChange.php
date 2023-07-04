<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ReasonForChange extends Model
{
    use HasFactory;

    /**
     * Creates a record in ReasonForChangeTable and returns the primary key of that row.
     *
     * @return int Primary key of ReasonForChangeTable
     */
    public static function createRecord($EquipmentID, $ReasonText): int
    {
        $ReasonForChangeID = ReasonForChange::query()->insertGetId(
            [
                'EquipmentID' => $EquipmentID,
                'ReasonText' => $ReasonText,
                'UserID' => Auth::id(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
        return $ReasonForChangeID;
    }

    /**
     * Returns the latest reason for change for given equipmentID.
     *
     * @param $EquipmentID
     * @return Builder|Model|object
     */
    public static function getLatestChange($EquipmentID) {
        return self::query()
            ->where('equipmentID', $EquipmentID)
            ->orderByDesc('created_at')
            ->first();
    }

}
