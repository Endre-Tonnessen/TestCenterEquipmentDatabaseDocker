<?php

namespace App\Http\Controllers;


use App\Exceptions\EquipmentNotFoundException;
use App\Models\Borrow;
use App\Models\Equipment;
use App\Models\Log;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DeliverController extends Controller
{
    /**
     * Returns the Deliver page
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request) {
        $equipmentID = $request->get('itemID');
        if ($equipmentID == null) $equipmentID='';

        return view('deliver', ['equipmentID' => $equipmentID]);
    }

    /**
     * Deletes item from the borrow table.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws EquipmentNotFoundException
     */
    public function deliver(Request $request) {
        $equipmentID = $request->equipmentID;

        //If empty input
        if ($equipmentID == "")  return Redirect::back()->with('modalResponse', ['icon' => 'warning', 'title' => "Please fill out Equipment ID."]);

        //If equipmentID does not exist. Promt user to check for typo.
        if (!Borrow::isEquipmentBorrowed($equipmentID)) return Redirect::back()->with('modalResponse', ['icon' => 'info', 'title' => "$equipmentID has not been borrowed."]);

        //Attempt to delete
        try {
            Borrow::deleteByEquipmentID($equipmentID);
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to delete from borrow table (Deliver equipment)',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }

        Log::store($equipmentID, "", Log::DeliverActionLog);

        $item = Equipment::getEquipmentById($equipmentID);
        $itemLocation = $item->Placement;
        //Redirect with success message
        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => 'Delivered successfully',
            'html' => "Please return $equipmentID to <label style=\'color: #25820d;\'>$itemLocation</label>",
        ]);
    }

}
