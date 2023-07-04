<?php

namespace App\Http\Controllers;

use App\Models\CalibrationFrequency;
use App\Models\EquipmentNote;
use App\Models\ReasonForChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class EquipmentNoteController extends Controller
{
    /**
     * Stores a record in the equipmentNotes table.
     *
     * @param Request $request
     * @param $equipmentID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $equipmentID)
    {
        $request->validate([
            'notes' => 'required',
            'ReasonText' => 'required',
        ]);

        try {
            DB::transaction(function () use ($equipmentID, $request){
                $ReasonForChangeID = ReasonForChange::createRecord($equipmentID, $request->ReasonText);

                $EquipmentNote = new EquipmentNote();
                $EquipmentNote->equipmentID = $equipmentID;
                $EquipmentNote->notes = $request->get('notes');
                $EquipmentNote->ReasonForChangeID = $ReasonForChangeID;
                $EquipmentNote->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to update notes.',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }

        return Redirect::back()->with('toastResponse', [
            'icon' => 'success',
            'title' => "Updated notes!",
        ]);
    }


}
