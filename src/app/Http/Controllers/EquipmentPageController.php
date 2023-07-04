<?php

namespace App\Http\Controllers;

use App\Exceptions\EquipmentNotFoundException;
use App\Models\Borrow;
use App\Models\CalibrationFrequency;
use App\Models\CalibrationRangeAndAccuracy;
use App\Models\Category;
use App\Models\ChangeDate;
use App\Models\Equipment;
use App\Models\EquipmentNote;
use App\Models\Log;
use App\Models\MeasuringRangeAndAccuracy;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class EquipmentPageController extends Controller
{

    /**
     *  Returns the equipment page with the most recent information.
     */
    public function index($id) {
        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::to('/');
        }

        $equipment = Equipment::getEquipmentById($id);
        $log = Log::getLogByEquipmentId($id);
        $calibrationRangeAndAccuracy = CalibrationRangeAndAccuracy::getEquipmentCalibrationAndAccuracy($id);
        $measurementRangeAndAccuracy = MeasuringRangeAndAccuracy::getEquipmentMeasurementAndAccuracy($id);
        $calibrationFrequency = CalibrationFrequency::getCalibrationFrequency($id);
        $allCategories = Category::getAllCategories();
        $notes = EquipmentNote::getEquipmentNoteByEquipmentId($id);
        $versionDateTime = "";
        //$changeDates = ChangeDate::getDatesChanged($id);
        $changeDates = \App\Models\ReasonForChange::query()
            ->where('equipmentID', $id)
            ->join('users', 'reason_for_changes.UserID', '=','users.id')
            ->orderByDesc('reason_for_changes.created_at')
            ->select('reason_for_changes.created_at','ReasonText', 'name')
            ->get();

        return view('equipment-page', compact(
            'id',
            'equipment',
            'log',
            'allCategories',
            'calibrationRangeAndAccuracy',
            'measurementRangeAndAccuracy',
            'calibrationFrequency',
            'notes',
            'versionDateTime',
            'changeDates'));
    }

    /**
     * Returns the equipment page view as it was on the given date.
     *
     * @param $id : EquipmentID
     * @param $versionDateTime
     * @return Application|Factory|View|RedirectResponse
     * @throws EquipmentNotFoundException
     */
    public function indexByDateTime($id, $versionDateTime) {
        //If user asks for a date greater than current, redirect to newest/index page.
        if ($versionDateTime > Carbon::now()) {
            return Redirect::to("Equipment/$id");
        }

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::to('/');
        }

        $equipment = Equipment::getEquipmentByIdAndDate($id, $versionDateTime);
        $calibrationRangeAndAccuracy = CalibrationRangeAndAccuracy::getEquipmentCalibrationAndAccuracyByDate($id, $versionDateTime);
        $measurementRangeAndAccuracy = MeasuringRangeAndAccuracy::getEquipmentMeasurementAndAccuracyByDate($id, $versionDateTime);
        $log = Log::getLogByEquipmentId($id);
        $allCategories = Category::getAllCategories();
        $notes = EquipmentNote::getEquipmentNoteByDate($id, $versionDateTime);
        $calibrationFrequency = CalibrationFrequency::getCalibrationFrequencyByDate($id, $versionDateTime);
        //$changeDates = ChangeDate::getDatesChanged($id);
        $changeDates = \App\Models\ReasonForChange::query()
            ->where('equipmentID', $id)
            ->join('users', 'reason_for_changes.UserID', '=','users.id')
            ->orderByDesc('reason_for_changes.created_at')
            ->select('reason_for_changes.created_at','ReasonText', 'name')
            ->get();
        return view('equipment-page', compact(
            'id',
            'equipment',
            'log',
            'allCategories',
            'calibrationRangeAndAccuracy',
            'measurementRangeAndAccuracy',
            'notes',
            'calibrationFrequency',
            'versionDateTime',
            'changeDates'));
    }
}
