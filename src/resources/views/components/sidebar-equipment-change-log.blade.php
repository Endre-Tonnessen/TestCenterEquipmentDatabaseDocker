<!-- History modal -->
<div class="modal fade" id="VersionHistoryModal" tabindex="-1" aria-labelledby="VersionHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="position:absolute; right: 0%; max-width: 350px; width: 200px;">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title" id="VersionHistoryModalLabel">Changelog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Link to current in-use version. -->
                <div class='col-12'>
                    <a href="{{ \Illuminate\Support\Facades\URL::to("/Equipment") }}/{{ $id }}/{{ \Carbon\Carbon::now()->addDay(1) }}" class="text-decoration-none">
                        <div class='row mb-2' style="color: {{ $versionDateTime == "" ? 'red' : "" }}">
                            <label for='' class='col-md-4 col-form-label text-md-start'><i class="fa fa-book{{ $versionDateTime == "" ? "-open" : "" }}"></i> </label>
                            <div class='col-md-8 d-inline-flex align-items-center'>
                                Current
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Older versions of equipment. -->
                @foreach($changeDates as $date)
                    <div class='col-12'>
                        <a href="{{ \Illuminate\Support\Facades\URL::to("/Equipment") }}/{{ $id }}/{{ $date->created_at }}" class="text-decoration-none">
                            <div class='row mb-2' style="color:{{ $versionDateTime == $date->created_at ? 'red' : "" }}" title="{{ $date->ReasonText . " -" . $date->name }}">
                                <label for='' class='col-md-4 col-form-label text-md-start'><i class="fa fa-book{{ $versionDateTime == $date->created_at ? "-open" : "" }}"></i> </label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    {{ $date->created_at }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach


                <!-- Older versions of equipment. -->
                {{-- Testing another way to retrieve changelog/history. This does NOT get the event deleting calibration and measuring range & accuracy rows.
                @php
                    $d = \App\Models\ReasonForChange::query()
                        ->where('equipmentID', $id)
                        ->join('users', 'reason_for_changes.UserID', '=','users.id')
                        ->orderByDesc('reason_for_changes.created_at')
                        ->select('reason_for_changes.created_at','ReasonText', 'name')
                        ->get()
                @endphp
                @foreach($d as $date)
                    <div class='col-12'>
                        <a href="{{ \Illuminate\Support\Facades\URL::to("/Equipment") }}/{{ $id }}/{{ $date->created_at }}" class="text-decoration-none">
                            <div class='row mb-2' style="color:{{ $versionDateTime == $date->created_at ? 'red' : "" }}" title="{{ $date->ReasonText . " -" . $date->name }}">
                                <label for='' class='col-md-4 col-form-label text-md-start'><i class="fa fa-book{{ $versionDateTime == $date->created_at ? "-open" : "" }}"></i> </label>
                                <div class='col-md-8 d-inline-flex align-items-center'>
                                    {{ $date->created_at }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
                --}}

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
