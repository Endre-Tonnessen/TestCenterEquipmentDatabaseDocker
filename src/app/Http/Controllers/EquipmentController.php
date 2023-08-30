<?php

namespace App\Http\Controllers;

use App\Exceptions\ArchiveEquipmentException;
use App\Exceptions\ArchiveException;
use App\Exceptions\EquipmentNotFoundException;
use App\Exceptions\FailedStoreEquipmentException;
use App\Models\Equipment;
use App\Models\Log;
use App\Models\ReasonForChange;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;

class EquipmentController extends Controller
{

    /** Create completely new Equipment record.
     *  Checks if Equipment already exists, must be unique.
     * @param Request $request
     * @return RedirectResponse
     */
   public function store(Request $request): RedirectResponse
   {
        $request->validate([
            'equipmentID' => ['required'],
            'Placement' => ['required']
        ]);

        if (Equipment::doesEquipmentExists($request->equipmentID)) return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => "Unable to create $request->equipmentID. ID already exists."]);

        $this->storeEquipment($request);

        //Redirect with success message
        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => "Created $request->equipmentID successfully",
        ]);
    }

    /**
     * Inserts data in to Equipment Table.
     *
     * @param Request $request
     * @return void
     * @throws FailedStoreEquipmentException Redirects back with error message.
     */
    public function storeEquipment(Request $request, $img_path=null, $deleted=0)
    {
        $request->validate([
            'equipmentID' => ['required'],
            'Placement' => ['required'],
            'ReasonText' => ['required'],
        ]);

        try {
            DB::transaction(function () use ($request, $img_path, $deleted){
                //Create record with justification for change
                $ReasonForChangeID = ReasonForChange::createRecord($request->equipmentID, $request->ReasonText);

                //Insert change with foreign key to reason for the change
                $equipment = new Equipment();
                $equipment->equipmentID = $request->equipmentID;
                $equipment->location = $request->location;
                $equipment->Category_id = $request->Category_id;
                $equipment->Description = $request->Description;

                $equipment->Placement = $request->Placement;
                $equipment->Department = $request->Department;
                $equipment->Serial_Number = $request->Serial_Number;
                $equipment->Model_Number = $request->Model_Number;
                $equipment->Usage = $request->Usage;
                $equipment->Manufacturer = $request->Manufacturer;
                $equipment->img_path = $img_path;
                $equipment->Deleted = $deleted;
                $equipment->ReasonForChangeID = $ReasonForChangeID;
                $equipment->save();
            });
        } catch (\Exception $e) {
            throw new FailedStoreEquipmentException();
        }
    }

    /** Updating creates a new equipment row with the updated information, and marks the previous row as archived.
     * @param $equipmentID
     * @param Request $request
     * @return RedirectResponse
     * @throws FailedStoreEquipmentException|ArchiveException|EquipmentNotFoundException
     */
    public function update($equipmentID, Request $request): RedirectResponse
    {
        $request->validate([
            'equipmentID' => ['required'],
            'Placement' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($equipmentID)) return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Failed to update!', 'text'=>"$equipmentID does not exist/could not be found."]);

        $equipment = Equipment::getEquipmentById($equipmentID);
        //Create new equipment row with updated information
        $this->storeEquipment($request, $equipment->img_path, $equipment->Deleted);
        try {
            //Mark equipment as archived by adding change date.
            $equipment->Date_Archived = Carbon::now();
            $equipment->save();
        } catch (\Exception $e) {
            throw new ArchiveException();
        }

        return Redirect::back()->with('modalResponse', ['icon' => 'success', 'title' => 'Sucessfully updated!']);
    }

    /**
     * Soft deletes equipment
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'equipmentID' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($request->equipmentID)) return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => "$request->equipmentID does not exist"]);

        try {
            $saveResult = Equipment::markEquipmentAsDeleted($request->equipmentID);
            if (!$saveResult) throw new \Exception();
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to mark as deleted in equipment table.',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }

        Log::store($request->equipmentID, Auth::user()->name, Log::DeleteActionLog);

        //Redirect with success message
        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => "Deleted successfully!",
            'html' => "<b>$request->equipmentID</b> is now deleted.",
        ]);
    }

    /**
     * Removes the soft deleting of an item
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function unDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'equipmentID' => ['required'],
        ]);

        if (!Equipment::doesEquipmentExists($request->equipmentID)) return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => "$request->equipmentID does not exist"]);

        try {
            $saveResult = Equipment::markEquipmentAsNotDeleted($request->equipmentID);
            if (!$saveResult) throw new \Exception();
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to undelete in equipment table.',
                'text' => 'If issue persists, please contact Administrator.'
            ]);
        }

        Log::store($request->equipmentID, Auth::user()->name, Log::UnDeleteActionLog);

        //Redirect with success message
        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => "Undeleted successfully!",
            'html' => "<b>$request->equipmentID</b> is now undeleted.",
        ]);
    }

    /**
     * Replaces the image
     *
     * @param $equipmentID
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateImage($equipmentID, Request $request): RedirectResponse
    {
        $request->validate([
            'equipmentID' => 'required',
            'imgFile' => 'Mimes:jpeg,jpg,png|required|image|max:30000'
        ]);

        try {
            $img_path = $request->imgFile->store('uploadedEquipmentImages','public');

            if ($request->imageFormatting == "cut") {
                $image = Image::make(public_path("storage".DIRECTORY_SEPARATOR."$img_path"))->fit(450,450);
            } else {
                $image = Image::make(public_path("storage".DIRECTORY_SEPARATOR."$img_path"))->resize(450,450);
            }
            $image->save();

            $equipment = Equipment::getEquipmentById($request->equipmentID);
            File::delete(public_path("storage/$equipment->img_path")); //Delete old image

            $equipment->img_path = $img_path;
            $equipment->save();
        } catch (\Exception $e) {
            return Redirect::back()->with('modalResponse', [
                'icon' => 'error',
                'title' => 'Failed to update image!',
                'text' => 'If issue persists, please contact an Administrator.'
            ]);
        }

        return Redirect::back()->with('modalResponse', [
            'icon' => 'success',
            'title' => "Updated image successfully!"
        ]);
    }


}
