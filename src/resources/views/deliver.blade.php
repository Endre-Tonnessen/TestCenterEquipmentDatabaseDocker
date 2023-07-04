@extends('layouts/master')

@section('title', 'Deliver Equipment')


@section('content')

    <script>
        changeNavigationbarHighlight('DeliverPage');
    </script>

    <div class="container-fluid pt-5">
        <div class="row justify-content-center">
            <div class="col-auto m-1">
                <form action='{{url('/Deliver/d')}}' method='POST'>
                    @csrf
                    <div class="col pb-2">
                        <h1 class="text-center border-red-bottom m-1 pb-2">Deliver Equipment</h1>
                    </div>

                    <div class="input-group mb-3">
                        <input id="deliverEquipmentID" class="form-control" style="background-color: rgb(244, 244, 244)" placeholder='Equipment ID' name='equipmentID' autocomplete='off' autofocus="autofocus"  value="{{empty($equipmentID) ? old('equipmentID') : $equipmentID}}">
                        <button class="btn btn-outline-secondary" type="submit" name="submitLoan" id="button-addon2"><i class='fa fa-search'></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
