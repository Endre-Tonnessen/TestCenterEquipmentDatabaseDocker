<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap/app.css') }}">
    <!-- JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>

    @yield('head')

    <title>@yield('title')</title>
</head>
<body>

    <script>
        function sweetAlertModal(icon, title, text="", html="", confirmedJavascript="") {
            Swal.fire({
                icon: icon,
                title: title,
                text: text,
                html: html,
                confirmButtonColor: 'rgb(126 191 89 / 81%)',
            }).then((result) => {
                if (result.isConfirmed) {
                    confirmedJavascript
                }
            })
        }

        function sweetAlertToast(icon, title, position) {
            Swal.fire({
                toast: true,
                icon: icon,
                title: title,
                animation: true,
                position: position,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    </script>

    @php
        if(\Illuminate\Support\Facades\Session::has('modalResponse')) {
            $data = \Illuminate\Support\Facades\Session::get('modalResponse');
            $icon = $data['icon'];
            $title = $data['title'];

            $text = "";
            if (array_key_exists('text', $data)) {
                $text = $data['text'];
            }
            $html = "";
            if (array_key_exists('html',$data)) {
                $html = $data['html'];
            }
            $conJava = "";
            if (array_key_exists('confirmedJavascript', $data)) {
                $conJava = $data['confirmedJavascript'];
            }
            echo "<script>sweetAlertModal('$icon','$title','$text','$html', $conJava);</script>";
        }
        elseif (\Illuminate\Support\Facades\Session::has('toastResponse')) {
            $toastData = \Illuminate\Support\Facades\Session::get('toastResponse');
            $toastIcon = $toastData['icon'];
            $toastTitle = $toastData['title'];
            $toastPosition = (array_key_exists('position', $toastData)) ? $toastData['position'] : 'top-middle';
            echo "<script>sweetAlertToast('$toastIcon','$toastTitle','$toastPosition')</script>";
        }
    @endphp

<div>
    <!-- Login/Register links -->
    @include('layouts/loginRegisterBar')

    <!-- Header Image -->
    <div>
        <div class="Header">
            <div class="Head">
                <div class="centerImage">
                    <a href="{{url('/')}}">
                        <img class="imageCenter" src="{{asset('images/Laerdal_Logo_Small.jpg')}}">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <div class="">
        <ul class="optionBar">
            <li><a id="FindEquipmentPage" class="active" href="{{url('/')}}">Find Equipment</a></li>
            <li><a id="BorrowPage" href="{{url('/Borrow')}}">Borrow Equipment</a></li>
            <li><a id="DeliverPage" href="{{url('/Deliver')}}">Deliver Equipment</a></li>

            @if(\Illuminate\Support\Facades\Auth::check())
                <li style="float:right"><a id="AdministratorPage" href="{{url('/Administrator')}}">Administrator</a></li>
            @endif
        </ul>
    </div>

    <!-- TODO: Implement this bootstrap container. New NavBar.
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Find Equipment</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-link active" aria-current="page" href="#">Borrow</a>
                        <a class="nav-link active" href="#">Deliver</a>
                    </div>
                    <div class="navbar-nav ms-auto">
                        <a class="nav-link" style="float: right;" href="#" tabindex="-1" aria-disabled="true">Administrator</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    -->
</div>

    <!-- Changes what part of navigationbar is highlighted in red based on webpage -->
    <script defer>
        //Changes what part of navigationbar is highlighted in red based on webpage
        function changeNavigationbarHighlight(page) {
            document.getElementById('FindEquipmentPage').setAttribute('class','');
            document.getElementById('BorrowPage').setAttribute('class','');
            document.getElementById('DeliverPage').setAttribute('class','');

            try {
                document.getElementById('AdministratorPage').setAttribute('class','');
            } catch (e) {}

            document.getElementById(page).setAttribute('class','active');
        }
    </script>

    @yield('content')


</body>
</html>

