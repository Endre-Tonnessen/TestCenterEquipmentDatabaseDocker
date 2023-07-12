<?php

namespace App\Http\Controllers;

use App\Exceptions\FailedStoreCalibrationRangeException;
use App\Models\CalibrationRangeAndAccuracy;
use App\Models\Equipment;
use App\Models\ReasonForChange;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CalibrationController extends Controller
{

    /**
     * Stores a record of calibration range & accuracy in the database.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws FailedStoreCalibrationRangeException
     */
    public function store(Request $request, $id) {
        $request->validate([
            'CalibrationRangeUnit' => ['required'],
            'ReasonText' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        try {
            DB::transaction(function () use ($id,$request) {
                //Create record with justification for change
                $ReasonForChangeID = ReasonForChange::createRecord($id, $request->ReasonText);

                $calibration = new CalibrationRangeAndAccuracy();
                $calibration->equipmentID = $id;
                $calibration->Range_Lower = $request->CalibrationRangeLower;
                $calibration->Range_Upper = $request->CalibrationRangeUpper;
                $calibration->SI_Unit = $request->CalibrationRangeUnit;
                $calibration->Accuracy = $request->CalibrationRangeAccuracy;
                $calibration->ReasonForChangeID = $ReasonForChangeID;
                $calibration->save();
            });
        } catch (\Exception $e) {
            throw new FailedStoreCalibrationRangeException();
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Added calibration data successfully!",
        ]);

        //return Redirect::back()->with('modalResponse', [
        //    'icon' => 'success',
        //    'title' => "Added calibration data successfully!",
        //]);
    }

    /**
     * Marks calibration data row as archived/"deleted".
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws FailedStoreCalibrationRangeException
     */
    public function destroy(Request $request, $id) {
        $request->validate([
            'CalibrationRangeAndAccuracyID' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $ReasonForChangeID = ReasonForChange::createRecord($id, $request->ReasonText);
                $calibration = CalibrationRangeAndAccuracy::getById($request->CalibrationRangeAndAccuracyID);
                $calibration->Date_Archived= Carbon::now();
                $calibration->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Failed to remove calibration. Contact administrator if issue persists.']);
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Removed calibration data successfully!",
        ]);
    }

    public function edit(Request $request, $id) {
        $request->validate([
            'CalibrationRangeUnit' => ['required'],
            'ReasonText' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        try {
            DB::transaction(function () use ($id,$request) { // Archive old and make new.
                // Mark old entry as deleted
                $calibration = CalibrationRangeAndAccuracy::getById($request->CalibrationRangeAndAccuracyID);
                $calibration->Date_Archived= Carbon::now();
                $calibration->save();

                //Create record with justification for change
                $ReasonForChangeID = ReasonForChange::createRecord($id, $request->ReasonText);

                // Create new row
                $calibration = new CalibrationRangeAndAccuracy();
                $calibration->equipmentID = $id;
                $calibration->Range_Lower = $request->CalibrationRangeLower;
                $calibration->Range_Upper = $request->CalibrationRangeUpper;
                $calibration->SI_Unit = $request->CalibrationRangeUnit;
                $calibration->Accuracy = $request->CalibrationRangeAccuracy;
                $calibration->ReasonForChangeID = $ReasonForChangeID;
                $calibration->save();

                // Update reason text to include id's of changed rows
                $reason = ReasonForChange::find($ReasonForChangeID);
                $reason->ReasonText = $reason->ReasonText . "\n Calibration R&A. Old id: $request->CalibrationRangeAndAccuracyID New id: $calibration->id";
                $reason->save();
            });
        } catch (\Exception $e) {
            throw new FailedStoreCalibrationRangeException();
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Edited calibration data successfully!",
        ]);
    }
}
