<?php

namespace App\Http\Controllers;

use App\Exceptions\FailedStoreCalibrationRangeException;
use App\Exceptions\FailedStoreMeasurementRangeException;
use App\Models\CalibrationRangeAndAccuracy;
use App\Models\Equipment;
use App\Models\MeasuringRangeAndAccuracy;
use App\Models\ReasonForChange;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MeasurementController extends Controller
{
    /**
     * Stores a row in measuringRangeAndAccuracy table.
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws FailedStoreMeasurementRangeException
     */
    public function store(Request $request, $id) {
        $request->validate([
            'MeasurementRangeUnit' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        try {
            DB::transaction(function () use ($id, $request) {
                //Create record with justification for change
                $ReasonForChangeID = ReasonForChange::createRecord($id, $request->ReasonText);

                $measurement = new MeasuringRangeAndAccuracy();
                $measurement->equipmentID = $id;
                $measurement->Range_Lower = $request->MeasurementRangeLower;
                $measurement->Range_Upper = $request->MeasurementRangeUpper;
                $measurement->SI_Unit = $request->MeasurementRangeUnit;
                $measurement->Accuracy = $request->MeasurementRangeAccuracy;
                $measurement->ReasonForChangeID = $ReasonForChangeID;
                $measurement->save();
            });
        } catch (\Exception $e) {
            throw new FailedStoreMeasurementRangeException();
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Added measurement data successfully!",
        ]);
    }

    /**
     * Marks measurement data row as archived/"deleted".
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     * @throws FailedStoreCalibrationRangeException
     */
    public function destroy(Request $request, $id) {
        $request->validate([
            'MeasurementRangeAndAccuracyID' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $ReasonForChangeID = ReasonForChange::createRecord($id, $request->ReasonText);
                $measurement = MeasuringRangeAndAccuracy::getById($request->MeasurementRangeAndAccuracyID);
                $measurement->Date_Archived= Carbon::now();
                $measurement->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Failed to remove measurement. Contact administrator if issue persists.']);
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Removed measurement data successfully!",
        ]);
    }

    public function edit(Request $request, $id) {
        $request->validate([
            'MeasurementRangeUnit' => ['required'],
            'ReasonText' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        try {
            DB::transaction(function () use ($id,$request) { // Archive old and make new.
                // Mark old entry as deleted
                $measurement = MeasuringRangeAndAccuracy::getById($request->MeasurementRangeAndAccuracyID);
                $measurement->Date_Archived= Carbon::now();
                $measurement->save();

                //Create record with justification for change
                $ReasonForChangeID = ReasonForChange::createRecord($id, $request->ReasonText);

                // Create new row
                $measurement = new MeasuringRangeAndAccuracy();
                $measurement->equipmentID = $id;
                $measurement->Range_Lower = $request->MeasurementRangeLower;
                $measurement->Range_Upper = $request->MeasurementRangeUpper;
                $measurement->SI_Unit = $request->MeasurementRangeUnit;
                $measurement->Accuracy = $request->MeasurementRangeAccuracy;
                $measurement->ReasonForChangeID = $ReasonForChangeID;
                $measurement->save();

                // Update reason text to include id's of changed rows
                $reason = ReasonForChange::find($ReasonForChangeID);
                $reason->ReasonText = $reason->ReasonText . "\n Measuring R&A. Old id: $request->MeasurementRangeAndAccuracyID New id: $measurement->id";
                $reason->save();
            });
        } catch (\Exception $e) {
            throw new FailedStoreMeasurementRangeException();
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Edited measurement data successfully!",
        ]);
    }
}
