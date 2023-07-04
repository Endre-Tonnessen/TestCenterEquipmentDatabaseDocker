<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Equipment;
use App\Models\Log;
use http\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class BorrowController extends Controller
{

    /**
     * Returns view of borrow page.
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request) {
        $equipmentID = $request->get('itemID');
        if ($equipmentID == null) $equipmentID='';

        return view('borrow', ['equipmentID' => $equipmentID]);
    }

    /**
     * Handles borrow request.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function borrow(Request $request) {
        $equipmentID = $request->equipmentID;

        //If empty request
        if ($equipmentID == "" | $request->borrowName == "")  return Redirect::back()->with('modalResponse', ['icon' => 'warning', 'title' => "Please fill out both name and equipmentID."]);

        //If equipmentID does not exist. Promt user to check for typo.
        $doesExist = Equipment::doesEquipmentExists($equipmentID);
        if (!$doesExist) return Redirect::back()->with('modalResponse', ['icon' => 'warning', 'title' => "$equipmentID does not exist. Check for typo."]);

        $isBorrowed = Borrow::isEquipmentBorrowed($equipmentID);
        $getBorrowed = Borrow::getBorrowed($equipmentID);
        if ($isBorrowed) return Redirect::back()->withInput()->with('reRegister', $getBorrowed); //Promt user with re-register modal.

        Log::store($request->equipmentID, $request->borrowName, Log::BorrowActionLog);

        return $this->store($request);
    }

    /**
     * Handles re-registering an already borrowed item.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function reRegister(Request $request) {
        //Deliver/delete equipment
        $this->destroy($request);

        Log::store($request->equipmentID, $request->borrowName, Log::ReRegisterActionLog);

        //Then borrow/register in new name
        return $this->store($request);
    }

    /**
     *  Creates a Borrow row in the DB.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'equipmentID' => ['required'],
            'borrowName' => ['required']
        ]);

        try {
            $borrow = new Borrow();
            $borrow -> equipmentID = $request -> equipmentID;
            $borrow -> borrowName = $request -> borrowName;
            $borrow -> save();
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to borrow',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }

        $url = url('/');
        //Redirect with success message
        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => 'Borrowed successfully',
            'html' => "$request->equipmentID is now registered on <b>$request->borrowName</b>",
            'confirmedJavascript' => "" //"window.location.href = \"$url\"" <-- Should redirect to main page when clicked Confirm button. Problem: Redirect immediately after borrow success, should wait on user to click ok.
        ]);
    }

    /**
     * This is soft deletion. Item is only marked as deleted, but not removed from the database.
     * @return RedirectResponse
     */
    public function destroy(Request $request) {
        try {
            Borrow::deleteByEquipmentID($request->equipmentID);
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to delete from borrow table (Deliver equipment)',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }
    }

}


