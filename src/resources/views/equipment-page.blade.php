@extends('layouts.master')

@section('title', "Equipment: " . $equipment->equipmentID)

@section('content')
    @php
        if (empty($equipment->equipmentID)) {
            echo "<script>sweetAlert('error', '$id does not exist')</script>";
        }
    @endphp

    <!-- Changelog sidebar modal -->
    <x-sidebar-equipment-change-log :versionDateTime="$versionDateTime" :changeDates="$changeDates" :id="$equipment->equipmentID"></x-sidebar-equipment-change-log>

    <div class="container-fluid mb-5" id="top-container">
                {{-- Notifies user if they are viewing an older form. --}}
                <script @if(empty($versionDateTime)) type="disabled" @else type="text/javascript" @endif>
                    Swal.fire({
                        toast: true,
                        icon: 'warning',
                        title: 'You are viewing an old version from {{ $versionDateTime }}.',
                        animation: false,
                        position: 'top-start',
                        showConfirmButton: false,
                    });
                </script>

                <div class="row justify-content-center mb-3">
                    <!-- Equipment form TODO: Change width of col. -->
                    <div class="col-xxl-6 col-xl-7  m-1" style=" min-width: 400px; /*col-lg-6 max-width: 940px;border: 1px solid red;*/">
                        <div class="container">
                            <form method="POST" action='{{$equipment->equipmentID}}/update' method='POST' autocomplete="off">
                                <div class="col-auto pb-2">
                                    <h1 class="text-center border-red-bottom m-1">Info for <b>{{$equipment->equipmentID}}</b></h1>
                                </div>

                                <!-- Status form general information -->
                                <div class="row justify-content-center pb-3">
                                    <div class="col-xl-6">
                                        <!-- Equipment ID-->
                                        <div class="row mb-2">
                                            <label for="itemID" class="col-md-4 col-form-label text-md-start">Equipment ID</label>
                                            <div class="col-md-8">
                                                <input readonly id="itemID" class="form-control" name="equipmentID" value="{{$equipment->equipmentID}}" required>
                                            </div>
                                        </div>

                                        <!-- Manufacturer -->
                                        <div class="row mb-2">
                                            <label for='Manufacturer' class="col-md-4 col-form-label text-md-start">Manufacturer </label>
                                            <div class="col-8">
                                                <input class="form-control" id='Manufacturer' type='' placeholder='Manufacturer' name='Manufacturer' value="{{$equipment->Manufacturer}}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>

                                        <!-- Model Number -->
                                        <div class="row mb-2">
                                            <label for='ModelNumber' class="col-md-4 col-form-label text-md-start">Model number </label>
                                            <div class="col-8 d-inline-flex align-items-center">
                                                <input class="form-control" id='ModelNumber' type='' placeholder='Model Number' name='Model_Number' value="{{$equipment->Model_Number}}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>

                                        <!-- Serial Number -->
                                        <div class="row mb-2">
                                            <label for='SerialNumber' class="col-md-4 col-form-label text-md-start">Serial number </label>
                                            <div class="col-8 d-inline-flex align-items-center">
                                                <input class="form-control" id='SerialNumber' type='' placeholder='Serial Number' name='Serial_Number' value="{{$equipment->Serial_Number}}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>

                                        <!-- Description -->
                                        <div class="row mb-2">
                                            <label for='itemDescription' style='line-height:2;' class="col-md-4 col-form-label text-md-start">Description: </label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id='itemDescription' style='vertical-align: top;' name='Description' rows='4' cols='30' placeholder="Short description." {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>{{$equipment->Description}}</textarea>
                                            </div>
                                        </div>

                                        @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime))
                                        @csrf
                                        <div class="row pt-2"> <!-- mb-2 -->
                                            <label for='submitUpdate' class="col-md-4 col-form-label text-md-start"></label>
                                            <div class="col-md-8">
                                                @php
                                                    $genInfoModalTarget = "GeneralInfoModal"
                                                @endphp
                                                <button id="genInfoModalTarget" type="button" class="w-auto btn btn-success " data-bs-toggle="modal" data-bs-target="#{{ $genInfoModalTarget }}">Update</button>
                                            </div>
                                        </div>
                                        <!-- Reason for change -->
                                        <x-reason-for-change-modal :modalIdTarget="$genInfoModalTarget"></x-reason-for-change-modal>
                                        @endif

                                    </div>

                                    <div class="col-xl-6"> <!-- Second row of input data. -->
                                        <!-- Department -->
                                        <div class="row mb-2">
                                            <label for='Department' class="col-md-3 col-form-label text-md-start">Department</label>
                                            <div class="col-md-8">
                                                <input class="form-control" id='Department' type='' placeholder='Department' name='Department' value="{{$equipment->Department}}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>

                                        <!-- Location -->
                                        <div class="row mb-2">
                                            <label for="location" class="col-md-3 col-form-label text-md-start">Location</label>

                                            <div class="col-md-8">
                                                <input id="location" class="form-control" name="location" value="{{$equipment->location}}" placeholder="Location" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>

                                        <!-- Placement -->
                                        <div class="row mb-2">
                                            <label for="Placement" class="col-md-3 col-form-label text-md-start">Placement</label>

                                            <div class="col-md-8">
                                                <input id="Placement" class="form-control @error('required') is-invalid @enderror" name="Placement" value="{{$equipment->Placement}}" placeholder="Placement" required {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>

                                                @error('required')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Usage -->
                                        <div class="row mb-2">
                                            <label for='Usage' class="col-md-3 col-form-label text-md-start">Used for </label>
                                            <div class="col-md-8 d-inline-flex align-items-center">
                                                <input class="form-control" id='Usage' placeholder='Usage' name='Usage' value="{{$equipment->Usage}}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>

                                        <!-- TAG -->
                                        <div class="row mb-2">
                                            <label for='Category_id' class="col-md-3 col-form-label text-md-start">TAG</label>
                                            <div class="col-md-8">
                                                <select class="form-select" style="width: auto" name='Category_id' id='TAG' {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                                    @foreach($allCategories as $cat)
                                                        <option value="{{ $cat->id  }}" {{ $equipment->Category_id == $cat->id ? "selected" : "" }}>{{ $cat->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Helpful buttons. -->
                                        <div class="row mb-2">
                                            <label for='Usage' class="col-md-3 col-form-label text-md-start"></label>
                                            <div class="col-md-8 pt-3">
                                                <a href="{{ \Illuminate\Support\Facades\URL::to('/') }}/Borrow?itemID={{ $equipment->equipmentID }}">
                                                    <button class="w-auto btn btn-info" type="button">Borrow</button>
                                                </a>
                                                <a href="{{ \Illuminate\Support\Facades\URL::to('/') }}/Deliver?itemID={{ $equipment->equipmentID }}">
                                                    <button class="w-auto btn btn-info ms-2" type="button">Deliver</button>
                                                </a>
                                            </div>

                                            <?php
                                            /*
                                                @if(\Illuminate\Support\Facades\Auth::check())
                                                @csrf
                                                <!-- //TODO: Finish possible admin panel. Add button for deleting.
                                                <div class="col-md-11 offset-1 p-3 mt-4" style="border: 1px solid red;">
                                                    <p class="text-md-center">Admin panel</p>

                                                    <div class="row pt-2">
                                                        <button class="w-auto btn btn-success ms-2" type='submit' name='submitNewItem' style='float:none;'>Update</button>
                                                        <button class="w-auto btn btn-info ms-2" type="button">Calibration</button>

                                                    </div>
                                                </div>
                                                -->
                                                @endif
                                            */
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Equipment Image -->
                    <div class="col-xxl-3 col-xl-3 m-1" style="min-width: 400px;  /*border: 1px solid red;*/;">
                        <div class="row">
                            <div class="col">
                                @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime))
                                    @include("modals.update-equipment-image-modal", ['equipmentID' => $equipment->equipmentID]) 
                                @endif
                                <img class="EquipmentImage img-fluid" onclick="updateEquipmentImage()" src="{{ $equipment->img_path != NULL && $equipment->img_path != "" && file_exists(public_path("storage/$equipment->img_path")) ? asset("storage/$equipment->img_path") : asset("storage/PlaceholderImage.jpg")}}" alt="Equipment Image">
                            </div> 
                        </div>
                    </div>
                </div> <!-- End of first box -->

                <div class="row justify-content-center mt-3">
                    <!-- Calibration and Measuring Range & Accuracy -->
                    <div class="col-xxl-4 col-xl-4 m-1" style=" min-width: 400px; /*border: 1px solid red;*/">
                        <!-- Navbar -->
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Calibration Range & Accuracy</button>
                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false" title="Information related to non conformance assessments of test instruments.">Additional information</button>
                            </div>
                        </nav>

                        <!-- Tab navbar area -->
                        <div class="tab-content" id="nav-tabContent">
                            <!-- Measuring Area -->
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="col-auto">
                                    <h1 class="text-center border-red-bottom m-1">Measuring Range & Accuracy </h1>
                                </div>
                                <div class="tableFixHead ScrollBottom mb-2" style="max-height: 300px">
                                    <table class="" id="MeasurementRangeAndAccuracyTable" @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime)) onclick="deleteMeasurementRangeAccuracyModal(event)" @endif> <!-- w-auto to fit table to content. -->
                                        <thead>
                                        <tr style="background-color: #EE2D24;">
                                            <th scope="col" style=" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "display: none" }}">
                                                ID
                                            </th>
                                            <th scope="col">Range lower</th>
                                            <th scope="col">Range upper</th>
                                            <th scope="col">Unit</th>
                                            <th scope="col">Accuracy</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($measurementRangeAndAccuracy as $m)
                                            <tr>
                                                <td style=" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "display: none" }}">{{ $m->id }}</td>
                                                <td class="">{{ $m->Range_Lower }}</td>
                                                <td class="">{{ $m->Range_Upper }}</td>
                                                <td class="">{{ $m->SI_Unit }}</td>
                                                <td class="">{{ $m->Accuracy }}</td>
                                            </tr>
                                        @empty
                                            <td colspan="5" class="text-md-center">No data yet.</td>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime))
                                    <x-measurement-table-modal :equipmentID="$id"></x-measurement-table-modal>
                                @endif

                            </div>

                            <!-- Calibration Area -->
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="col-auto">
                                    <h1 class="text-center border-red-bottom m-1">Calibration Range & Accuracy </h1>
                                </div>
                                <div class="tableFixHead mb-2" style="max-height: 300px">
                                    <table class="" id="CalibrationRangeAndAccuracyTable" @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime)) onClick="deleteCalibrationRangeAccuracyModal(event)" @endif> <!-- w-auto to fit table to content. -->
                                    {{-- <table class="" id="CalibrationRangeAndAccuracyTable" @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime)) onClick="deleteCalibrationRangeAccuracyModal(event)" @endif> --}} <!-- w-auto to fit table to content. -->
                                        <thead>
                                        <tr style="background-color: #EE2D24;">
                                            <th scope="col" style=" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "display: none" }}">
                                           {{--  <th scope="col" style=" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "display: none" }}"> --}}
                                                ID
                                            </th>
                                            <th scope="col">Range lower</th>
                                            <th scope="col">Range upper</th>
                                            <th scope="col">Unit</th>
                                            <th scope="col">Accuracy</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($calibrationRangeAndAccuracy as $c)
                                            <tr>
                                                {{-- <td style=" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "display: none" }}"> --}}
                                                <td style=" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "display: none" }}">
                                                    {{ $c->id }}
                                                </td>
                                                <td class="" style="">{{ $c->Range_Lower }}</td>
                                                <td class="">{{ $c->Range_Upper }}</td>
                                                <td class="">{{ $c->SI_Unit }}</td>
                                                <td class="">{{ $c->Accuracy }}</td>
                                            </tr>
                                        @empty
                                            <td colspan="5" class="text-md-center">No data yet.</td>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime))
                                    <x-calibration-table-modal :equipmentID="$id"></x-calibration-table-modal>
                                @endif

                            </div>
                        </div>
                    </div>

                    <!-- Calibration information -->
                    <div class="col-xxl-3 col-xl-3 m-1" style=" min-width: 400px; border: 1px solid red;">
                        <div class="col-auto">
                            <h1 class="text-center border-red-bottom m-1">Calibration Frequency</h1>
                        </div>

                        <div class="row mt-4">
                            <form method="POST" action='{{$equipment->equipmentID}}/cCalFreq' method='POST' autocomplete="off">
                                <div class="col-xxl-12">
                                    <!-- Calibration interval TODO: Make input group the same length as rest of form. -->
                                    <input style="display: none;" name="CalibrationFrequencyId" value="{{ $calibrationFrequency->id }}">
                                    <div class="row mb-2" style="max-width: 250px">
                                        <label for='Cal_Interval_Year' class="col-md-auto col-form-label text-md-start">Calibration Interval </label>
                                        <div class="input-group d-inline-flex align-items-center">

                                            <input class="form-control" id='Cal_Interval_Year' name='Cal_Interval_Year' placeholder="1" style="max-height: 36px;" value="{{ $calibrationFrequency->Cal_Interval_Year }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            <span class="input-group-text" style="max-height: 36px">Year</span>

                                            <input class="form-control" id='Cal_Interval_Month' name='Cal_Interval_Month' placeholder="0" style="max-height: 36px" value="{{ $calibrationFrequency->Cal_Interval_Month }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            <span class="input-group-text" style="max-height: 36px">Month</span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-auto">
                                            <div class="col-xl-auto">
                                                <!-- Last calibration -->
                                                <label for='Last_Calibration_Date' class="col-md-auto col-form-label text-md-start">Last Calibration</label>
                                                <div class="row mb-2">
                                                    <div class="col-md-8 w-auto">
                                                        <input class="form-control" id='Last_Calibration_Date' type='date' name='Last_Calibration_Date' placeholder="dd-mm-yyyy" value="{{ $calibrationFrequency->Last_Calibration_Date }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-auto">
                                                <!-- Next calibration -->
                                                <label class="col-md-auto col-form-label text-md-start">Next Calibration</label>
                                                <div class="row mb-2">
                                                    <div class="col-md-8 w-auto">
                                                        <input id="Next_Calibration_Date" class="form-control" type='date' placeholder="dd-mm-yyyy" value="{{ $calibrationFrequency->Last_Calibration_Date && $calibrationFrequency->Cal_Interval_Year && $calibrationFrequency->Cal_Interval_Year ? \Carbon\Carbon::make($calibrationFrequency->Last_Calibration_Date)->addYear($calibrationFrequency->Cal_Interval_Year)->addMonth($calibrationFrequency->Cal_Interval_Month)->toDateString() : ""}}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xl">
                                            <div class="row">
                                                <div class="col-xl-auto">
                                                    <!-- Last calibration -->
                                                    <label for='Calibration_Provider' class="col-md-auto col-form-label text-md-start">Calibration Provider</label>
                                                    <div class="row mb-2">
                                                        <div class="col-md-8 w-auto">
                                                            <input class="form-control" id='Calibration_Provider' type='' name='Calibration_Provider' placeholder="" value="{{ $calibrationFrequency->Calibration_Provider }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-xl-auto">
                                                    <!-- Last calibration -->
                                                    <label for='Calibration_location' class="col-md-auto col-form-label text-md-start">Calibration Location</label>
                                                    <div class="row mb-2">
                                                        <div class="col-md-8 w-auto">
                                                            <input class="form-control" id='Calibration_location' type='' name='Calibration_location' placeholder="" value="{{ $calibrationFrequency->Calibration_location }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl">
                                        <!-- Document Reference -->
                                        <label for='Document_Reference' class="col-md-auto col-form-label text-md-start">Document Reference</label>
                                        <div class="row mb-2">
                                            <div class="col-md-8 w-auto">
                                                <input class="form-control" id='Document_Reference' type='' name='Document_Reference' placeholder="" value="{{ $calibrationFrequency->Document_Reference }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>
                                            </div>
                                        </div>
                                    </div>

                                    @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime)) <!-- TODO: Style button. Make appear on the end of box, either left or right. -->
                                    @csrf
                                    <div class="row my-2"> <!-- mb-2 -->
                                        <!-- <label for='submitNewItem' class="col-md-3 col-form-label text-md-start"></label> -->
                                        <div class="col-md-8">
                                            @php
                                                $calFreqModelTarget = "calFreqModelTarget"
                                            @endphp
                                            <!-- <button id="updateCalInfo" class="w-auto btn btn-success" type='submit' name='submitNewItem' style='float:none;'>Update</button> -->
                                            <button id="updateCalInfo" type="button" class="w-auto btn btn-success " data-bs-toggle="modal" data-bs-target="#{{ $calFreqModelTarget }}">Update</button>
                                            <!-- Reason for change modal -->
                                            <x-reason-for-change-modal :modalIdTarget="$calFreqModelTarget"></x-reason-for-change-modal>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </form>

                            {{--
                            <!--
                            <div class="col-xxl-6">
                                <div class="row-12 tableFixHead ScrollBottom mb-3" style="max-height: 200px;">
                                    <table>
                                        <thead>
                                        <tr style="background-color: #EE2D24;">
                                            <th class="text-center" scope="col">Calibration dates</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($log as $l)
                                            @php
                                                $logLevel = $l->action;
                                                $actionNameAndColour = \App\Models\Log::getActionLogName($logLevel);
                                                $name = $actionNameAndColour["name"];
                                                $colour = $actionNameAndColour["colour"];
                                            @endphp

                                            <tr>
                                                <td class="col-xl-4" style="max-width:100px;">2021/03/03</td>
                                            </tr>
                                        @empty
                                            <td colspan="1">No Log yet.</td>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div> -->
                            --}}
                        </div>
                    </div>

                    <!-- Misc information -->
                    <div class="col-xxl-2 col-xl-3 m-1" style=" min-width: 200px;  /*border: 1px solid red;*/">
                        <div class="col-auto">
                            <h1 class="text-center border-red-bottom m-1">Misc</h1>
                        </div>

                        <div class="card card-body mt-2 " style="max-height: 300px">
                            <label for="" class="col-md-auto col-form-label align-items-center d-flex justify-content-center"><h3 class="" style="color: black; font-size: 15px;">{{ $versionDateTime == "" ? "Latest Change" : "Change on " . $versionDateTime }}</h3></label>
                            <p class="tableFixHead">
                                @php
                                    if ($versionDateTime == "") {
                                        echo "User: " . $changeDates[0]->name . " <br>Comment: <br>" . $changeDates[0]->ReasonText;
                                    } else {
                                        $changeDates->filter(function ($item) use ($versionDateTime) {
                                            if ($item->created_at == $versionDateTime) {
                                                echo "User: " . $item->name . " <br>Comment: <br>" . $item->ReasonText;
                                            }
                                        });
                                    }
                                @endphp
                            </p>
                        </div>

                        <div class="row">
                            <!-- Export information -->
                            <div class="row mb-2 mt-4">
                                <label for="" class="col-md-auto col-form-label text-md-start">Export to Excel</label>
                                <div class="col-md-auto">
                                    <form action="{{ $versionDateTime != "" ? $versionDateTime : $equipment->equipmentID }}/excel" method="get"> <!-- //TODO: Fix excel export of statusform for older versions of status forms. Problem with path? -->
                                        <button type="submit" class="btn btn-info"><i class="fas fa-download"></i></button>
                                    </form>
                                </div>
                            </div>

                            <!-- History sidebar button -->
                            <div class="row">
                                <label for="ChangeLogButton" class="col-md-auto col-form-label ">Changelog</label>
                                <div class="col-md-auto">
                                    <button id="ChangeLogButton" type="button" class="w-auto btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#VersionHistoryModal"><i class="fas fa-plus"> History</i></button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div> <!-- End of second box -->

                <div class="row justify-content-center">
                    <!-- Notes -->
                    <div class="col-xxl-5 col-xl-5 m-1 pb-2" style="min-width: 400px; /*border: 1px solid red;*/">
                        <div class="col-auto pb-2">
                            <h1 class="text-center border-red-bottom m-1">Additional notes</h1>
                        </div>

                        <form method="POST" action='{{$equipment->equipmentID}}/cNotes' autocomplete="off">
                            <div class="col-12">
                                <textarea class="form-control" id='notes' style='vertical-align: top;' name='notes' rows='16' {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime) ? "" : "disabled" }}>@if($notes != null) {{ $notes->notes }} @endif</textarea>
                            </div>
                            @if(\Illuminate\Support\Facades\Auth::check() && empty($versionDateTime))
                            @csrf
                            <div class="row pt-2"> <!-- mb-2 -->
                                <div class="col-md-8 ">
                                    <!-- <button id="updateNotes" class="w-auto btn btn-success" type='submit' name='submitNewItem' style='float:none;'>Update</button> -->
                                    @php
                                        $NotesModalTarget = "notesModal"
                                    @endphp
                                    <button id="updateNotes" type="button" class="w-auto btn btn-success " data-bs-toggle="modal" data-bs-target="#{{ $NotesModalTarget }}">Update</button>
                                </div>
                                <!-- Reason for change -->
                                <x-reason-for-change-modal :modalIdTarget="$NotesModalTarget"></x-reason-for-change-modal>
                            </div>
                            @endif
                        </form>
                    </div>

                    <!-- Documents -->
                    <!--
                    <div class="col-4 m-1" style="min-width: 400px;  border: 1px solid red;">Documents
                    </div>
                    -->

                    <!-- Log for equipment -->
                    <div class="col-xxl-4 col-xl-5 mt-1 mb-5" style="min-width: 400px; /*border: 1px solid red;*/">
                        <div class="row-10">
                            <h1 class="text-center border-red-bottom m-1">Log for <b>{{$equipment->equipmentID}}</b></h1>
                        </div>
                        <div class="row-10 tableFixHead ScrollBottom mb-3">
                            <table>
                                <thead>
                                    <tr style="background-color: #EE2D24;">
                                        <th scope="col">Borrower Name</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($log as $l)
                                    @php
                                        $logLevel = $l->action;
                                        $actionNameAndColour = \App\Models\Log::getActionLogName($logLevel);
                                        $name = $actionNameAndColour["name"];
                                        $colour = $actionNameAndColour["colour"];
                                    @endphp

                                    <tr>
                                        <td class="col-xl-4" style="max-width:300px;">{{ $l->person_responsible  }}</td>
                                        <td class="" style="background: {{ $colour }};">{{ $name }}</td>
                                        <td class="">{{ $l->created_at->format("Y-m-d")  }}</td>
                                        <td class="">{{ $l->created_at->format("H:i:s")  }}</td>
                                    </tr>
                                @empty
                                    <td colspan="4">No Log yet.</td>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>



    <script defer>
        /*
            There is a bug with the onClick="", requiring the user to click two times on the item row the user wants to borrow.
            The current workaround is the line underneath. By clicking the table once before the user, the user only has to click once more.
            If removed expected reaction is the user has to click twice on the itemTable to activate the borrow modal.
             * The positioning of the onClick="" also matters. If moved in onto the table tag itself. The click bug will return.
        */
        document.getElementById("MeasurementRangeAndAccuracyTable").click();
        document.getElementById("CalibrationRangeAndAccuracyTable").click();
    </script>

    <!--
    <div class="row mb-2">
        <label for='rangeUpper' class="col-md-2 col-form-label text-md-start">Range Upper: </label>
        <div class="col-md-7">
            <input class="form-control" id='rangeUpper' type='' placeholder='Range Upper' name='Range_Upper' value="{{ $equipment->Range_Upper }}" {{ \Illuminate\Support\Facades\Auth::check() && empty($versionDateTime)? "" : "disabled" }}>
        </div>
    </div>
    -->
@endsection

