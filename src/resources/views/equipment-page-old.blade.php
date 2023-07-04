@extends('layouts/master')

@section('title', 'Inventory')

@section('content')
    @php
        if (empty($equipment->equipmentID)) {
            echo "<script>sweetAlert('error', '$id does not exist')</script>";
        }
    @endphp

    <div id="mainbox">
        <div class="top">
            <!-- Box for inserting new items -->
            <div class="childTopLeft">
                <div class='search-container' style="flex-direction:column; margin-left:auto;">
                    <div class='border-red-bottom' style="justify-content:center;"><h1>Info for {{$equipment->equipmentID}}</h1></div>
                    <div class='boxInsertNewItems'>
                        <form action='{{$equipment->equipmentID}}/update' method='POST' class='formTable'>
                            @if(\Illuminate\Support\Facades\Auth::check())
                                @csrf
                            @endif
                            <p>
                                <label for='itemID'>ID for equipment: </label>
                                <input readonly id='itemID' type='text' placeholder='Item ID' name='equipmentID' value="{{$equipment->equipmentID}}" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>
                            </p>
                            <p>
                                <label for='location'>Location: </label>
                                <input id='location' type='text' placeholder='Location' name='location' value="{{$equipment->location}}" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>
                            </p>
                            <p>
                                <label for='rangeLower'>Range Lower: </label>
                                <input id='rangeLower' type='text' placeholder='Range Lower' name='Range_Lower' value="{{ $equipment->Range_Lower }}" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>
                            </p>
                            <p>
                                <label for='rangeUpper'>Range Upper: </label>
                                <input id='rangeUpper' type='text' placeholder='Range Upper' name='Range_Upper' value="{{ $equipment->Range_Upper }}" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>
                            </p>
                            <p>
                                <label for='SIUnit'>SI-Unit: </label>
                                <input id='SIUnit' type='text' placeholder='SI-Unit' name='SI_Unit' value="{{$equipment->SI_Unit }}" {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>
                            </p>
                            <p>
                                <label for='Category_id'>TAG: </label>
                                <select name='Category_id' id='TAG' {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id  }}" {{ $equipment->Category_id == $cat->id ? "selected" : "" }}>{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </p>
                            <p>
                                <label for='itemDescription' style='line-height:2;'>Description: </label>
                                <textarea id='itemDescription' style='vertical-align: top;' name='Description' rows='4' cols='30' {{ \Illuminate\Support\Facades\Auth::check() ? "" : "disabled" }}>{{$equipment->Description}}</textarea>
                            </p>

                            @if(\Illuminate\Support\Facades\Auth::check())
                                <p><label for=''>Change</label><button type='submit' name='submitNewItem' style='float:none;'><i class='fa fa-search'></i></button></p>
                            @endif
                        </form>
                    </div> <!-- end of boxInsertNewItems -->
                </div> <!-- End of search-container -->
            </div>

            <!-- Image of equipment -->
            <div class="childTopRight">
                <div class='search-container' style="flex-direction:column;">
                    <div class='EquipmentImageDiv'>
                            @if(\Illuminate\Support\Facades\Auth::check())
                                @include("modals/update-equipment-image-modal", ['equipmentID' => $equipment->equipmentID])
                            @endif
                        <img class="EquipmentImage" onclick="updateEquipmentImage()" src="{{ $equipment->img_path != NULL && $equipment->img_path != "" && file_exists(public_path("storage/$equipment->img_path")) ? asset("storage/$equipment->img_path") : asset("storage/PlaceholderImage.jpg")}}" alt="Equipment Image">
                    </div> <!-- end of boxInsertNewItems -->
                </div> <!-- End of search-container -->
            </div>
        </div>

        <div class="bottom">
            {{--
            <!-- Advanced Search by SI-Unit  -->
            <div style="display:flex; justify-content: center;">
                <div style="display:flex; margin:20px; flex-direction:column;">
                    <div class='border-red-bottom' style="justify-content:center;"><h1>Measuring units</h1></div>



                </div>
            </div>
            --}}

            <!-- Log for equipment -->
            <div style="display:flex; justify-content: center;">
                <div style="display:flex; margin:20px; flex-direction:column;">
                    <div class='border-red-bottom' style="justify-content:center;"><h1>Log for <b>{{$equipment->equipmentID}}</b></h1></div>

                    <div class='tableFixHead ScrollBottom'>
                        <table class='tableFixHead'>
                            <thead>
                            <tr>
                                <th>Borrower Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Timestamp</th>
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
                                        <td style="max-width:300px;">{{ $l->person_responsible  }}</td>
                                        <td style="background: {{ $colour }};">{{ $name }}</td>
                                        <td>{{ $l->created_at->format("Y-m-d")  }}</td>
                                        <td>{{ $l->created_at->format("H:i:s")  }}</td>
                                    </tr>
                                @empty
                                    <td colspan="4">No Log yet.</td>
                                @endforelse

                            </tbody>
                        </table>
                    </div> <!-- End of resultTable-->


                </div>
            </div>

        </div>
    </div>

    <script>
        /*
            There is a bug with the onClick="", requiring the user to click two times on the item row the user wants to borrow.
            The current workaround is the line underneath. By clicking the table once before the user, the user only has to click once more.
            If removed expected reaction is the user has to click twice on the itemTable to activate the borrow modal.
             * The positioning of the onClick="" also matters. If moved in onto the table tag itself. The click bug will return.
        */
        document.getElementById("measuringUnitTableClickable").click();


@endsection
