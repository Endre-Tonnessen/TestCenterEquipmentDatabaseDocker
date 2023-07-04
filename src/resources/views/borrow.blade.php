@extends('layouts/master')

@section('title', 'Borrow Equipment')


@section('content')

    <script>
        changeNavigationbarHighlight('BorrowPage');
    </script>

    {{-- If already borrowed, promt user to re-register--}}
    @if(\Illuminate\Support\Facades\Session::has('reRegister'))
        @include('modals/reRegisterModal')

        @php
            $res = \Illuminate\Support\Facades\Session::get('reRegister');
        @endphp
        <script>openModal('{{$res[0]->equipmentID}}','{{$res[0]->borrowName}}','{{$res[0]->created_at}}','{{old('borrowName')}}');</script>
    @endif

    <div class="container-fluid pt-5">
        <div class="row justify-content-center">
            <div class="col-auto m-1">
                <form action='{{url('/Borrow/b')}}' method='POST'>
                    @csrf
                    <div class="col pb-2">
                        <h1 class="text-center border-red-bottom m-1 pb-2">Borrow Equipment</h1>
                    </div>

                    <input style="background-color: rgb(244,244,244)" class="form-control mb-2" placeholder='Name' value="{{old('borrowName')}}" name='borrowName' autocomplete='off' autofocus="autofocus" id="name">
                    <div class="input-group mb-3">
                        <input  style="background-color: rgb(249, 249, 249)" class="form-control" placeholder="Equipment ID" value='{{empty($equipmentID) ? old('equipmentID') : $equipmentID}}' name='equipmentID' autocomplete='off' id="equipmentID">
                        <button class="btn btn-outline-secondary" type="submit" name="submitLoan" id="button-addon2"><i class='fa fa-search'></i></button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
