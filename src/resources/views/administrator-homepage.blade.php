@extends('layouts/master')

@section('title', 'Administrator')


@section('content')
    <script>
        changeNavigationbarHighlight('AdministratorPage');
    </script>

    <div id="mainbox">
        <div class="top">
            <!-- Inserting new equipment -->
            <div class="childTopLeft">
                <div style="display:flex; justify-content: center;  margin-left:auto;">
                    <div style="display:flex; margin:20px; flex-direction:column;">
                        <div class='border-red-bottom' style="justify-content:center;"><h1>Insert new equipment<b></b></h1></div>

                        <!-- Box for inserting new items -->
                        <div class='search-container'>
                            <div class='boxInsertNewItems'>
                                <form action='{{ url('/Administrator/createEquipment') }}' method='POST' class='formTable'>
                                    @csrf
                                    <p>
                                        <label for='itemID'>ID for equipment: </label>
                                        <input id='itemID' type='text' placeholder='Item ID' name='equipmentID' autocomplete='off' value="{{ old('equipmentID') }}">
                                    </p>
                                    <p>
                                        <label for='Placement'>Location: </label>
                                        <input id='Placement' type='text' placeholder='Placement' name='Placement' autocomplete='off' value="{{ old('location') }}">
                                    </p>

                                    <p style="">
                                        <label for='Category_id'>Category: </label>
                                        <select name='Category_id' id='TAG' style="margin-top: 8px; margin-bottom: 8px;">
                                            @foreach($allCategories as $cat)
                                                <option value="{{ $cat->id  }}">{{ $cat->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </p>
                                    <p>
                                        <label for='itemDescription' style='line-height:2;'>Description: </label>
                                        <textarea id='itemDescription' style='vertical-align: top;' name='Description' rows='4' cols='30'>{{ (old('Description')) ? old('Description') : "Description for the item." }}</textarea>
                                    </p>
                                    <p style="display: none;">
                                        <input id='' type='text' placeholder="Reason for change" name='ReasonText' autocomplete='off' value="Created new item.">
                                    </p>

                                    <p><label for=''>Submit</label><button type='submit' name='submitNewItem' style='float:none;'><i class='fa fa-search'></i></button></p>
                                </form>
                            </div> <!-- end of boxInsertNewItems -->
                        </div> <!-- End of search-container -->

                        @if($errors->any())
                            @foreach($errors->all() as $error)
                                <div class="">{{ $error }}</div>
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>

            <!-- Mark equipment as deleted -->
            <div class="childTopRight">
                <div style="display:flex; justify-content: center;">
                    <div style="display:flex; margin:20px; flex-direction:column;">
                        <div class='border-red-bottom' style="justify-content:center;"><h1>Delete equipment<b></b></h1></div>

                        <!-- Box for deleting item -->
                        <div class='search-container'>
                            <div class='boxInsertNewItems'>
                                <form action='{{ url('/Administrator/deleteEquipment') }}' method='POST' class='formTable'>
                                    @csrf
                                    <p>
                                        <label for=''>ID for equipment: </label>
                                        <input id='deleteEquipmentID' type='text' placeholder='Item ID' name='equipmentID' autocomplete='off'>
                                    </p>
                                    <p><label for=''>Submit</label><button type='submit' id="submitDelete" name='submitDelete' style='float:none;'><i class='fa fa-search'></i></button></p>
                                </form>
                            </div> <!-- end of boxInsertNewItems -->
                        </div> <!-- End of search-container -->
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom">
            <div class="top">

                <!-- Manual backup -->
                <div class="childTopLeft">
                    <!-- Backup button -->
                    <div style="display:flex; justify-content: center; margin-left:auto;">
                        <div style="display:flex; margin:20px; flex-direction:column;">
                            <div class='border-red-bottom' style="justify-content:center;"><h1>Create backup<b></b></h1></div>
                            <div class='resultBox search-container'> <!-- Box around resulting table and title -->
                                <form action="{{ url('/Administrator/createDatabaseBackup') }}" method='POST'>
                                    @csrf
                                    <p><label for=''>New Backup </label><button type='submit' name='submitNewBackup' style='float:none;'><i class="fas fa-save"></i></button></p>
                                </form>
                            </div> <!-- End of resultBox -->
                        </div>
                    </div>
                </div>

                <!-- Download backup -->
                <div class="">
                    <!-- Backup button -->
                    <div style="display:flex; justify-content: center; margin-left:auto;">
                        <div style="display:flex; margin:20px; flex-direction:column;">
                            <div class='border-red-bottom' style="justify-content:center;"><h1>Download backup<b></b></h1></div>
                            <div class='resultBox search-container'> <!-- Box around resulting table and title -->
                                <form action="{{ url('/Administrator/downloadNewestBackup') }}" method='GET'>
                                    @csrf
                                    <p><label for=''>Download newest backup </label><button type='submit' name='downloadNewestBackup' style='float:none;'><i class="fas fa-download"></i></button></p>
                                </form>
                            </div> <!-- End of resultBox -->
                        </div>
                    </div>
                </div>

                <!-- Restore database -->
                <div class="childTopRight">
                    <!-- Backup button -->
                    <div style="display:flex; justify-content: center;">
                        <div style="display:flex; margin:20px; flex-direction:column;">
                            <div class='border-red-bottom' style="justify-content:center;"><h1>Restore from backup<b></b></h1></div>
                            <div class='resultBox search-container'> <!-- Box around resulting table and title -->

                                <form action="{{ url("/Administrator/restoreFromBackup") }}" method='POST' enctype='multipart/form-data'>
                                    @csrf
                                    <p>
                                        <label for='backupFile'>Choose backup .sql file.</label>
                                        <input id='backupFile' type='file' placeholder='.sql file of backup' name='user_input_backup_file'>
                                    </p>
                                    <p><label for=''>Restore Database </label><button type='submit' id="restoreDatabaseButton" name='submitRestoreFromBackup' style='float:none;'><i class='fa fa-search'></i></button></p>
                                </form>
                            </div> <!-- End of resultBox -->
                        </div>
                    </div>
                </div>

            </div>

            <!-- Loan table -->
            <div style="display:flex; justify-content: center;">
                <div style="display:flex; margin:20px; flex-direction:column;">
                    <!-- Everything under is for resultTable -->
                    <div class='resultBox'> <!-- Box around resulting table and title -->

                        <div class='border-red-bottom'><h1>Borrowed Equipment</h1></div>
                        <div class='tableFixHead ScrollBottom'>
                            <table class='tableFixHead'>
                                <thead>
                                    <tr>
                                        <th style="min-width: 70px">ID</th>
                                        <th>Name</th>
                                        <th>Borrowed at</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allBorrowed as $item)
                                        <tr>
                                            <td style="min-width: 70px">{{ $item->equipmentID  }}</td>
                                            <td style='min-width: 100px; max-width: 300px;'>{{ $item->borrowName  }}</td>
                                            <td style='min-width: 150px;'>{{ $item->created_at  }}</td>
                                        </tr>
                                    @endforeach

                                    {{-- Response if empty response from SQL --}}
                                    @if(count($allBorrowed) == 0)
                                        <td colspan="3">No equipment is borrowed.</td>
                                    @endif
                                </tbody>
                            </table>
                        </div> <!-- End of resultTable-->
                    </div> <!-- End of resultBox -->
                </div>
            </div>

            <!-- Deleted item table -->
            <div style="display:flex; justify-content: center;">
                <div style="display:flex; margin:20px; flex-direction:column;">
                    <div> <!-- Box around resulting table and title -->

                        <!-- Form for un-deleteing items -->
                        <div style='display:none;'>
                            <form action='{{ url('/Administrator/unDeleteEquipment') }}' method='POST'>
                                @csrf
                                <input id='unDelete' type='text' value='' name='equipmentID'>
                                <button type='submit' id='unDeleteButton' name='unDelete_Button'></button>
                            </form>
                        </div>

                        <!-- Modal asking if user wants to un-delete.  -->
                        @include('modals/unDeleteModal')
                        <!-- Title and table -->
                        <div class='border-red-bottom'><h1>Deleted Equipment</h1></div>
                        <div class='tableFixHead ScrollBottom' onClick='clickOnItem(event)'>
                            <table class='tableFixHead' id='printDeletedItemsTable'>
                                <thead>
                                    <tr>
                                        <th>ItemID</th>
                                        <th>Location</th>
                                        <th>Description</th>
                                        <th>Range Lower</th>
                                        <th>Range Upper</th>
                                        <th>SI-Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allDeleted as $item)
                                        <tr @if ($item->borrowed == 1) style="background: rgba(253,152,151,1);" @endif>
                                            <td style="min-width: 70px">{{ $item->equipmentID  }}</td>
                                            <td style='min-width: 100px; max-width: 300px;'>{{ $item->location  }}</td>
                                            <td style='min-width: 150px; max-width: 300px;'>{{ $item->Description  }}</td>
                                            <td>{{ $item->Range_Lower  }}</td>
                                            <td>{{ $item->Range_Upper  }}</td>
                                            <td>{{ $item->SI_Unit  }}</td>
                                        </tr>
                                    @endforeach

                                    {{-- Response if empty response from SQL --}}
                                    @if(count($allDeleted) == 0)
                                        <td colspan="6">No equipment is deleted.</td>
                                    @endif

                                </tbody>
                            </table>
                        </div> <!-- End of resultTable-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        /*
            There is a bug with the onClick="", requiring the user to click two times on the item row the user wants to borrow.
            The current workaround is the line underneath. By clicking the table once before the user, the user only has to click one once.
            If removed the expected reaction is the user has to click twice on the itemTable to activate the borrow modal.
            * The positioning of the onClick="" also matters. If moved in onto the table tag itself. The click bug will return.
        */
        var id = document.getElementById('printDeletedItemsTable').click();
    </script>


@endsection
