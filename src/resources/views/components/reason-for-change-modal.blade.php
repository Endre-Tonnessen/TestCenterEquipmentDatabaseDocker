<div>
    <!-- Modal -->
    <div class="modal fade" id="{{ $modalIdTarget }}" tabindex="-1" aria-labelledby="GeneralInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="GeneralInfoModalLabel">Reason for change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class='col-11'>
                        <div class='row mb-2'>
                            <label for='ReasonText' class='col-md-2 col-form-label text-md-start'>Reason</label>
                            <div class='col-md-10 d-inline-flex align-items-center'>
                                <textarea class='form-control' id='ReasonText' type="text" placeholder="Changed x information. &#10;Fixed error in x field. &#10;etc." name='ReasonText' rows="5" required {{ \Illuminate\Support\Facades\Auth::check() ? '' : 'disabled' }}></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </div>
        </div>
    </div>

</div>
