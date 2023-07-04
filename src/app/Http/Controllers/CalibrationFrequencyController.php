<?php

namespace App\Http\Controllers;

use App\Models\CalibrationFrequency;
use App\Models\ReasonForChange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CalibrationFrequencyController extends Controller
{
    /**
     * Stores row in reasonForChange table.
     *
     * @param Request $request
     * @param $equipmentID
     * @return RedirectResponse
     */
    public function store(Request $request, $equipmentID)
    {
        try {
            DB::transaction(function () use ($equipmentID, $request) {
                $ReasonForChangeID = ReasonForChange::createRecord($equipmentID, $request->ReasonText);

                $calibrationFrequency = new CalibrationFrequency();
                $calibrationFrequency->equipmentID = $equipmentID;
                $calibrationFrequency->Cal_Interval_Year = $request->get('Cal_Interval_Year');
                $calibrationFrequency->Cal_Interval_Month = $request->get('Cal_Interval_Month');
                $calibrationFrequency->Last_Calibration_Date = $request->get('Last_Calibration_Date');
                $calibrationFrequency->Calibration_location = $request->get('Calibration_location');
                $calibrationFrequency->Calibration_Provider = $request->get('Calibration_Provider');
                $calibrationFrequency->Document_Reference = $request->get('Document_Reference');
                $calibrationFrequency->ReasonForChangeID = $ReasonForChangeID;
                $calibrationFrequency->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to update calibration frequency.',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Updated calibration frequency!",
        ]);
    }

    public function destroy()
    {
        //
    }
}
