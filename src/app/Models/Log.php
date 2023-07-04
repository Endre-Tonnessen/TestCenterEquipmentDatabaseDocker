<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'log';

    //Log actions
    public const DeliverActionLog  = 0;
    public const BorrowActionLog   = 1;
    public const DeleteActionLog   = 2;
    public const UnDeleteActionLog = 3;
    public const ReRegisterActionLog = 4;

    private const ActionNames = [
        self::BorrowActionLog   => [
            'name' => 'Borrowed',
            'colour' => ''
        ],
        self::DeliverActionLog  => [
            'name' => 'Delivered',
            'colour' => 'lightgreen'
        ],
        self::DeleteActionLog   => [
            'name' => 'Deleted',
            'colour' => 'lightgrey'
        ],
        self::UnDeleteActionLog => [
            'name' => 'UnDeleted',
            'colour' => '#7c8bf5'
        ],
        self::ReRegisterActionLog => [
            'name' => 'Re-registered',
            'colour' => 'orange'
        ]
    ];

    /**
     * Gets the name of the Log action.
     * @param int $action
     * @return array
     */
    public static function getActionLogName(int $action): array
    {
        return self::ActionNames[$action];
    }

    /**
     * Returns all log entries for this item.
     * @param $equipmentID
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getLogByEquipmentId($equipmentID) {
        return self::query()->where('equipmentID', $equipmentID)->get();
    }

    public static function store($equipmentID, $person_responsible, $action) {
        $log = new Log();
        $log->equipmentID = $equipmentID;
        $log->person_responsible = $person_responsible;
        $log->action = $action;
        $log->save();
    }



}
