<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ระบบเคลื่อนย้ายผู้ป่วย | โรงพยาบาลพระยุพราชเดชอุดม</title>
        <link rel="icon" type="image/png"  href="img/logo5.png">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Sarabun:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Sarabun', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                background-image: url("/img/bg.jpg");
                background-size:cover;

            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links" >
                    @auth
                        <a href="{{ url('/select_service') }}" style="color: black">admin</a>

                        <a href="{{ route('select_depart') }}" style="color: black">ระบบเรียก</a>
                    @else
                        <a href="{{ route('login') }}" style="color: black">admin</a>

                        @if (Route::has('register'))
                            <a href="{{ route('select_depart') }}" style="color: black">ระบบเรียก</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    <img src="{{URL::asset('/img/logo.png')}}" width="350" class="d-inline-block align-top" alt="">
                </div>
                <div class="title m-b-md" style="color: rgb(7, 78, 13)">
                   <h4 style="margin:0">ระบบเคลื่อนย้ายผู้ป่วย</h4>
                   <h4 style="margin:0; margin-block-start: 0">โรงพยาบาลสมเด็จพระยุพราชเดชอุดม</h4>
                </div>
            </div>
        </div>
    </body>
</html>
