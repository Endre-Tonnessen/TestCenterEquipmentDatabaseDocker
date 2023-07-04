@extends('layouts/master')

@section('title', 'Inventory')

@section('content')


<div class='mainBox'>
    <div class='centeredBox' style='align-items: flex-start;'>
        <div class='optionBox'>
            <div class='search-container border-red-bottom'>
                <form action='{{url('/search')}}' method='POST' autocomplete="off">
                    @csrf
                    <h3>Custom search for equipment: </h3>
                        <select name='selectSearch' id='selectSearchType' style="height: 38px;">
                            <option value="multiSearch">All Fields</option>
                            <option value='equipmentID'>ID</option>
                            <option value='Description'>Description</option>
                            <option value='Placement'>Location</option>
                            <option value='SI_Unit'>SI-Unit</option>
                        </select>
                        <input type='text' placeholder='Search..' name='user_input' autocomplete='off' autofocus="autofocus" onFocus="this.select()">
                        <button type='submit' id="sendData" name='submit' style="margin-top: 9px"><i class='fa fa-search'></i></button>

                   <!--
                    <div class="container" style="display: none;">
                        <div class="input-group">
                            <select class="input-group form-select" name='selectSearch' id='selectSearchType'>
                                <option value="multiSearch">All Fields</option>'
                                <option value='equipmentID'>ID</option>
                                <option value='Description'>Description</option>
                                <option value='location'>Location</option>
                                <option value='SI-Unit'>SI-Unit</option>
                            </select>
                            <input class="form-control" type='text' placeholder='Search..' name='user_input' autocomplete='off' autofocus="autofocus" onFocus="this.select()">
                            <button class="form-control " type='submit' id="sendData" name='submit' style="margin-top: 9px"><i class='fa fa-search'></i></button>
                        </div>
                    </div>
                   -->
                </form>
            </div> <!-- End of search-container -->

            <div class='orderByCategory'>
                <h3>Sort by category</h3>
                <form action='{{ url('/searchTag') }}' method='POST'>
                    @csrf

                    @foreach($categories as $cat)
                        <button id="cat{{$cat->id}}" type='submit' name='tag' value='{{ $cat->id  }}' class='orderByCat'>{{ $cat->category_name }}</button>
                    @endforeach
                </form>
            </div>
        </div> <!-- End of optionBox -->


        <div class='resultBox' id='autoLoadedTable' onClick='clickOnItem(event)'> <!-- Box around resulting table and title -->
            @include('modals/borrowModal')

            <div class='border-red-bottom'><h1>{{ $searchTitle }}</h1></div>
            <div id='itemTable' style='max-width:60%' onclick="clickOnItem(event)">
                <form action='' method='get'>
                    <table id="tableStyle1">
                        <thead>
                            <tr>
                                <th style="min-width: 70px">ID</th>
                                <th>Location</th>
                                <th>Description</th>
                                <th>Range Lower</th>
                                <th>Range Upper</th>
                                <th style="min-width: 60px;">SI-Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($equipment as $item)
                                <tr @if ($item->borrowed == 1) style="background: rgba(253,152,151,1);" @endif>
                                    <td style="min-width: 70px">{{ $item->equipmentID  }}</td>
                                    <td style='min-width: 100px;'>{{ $item->Placement  }}</td>
                                    <td style='min-width: 150px; max-width: 250px'>{{ $item->Description  }}</td>
                                    @php
                                        if ($item->SI_Unit) {
                                            echo "<td> $item->Range_Lower </td>
                                                  <td> $item->Range_Upper </td>
                                                  <td> $item->SI_Unit </td>";
                                        } else {
                                            echo "<td colspan='3'>No data</td>";
                                        }
                                    @endphp
                                </tr>
                            @endforeach

                            {{-- Response if empty response from SQL --}}
                            @if(count($equipment) == 0)
                                <td colspan="6">No Match</td>
                            @endif

                            {{-- If custom search, highlight all matching substrings. --}}
                            @if(!empty($highlightText))
                                <script>
                                    //Appends class="highlight" to any table td element matching the argument
                                    function highlightText(textToHighlight) {
                                        $('#tableStyle1 tr').each(function(){
                                            $(this).find('td').each(function(){
                                                $(this).highlight('{{$highlightText}}');
                                            })
                                        })
                                    }
                                    highlightText('$user_input')
                                </script>
                            @endif
                        </tbody>
                    </table>
                </form>
        </div> <!-- End of resultBox -->
    </div> <!-- centeredBox-->
</div> <!-- MainBox-->


<script>
    /*
        There is a bug with the onClick="", requiring the user to click two times on the item row the user wants to borrow.
        The current workaround is the line underneath. By clicking the table once before the user, the user only has to click once more.
        If removed expected reaction is the user has to click twice on the itemTable to activate the borrow modal.
         * The positioning of the onClick="" also matters. If moved in onto the table tag itself. The click bug will return.
    */
    document.getElementById("itemTable").click();
</script>

<!-- Page auto refreshes every 1 hour if no mouse movement has occurred. Prevents Laravel Page Error 419.-->
<script defer>
    var timer = null;
    function goAway() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            window.location.reload(true);
        }, 3600000); //1 Hours
    }

    window.addEventListener('mousemove', goAway, true);
    goAway();  // start the first timer off

</script>

@endsection
