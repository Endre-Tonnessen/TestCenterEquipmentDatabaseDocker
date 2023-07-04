<div>
    {{-- Adding new row --}}
    <div class="mb-2">
        <button onclick="showModalForAdding()" id="addCalibrationButton" type="button" class="w-auto btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#calibrationRangeModal"><i class="fas fa-plus"></i></button>
        <!-- <button id="addCalibrationButton" type="button" class="w-auto btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#calibrationRangeModal"><i class="fas fa-plus"></i></button> -->
    </div>

    <!-- Modal -->
    <div class="modal fade" id="calibrationRangeModal" tabindex="-1" aria-labelledby="calibrationRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ $equipmentID }}/cCal" method="POST" autocomplete="off" id="calibration_form"> <!-- Complete form, check that button can submit form. -->
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="calibrationRangeModalLabel">Add Calibration Range and Accuracy</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class='col-11'>
                            <div class='row mb-2'>
                                <label for='CalibrationRangeLower' class='col-md-3 col-form-label text-md-start'>Range Lower </label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='CalibrationRangeLower' type="number" step="any" placeholder='Lower Range' name='CalibrationRangeLower' value='' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='CalibrationRangeUpper' class='col-md-3 col-form-label text-md-start'>Range Upper</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='CalibrationRangeUpper' type="number" step="any" placeholder='Upper Range' name='CalibrationRangeUpper' value='' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='CalibrationRangeUnit' class='col-md-3 col-form-label text-md-start'>Unit</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='CalibrationRangeUnit' placeholder='Unit' name='CalibrationRangeUnit' value='' required {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='CalibrationRangeAccuracy' class='col-md-3 col-form-label text-md-start'>Accuracy</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='CalibrationRangeAccuracy' placeholder='Accuracy' name='CalibrationRangeAccuracy' value='' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='ReasonTextMeasuringModal' class='col-md-3 col-form-label text-md-start'>Reason for change</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <textarea required class='form-control' id='ReasonTextMeasuringModal' placeholder='Reason for this change' rows="3" name='ReasonText' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="CalibrationRangeAndAccuracyIDInModal" name="CalibrationRangeAndAccuracyID" value="">

                        </div>

                    </div>
                    <div class="modal-footer" id="model-buttons-close-add-delete">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
                        <!-- <button  id="deletecalibrationandaccuracybutton" onclick="deleteCalibrationRangeAndAccuracy(itemID)" class="btn btn-danger">Delete</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Deleting existing row --}}
    <form action="{{ $equipmentID }}/dMes" method="POST" id="deleteCalibrationRangeAndAccuracy" style="display: none;">
        @csrf
        <input id="CalibrationRangeAndAccuracyID" name="CalibrationRangeAndAccuracyID" value="">
        <input id="ReasonText" name="ReasonText" value="Deleted row of calibration range & accuracy.">
    </form>

    <script defer>

        function showModalForAdding() {
            // Set data for adding calibration records
            //$('#calibrationRangeModal').modal('show')
            //Add delete button to model
            document.querySelector('#model-buttons-close-add-delete').innerHTML = '' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
                '<button type="submit" class="btn btn-success">Add</button>';
            document.querySelector('#calibration_form').setAttribute("action", "{{ $equipmentID }}/cCal")
            document.querySelector('#calibrationRangeModalLabel').innerHTML = "Add Calibration Range and Accuracy"

            // Place correct field values into input fields
            document.querySelector('#CalibrationRangeUpper').setAttribute('value', "")
            document.querySelector('#CalibrationRangeLower').setAttribute('value', "")
            document.querySelector('#CalibrationRangeUnit').setAttribute('value', "")
            document.querySelector('#CalibrationRangeAccuracy').setAttribute('value', "")
        }


        /**
         * When a row in the table is clicked, user is promted if they wish to delete this item.
         * @param {*} e
         */
        function deleteCalibrationRangeAccuracyModal(event) {
            $("tr").click(function(event) {
                //Check that the clicked table is the correct one
                if ($(this).closest('tr').closest("#CalibrationRangeAndAccuracyTable").length === 0) {
                    return;
                }

                //itemID from clicked <tr>
                const calibration_itemID = $(this).closest('tr').find('td:first-child').html();
                const calibration_rangeLower = $(this).closest('tr').find('td:nth-child(2)').html();
                const calibration_rangeUpper = $(this).closest('tr').find('td:nth-child(3)').html();
                const calibration_unit = $(this).closest('tr').find('td:nth-child(4)').html();
                const calibration_accuracy = $(this).closest('tr').find('td:nth-child(5)').html();
                document.querySelector('#CalibrationRangeAndAccuracyID').setAttribute('value', calibration_itemID); // Set id in deleting form
                document.querySelector('#CalibrationRangeAndAccuracyIDInModal').setAttribute('value', calibration_itemID); // Set id in editing form

                //Add delete button to model
                document.querySelector('#model-buttons-close-add-delete').innerHTML = '' +
                    '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
                    '<button id="deletecalibrationandaccuracybutton" onclick="deleteCalibrationRangeAndAccuracy('+calibration_itemID+')" type="button" class="btn btn-danger">Delete</button>' +
                    '<button type="submit" class="btn btn-success">Edit</button>';


                if (calibration_itemID === undefined || calibration_itemID === "No data yet.") return; //Can't select title of item table.
                // Place correct field values into input fields
                document.querySelector('#CalibrationRangeUpper').setAttribute('value', calibration_rangeUpper)
                document.querySelector('#CalibrationRangeLower').setAttribute('value', calibration_rangeLower)
                document.querySelector('#CalibrationRangeUnit').setAttribute('value', calibration_unit)
                document.querySelector('#CalibrationRangeAccuracy').setAttribute('value', calibration_accuracy)


                document.querySelector('#calibration_form').setAttribute("action", "{{ $equipmentID }}/eCal")
                document.querySelector('#calibrationRangeModalLabel').innerHTML = "Edit Calibration Range and Accuracy"
                $('#calibrationRangeModal').modal('toggle')

                // When "editing" a record
                //  -> Change action of the model form to edit
                //  -> Add hidden input field named "CalibrationRangeAndAccuracyID", eg <input id="CalibrationRangeAndAccuracyID" name="CalibrationRangeAndAccuracyID" value="">

                //Comment out below function to implement edit ability. It is moved up into the larger modal.
                //deleteCalibrationRangeAndAccuracy(itemID) // To edit data, maybe call on modal above, with the adition of a delete button, calling the original one below.
            })
        }

        function deleteCalibrationRangeAndAccuracy(itemID) {
            Swal.fire({
                icon: 'question',
                title: "Do you want to delete calibration id " + parseInt(itemID) + "?",
                showDenyButton: true,
                denyButtonText: `No`,
                confirmButtonText: `Yes`,
                confirmButtonColor: 'green',
                reverseButtons: true,
                html: '',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("deleteCalibrationRangeAndAccuracy").submit();
                }
            })
        }
    </script>

</div>



