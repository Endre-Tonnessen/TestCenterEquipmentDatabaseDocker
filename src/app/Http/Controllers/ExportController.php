<?php

namespace App\Http\Controllers;

use App\Exceptions\EquipmentNotFoundException;
use App\Exceptions\FailedCreatingExcelStatusForm;
use App\Exceptions\MeasuringRangeAccuracyException;
use App\Models\CalibrationFrequency;
use App\Models\CalibrationRangeAndAccuracy;
use App\Models\ChangeDate;
use App\Models\Equipment;
use App\Models\EquipmentNote;
use App\Models\MeasuringRangeAndAccuracy;
use App\Models\ReasonForChange;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Nette\Utils\Json;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ExportController extends Controller
{

    /**
     * Creates and exports an Excel file with the newest statusform information.
     *
     * @throws EquipmentNotFoundException
     * @throws FailedCreatingExcelStatusForm
     * @throws MeasuringRangeAccuracyException
     */
    public function statusFormExcel(Request $request, $id) {
        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        $equipment = Equipment::getEquipmentById($id);
        $calibrationRangeAndAccuracy = CalibrationRangeAndAccuracy::getEquipmentCalibrationAndAccuracy($id);
        $measurementRangeAndAccuracy = MeasuringRangeAndAccuracy::getEquipmentMeasurementAndAccuracy($id);
        $calibrationFrequency = CalibrationFrequency::getCalibrationFrequency($id);
        $notes = EquipmentNote::getEquipmentNoteByEquipmentId($id);
        //$ReasonForChanges = ChangeDate::getDatesChanged($id);
        $ReasonForChanges = \App\Models\ReasonForChange::query()
            ->where('equipmentID', $id)
            ->join('users', 'reason_for_changes.UserID', '=','users.id')
            ->orderByDesc('reason_for_changes.created_at')
            ->select('reason_for_changes.created_at','ReasonText', 'name')
            ->get();

        $this->createExcel($equipment, $calibrationRangeAndAccuracy, $measurementRangeAndAccuracy, $calibrationFrequency, $notes, $ReasonForChanges[0]->ReasonText);
    }

    /**
     * Creates and exports an Excel file with the statusform information on the given date.
     *
     * @throws EquipmentNotFoundException
     * @throws FailedCreatingExcelStatusForm
     */
    public function statusFormExcelDate(Request $request, $id, $versionDateTime) {
        if (!Equipment::doesEquipmentExists($id)) {
            return Redirect::back()->with('modalResponse', ['icon' => 'error', 'title' => 'Unable to find equipment. Contact administrator if issue persists.']);
        }

        $equipment = Equipment::getEquipmentByIdAndDate($id, $versionDateTime);
        $calibrationRangeAndAccuracy = CalibrationRangeAndAccuracy::getEquipmentCalibrationAndAccuracyByDate($id, $versionDateTime);
        $measurementRangeAndAccuracy = MeasuringRangeAndAccuracy::getEquipmentMeasurementAndAccuracyByDate($id, $versionDateTime);
        $calibrationFrequency = CalibrationFrequency::getCalibrationFrequencyByDate($id, $versionDateTime);
        $notes = EquipmentNote::getEquipmentNoteByDate($id, $versionDateTime);

        //$ReasonForChanges = ChangeDate::getDatesChanged($id);
        $ReasonForChanges = \App\Models\ReasonForChange::query()
            ->where('equipmentID', $id)
            ->join('users', 'reason_for_changes.UserID', '=','users.id')
            ->orderByDesc('reason_for_changes.created_at')
            ->select('reason_for_changes.created_at','ReasonText', 'name')
            ->get();
        $ReasonForChanges = $ReasonForChanges->first(function ($value, $key) use ($versionDateTime){
            return $value->created_at == $versionDateTime; //Returns first change with date equal to requested version date
        }, json_decode(Collect(['ReasonText' => "Error: Unable to find reason for change on date:" . $versionDateTime])->toJson()));

        $this->createExcel($equipment, $calibrationRangeAndAccuracy, $measurementRangeAndAccuracy, $calibrationFrequency, $notes, $ReasonForChanges->ReasonText);
    }

    /**
     * Creates an Excel sheet from the template.
     *
     * @throws FailedCreatingExcelStatusForm
     */
    public function createExcel($equipment,
                                $calibrationRangeAndAccuracy,
                                $measurementRangeAndAccuracy,
                                $calibrationFrequency,
                                $notes,
                                $ReasonForChanges
    ) {
        try {
            //Open template
            $spreadsheet = IOFactory::load(storage_path("app\public\StatusFormTemp.xlsx"));
            $worksheet = $spreadsheet->getActiveSheet();

            //Reason for change
            $worksheet->getCell('I11')->setValue($ReasonForChanges)->getStyle()->getAlignment()->setWrapText(true);
            //Notes/More reason for change
            $worksheet->getCell('A40')->setValue($notes == null ? "" : $notes->notes)->getStyle()->getAlignment()->setWrapText(true);
            //Date
            $worksheet->getCell('D48')->setValue(Carbon::now()->toDateString())->getStyle()->getFont()->setSize(10);

            //General info
            $worksheet->getCell('A7')->setValue($equipment->equipmentID);
            $worksheet->getCell('J7')->setValue($equipment->Department);
            $worksheet->getCell('N7')->setValue($equipment->location);
            $worksheet->getCell('A9')->setValue($equipment->Description);
            $worksheet->getCell('J9')->setValue($equipment->Usage);
            $worksheet->getCell('A11')->setValue($equipment->Manufacturer);
            $worksheet->getCell('A15')->setValue($equipment->Model_Number);
            $worksheet->getCell('A19')->setValue($equipment->Serial_Number);

            //Calibration Range & Accuracy
            $worksheet->getCell('G23')->setValue($calibrationFrequency->Cal_Interval_Year);
            $worksheet->getCell('I23')->setValue($calibrationFrequency->Cal_Interval_Month);
            $calRange = "";
            $calAccuracy = "";
            foreach ($calibrationRangeAndAccuracy as $calRA) {
                $calRange .= $calRA->Range_Lower . "-" . $calRA->Range_Upper . " " . $calRA->SI_Unit . ", ";
                $calAccuracy .= $calRA->Accuracy . ", ";
            }
            $calRange = substr_replace($calRange,"", -2);
            $calAccuracy = substr_replace($calAccuracy,"", -2);
            $worksheet->getCell('G24')->setValue($calRange)->getStyle()->getFont()->setSize(8);
            $worksheet->getCell('G28')->setValue($calAccuracy)->getStyle()->getFont()->setSize(8);
            //Measuring Range & Accuracy
            $mesRange = "";
            $mesAccuracy = "";
            foreach ($measurementRangeAndAccuracy as $mesRA) {
                $mesRange .= $mesRA->Range_Lower . "-" . $mesRA->Range_Upper . " " . $mesRA->SI_Unit . ", ";
                $mesAccuracy .= $mesRA->Accuracy . ", ";
            }
            $mesRange = substr_replace($mesRange,"", -2); //Remove last ", "
            $mesAccuracy = substr_replace($mesAccuracy,"", -2);
            $worksheet->getCell('G26')->setValue($mesRange)->getStyle()->getFont()->setSize(8);
            $worksheet->getCell('G30')->setValue($mesAccuracy)->getStyle()->getFont()->setSize(8);
            //Calibration Frequency
            $worksheet->getCell('G32')->setValue($calibrationFrequency->Calibration_location);
            $worksheet->getCell('G33')->setValue($calibrationFrequency->Calibration_Provider);
            $worksheet->getCell('G34')->setValue($calibrationFrequency->Document_Reference);

            $worksheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $writer = IOFactory::createWriter($spreadsheet, 'Xls'); //For pdf convert, writer-type: Dompdf
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="'.$equipment->equipmentID . "S-" . Carbon::now()->format('Y').'.xls"');
            $writer->save('php://output'); //save(storage_path("app\public\\test.xls"));
        } catch (\Exception $e) {
            throw new FailedCreatingExcelStatusForm();
        }
    }
}
