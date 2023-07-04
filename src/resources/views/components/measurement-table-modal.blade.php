<div>
    {{-- Adding new row --}}
    <div class="mb-2">
        <button onclick="showModalForAddingMeasuringData()" id="addMeasuringButton" type="button" class="w-auto btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#MeasuringModal"><i class="fas fa-plus"></i></button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="MeasuringModal" tabindex="-1" aria-labelledby="measurementRangeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ $equipmentID }}/cMes" method="POST" autocomplete="off" id="measurement_form"> <!-- Complete form, check that button can submit form. -->
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="measurementRangeModalLabel">Add Measurement Range and Accuracy</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class='col-11'>
                            <div class='row mb-2'>
                                <label for='MeasurementRangeLower' class='col-md-3 col-form-label text-md-start'>Range Lower </label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='MeasurementRangeLower' type="number" placeholder='Lower Range' name='MeasurementRangeLower' value='' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='MeasurementRangeUpper' class='col-md-3 col-form-label text-md-start'>Range Upper</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='MeasurementRangeUpper' type="number" placeholder='Upper Range' name='MeasurementRangeUpper' value='' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='MeasurementRangeUnit' class='col-md-3 col-form-label text-md-start'>Unit</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='MeasurementRangeUnit' placeholder='Unit' name='MeasurementRangeUnit' value='' required {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='MeasurementRangeAccuracy' class='col-md-3 col-form-label text-md-start'>Accuracy</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <input class='form-control' id='MeasurementRangeAccuracy' placeholder='Accuracy' name='MeasurementRangeAccuracy' value='' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}>
                                </div>
                            </div>

                            <div class='row mb-2'>
                                <label for='ReasonTextMeasuringModal' class='col-md-3 col-form-label text-md-start'>Reason for change</label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    <textarea required class='form-control' id='ReasonTextMeasuringModal' placeholder='Reason for this change' rows="3" name='ReasonText' {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}></textarea>
                                </div>
                            </div>
                            <input type="hidden" id="MeasurementRangeAndAccuracyIDInModal" name="MeasurementRangeAndAccuracyID" value="">
                        </div>

                    </div>
                    <div class="modal-footer" id="model-buttons-close-add-delete-measurment">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Deleting existing row --}}
    <form action="{{ $equipmentID }}/dMes" method="POST" id="deleteMeasurementRangeAndAccuracy" style="display: none;">
        @csrf
        <input id="MeasurementRangeAndAccuracyID" name="MeasurementRangeAndAccuracyID" value="">
        <input id="ReasonText" name="ReasonText" value="Deleted row of measuring range & accuracy.">
    </form>

    <script defer>

        function showModalForAddingMeasuringData() {
            // Set data for adding measuring records
            //Add delete button to model
            document.querySelector('#model-buttons-close-add-delete-measurment').innerHTML = '' +
                '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
                '<button type="submit" class="btn btn-success">Add</button>';
            document.querySelector('#measurement_form').setAttribute("action", "{{ $equipmentID }}/cMes")
            document.querySelector('#measurementRangeModalLabel').innerHTML = "Add Measuring Range and Accuracy"

            // Place correct field values into input fields
            document.querySelector('#MeasurementRangeUpper').setAttribute('value', "")
            document.querySelector('#MeasurementRangeLower').setAttribute('value', "")
            document.querySelector('#MeasurementRangeUnit').setAttribute('value', "")
            document.querySelector('#MeasurementRangeAccuracy').setAttribute('value', "")
        }

        /**
         * When a row in the table is clicked, user is promted if they wish to delete this item.
         * @param {*} e
         */
        function deleteMeasurementRangeAccuracyModal(e) {
            $("tr").click(function(event) {
                //Check that the clicked table is the correct one
                if ($(this).closest('tr').closest("#MeasurementRangeAndAccuracyTable").length === 0) {
                    return;
                }

                //itemID from clicked <tr>
                const measurement_itemID = $(this).closest('tr').find('td:first-child').html();
                const measurement_rangeLower = $(this).closest('tr').find('td:nth-child(2)').html();
                const measurement_rangeUpper = $(this).closest('tr').find('td:nth-child(3)').html();
                const measurement_unit = $(this).closest('tr').find('td:nth-child(4)').html();
                const measurement_accuracy = $(this).closest('tr').find('td:nth-child(5)').html();
                document.querySelector('#MeasurementRangeAndAccuracyID').setAttribute('value', measurement_itemID); // Set id in deleting form
                document.querySelector('#MeasurementRangeAndAccuracyIDInModal').setAttribute('value', measurement_itemID); // Set id in editing form

                //Add delete button to model
                document.querySelector('#model-buttons-close-add-delete-measurment').innerHTML = '' +
                    '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>' +
                    '<button id="deletemeasurementandaccuracybutton" onclick="deleteMeasurementRangeAndAccuracy('+measurement_itemID+')" type="button" class="btn btn-danger">Delete</button>' +
                    '<button type="submit" class="btn btn-success">Edit</button>';

                if (measurement_itemID === undefined || measurement_itemID === "No data yet.") return; //Can't select title of item table.

                // Place correct field values into input fields
                document.querySelector('#MeasurementRangeUpper').setAttribute('value', measurement_rangeUpper)
                document.querySelector('#MeasurementRangeLower').setAttribute('value', measurement_rangeLower)
                document.querySelector('#MeasurementRangeUnit').setAttribute('value',  measurement_unit)
                document.querySelector('#MeasurementRangeAccuracy').setAttribute('value', measurement_accuracy)

                document.querySelector('#measurement_form').setAttribute("action", "{{ $equipmentID }}/eMes")
                document.querySelector('#measurementRangeModalLabel').innerHTML = "Edit Measurement Range and Accuracy"
                $('#MeasuringModal').modal('toggle')
            })
        }

        function deleteMeasurementRangeAndAccuracy(itemID) {
            Swal.fire({
                icon: 'question',
                title: 'Do you want to delete measurement id  '+itemID+'?',
                showDenyButton: true,
                denyButtonText: `No`,
                confirmButtonText: `Yes`,
                confirmButtonColor: 'green',
                reverseButtons: true,
                html: '',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("deleteMeasurementRangeAndAccuracy").submit();
                }
            })
        }
    </script>

</div>
