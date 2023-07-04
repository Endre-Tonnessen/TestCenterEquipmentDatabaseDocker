@extends('layouts/master')

@section('title', 'Borrow Equipment')


@section('content')

    <div class="container-fluid">
        <div class="col-3 m-1" style="height: 530px; min-width: 400px;  border: 1px solid red;">
            <div class="col-auto">
                <h1 class="text-center border-red-bottom m-1">Calibration Range & Accuracy </h1>
            </div>
            <div class="tableFixHead ScrollBottom">
                <table class=""> <!-- w-auto to fit table to content. -->
                    <thead>
                    <tr style="background-color: #EE2D24;">
                        <th scope="col">Range lower</th>
                        <th scope="col">Range upper</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Accuracy</th>
                        <th scope="col">Unit</th>
                        @if(\Illuminate\Support\Facades\Auth::check())
                            <th scope="col">Admin</th>
                        @endif
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
                            <td class="" style="">0</td>
                            <td class="">100</td>
                            <td class="">mm</td>
                            <td class="">± 0.5</td>
                            <td class="">mm</td>
                            @if(\Illuminate\Support\Facades\Auth::check())
                                <td>Admin</td>
                            @endif
                        </tr>
                    @empty
                        <td colspan="5">No rows yet.</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

<!--
style="white-space: nowrap; width: 1%"
-->
