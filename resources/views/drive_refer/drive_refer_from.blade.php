<?php
define('CLIENT_ID', 'hvkKRRafzQYD9Ot3bLnEM5'); // เปลี่ยนเป็น Client ID ของเรา
define('LINE_API_URI', 'https://notify-bot.line.me/oauth/authorize?');
define('CALLBACK_URI', 'http://192.168.231.20:8000/notify'); // เปลี่ยนให้ตรงกับ Callback URL ของเรา
$queryStrings = [
    'response_type' => 'code',
    'client_id' => CLIENT_ID,
    'redirect_uri' => CALLBACK_URI,
    'scope' => 'notify',
    'state' => 'abcdef123456'
];
$queryString = LINE_API_URI . http_build_query($queryStrings);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>แบบคำขอเรียกตัว{{ Auth::user()->service }} - Admin</title>
    @include('admin.asset.css')
    <link rel="icon" type="image/png" href="img/logo.png">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    {{-- <link href="datetime/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> --}}
    <link href="datetime/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="dist/css/bootstrap-select.css">
    <style>
        body {
            font-family: Kanit;
            background-image: url("/img/bg.jpg");
            background-size: cover;
            height: 100 !important;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        #x {
            position: absolute;
            color: white;
            top: -13px;
            right: -8px;
            width: 25px;
            height: 25px;
            z-index: 99;
        }

        #x {
            position: absolute;
            color: white;
            top: -13px;
            right: -8px;
            width: 25px;
            height: 25px;
            z-index: 99;
        }

        ul {
            list-style-type: none;

        }

        /* li {
                display: inline-block;
            } */

        input[type="checkbox"][id^="cb"] {
            display: none;
        }

        label {
            display: block;
            position: relative;
            cursor: pointer;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        label::before {
            background-color: white;
            color: white;
            content: " ";
            display: block;
            border-radius: 50%;
            border: 1px solid grey;
            position: absolute;
            top: -5px;
            left: -5px;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 22px;
            transition-duration: 0.4s;
            transform: scale(0);
        }

        label img {
            height: 200px;
            width: 200px;
            transition-duration: 0.2s;
            transform-origin: 50% 50%;
            border-radius: 50%;
        }

        :checked+label {
            border-color: #ddd;
        }

        :checked+label::before {
            content: "✓";
            background-color: rgb(92, 216, 54);
            transform: scale(1);
        }

        :checked+label img {
            transform: scale(0.9);
            box-shadow: 0 0 20px rgb(4, 255, 58);
            z-index: -1;
        }

    </style>
    @include('admin.asset.jconcss')
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="drive_refer_from">ระบบเรียก{{ Auth::user()->service }}</a> <br>
        <button class="btn btn-link btn-sm order-0 order-lg-0" style="padding-left: 20px;" id="sidebarToggle"
            href="#"><i class="fas fa-bars"></i></button>
        <!-- Navbar-->
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest
                <li class="nav-item">
                    <a class="nav-link"
                        href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if(Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link"
                            href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        สวัสดี {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link active" href="drive_refer_from">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            แบบฟอร์มเรียก
                        </a>
                        <a class="nav-link" href="<?php echo $queryString; ?>">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                            ลงทะเบียนรับแจ้งเตือน.
                        </a>
                        {{-- <a class="nav-link" href="Cribbooking">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                แบบคำขอ
                            </a> --}}
                        <a class="nav-link" href="drive_refer_tables">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            ตารางการทำงาน
                        </a>
                        <a class="nav-link" href="drive_refer_sum">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            ตารางสรุปงาน
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    {{ Auth::user()->name }}
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <form method="POST" id="myform" action="" class="needs-validation" novalidate>
                    @csrf
                    <div class="container">
                        <div class="row justify-content-center align-items-center">
                            <div class="col center-auto" style="padding-top: 20px;">
                                <!--div class='card-transparent' id="main-content"-->
                                <div class='card shadow-sm'>
                                    <div class='card-header'>
                                        <div class="row">
                                            <div class="col">
                                                <div class="text-dark font-weight-bold">
                                                    <i class='fas fa-info-circle'></i>
                                                    กรุณากรอกข้อมูลให้ครบถ้วน
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class='card shadow-sm' id="main-content">
                                    <input class="form-control" id="user_add" name="user_add" type="hidden"
                                        value="{{ Auth::user()->name }}" readonly>
                                    <input class="form-control" id="service" name="service" type="hidden"
                                        value="{{ Auth::user()->service }}" readonly>
                                    <div class='card-body'>
                                        <div class='row form-group'>
                                            <div class='col'>
                                                <label class='title' id="patient_name_label">1) ประเภทงาน</label>
                                                <div class="col">
                                                    <div class="row">
                                                        <div class="col-lg">
                                                            <input class="form-control" id="section" name="section"
                                                                type="text" value="{{ Auth::user()->service }}"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class='row form-group'>
                                            <div class='col'>
                                                <label class='title' id="patient_place_label">2) สถานที่รับ </label>
                                                <div class="col">
                                                    <input type="text" class="form-control" placeholder="สถานที่รับ..." id="pickup" name="pickup" >
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class='row form-group'>
                                            <div class='col'>
                                                <label class='title' id="patient_place_label">3)
                                                    {{ Auth::user()->service }}</label>
                                                <div class="col">
                                                    <select class="selectpicker form-control" data-live-search="true"
                                                        data-dropup-auto="true" title="--กรุณาเลือกแผนก--"
                                                        data-size="10" id="name_ssn" name="name_ssn" data-search="true"
                                                        theme="google" required>
                                                        @foreach($data as $row)
                                                            <option value="{{ $row->cid }}">{{ $row->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class='row form-group'>
                                            <div class='col'>
                                                <label class='title' id="patient_place_label">4) เวร</label>
                                                <div class="col">
                                                    <select class="selectpicker form-control" data-live-search="true"
                                                        data-dropup-auto="true" title="--กรุณาเลือกเวร--" data-size="10"
                                                        id="shift" name="shift" data-search="true" theme="google"
                                                        required>
                                                        <option value="เช้า">เช้า</option>
                                                        <option value="บ่าย">บ่าย</option>
                                                        <option value="ดึก">ดึก</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class='row form-group'>
                                            <div class='col-auto'>
                                                <label class='title' id="book_tel_label">5) วันเวลารับ</label>
                                                <div class="col">
                                                    <input
                                                        class="form-control bg-white datepicker date form_datetime readonly"
                                                        size="25" type="text" name="datetime" id="date_admit"
                                                        placeholder="กรุณาระบุวันเวลา.." required autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row form-group">
                                            <div class="col">
                                                <label class='title'>6) ระดับความเร่งด่วน</label>
                                                <div class="col">
                                                    <div class="col-auto">
                                                        <ul>
                                                            <li style="display: inline-block;"><input type="radio"
                                                                    class="custom-control-input" id="note_1" name="note"
                                                                    value='1' onchange="hide_current(this);" required />
                                                                <label for="note_1"><img src="img/ปกติ.png"
                                                                        style="height: 50px; width: 50px"> ปกติ</label>
                                                            </li>&emsp;&emsp;
                                                            <li style="display: inline-block;"><input type="radio"
                                                                    class="custom-control-input" id="note_2" name="note"
                                                                    value='2' onchange="hide_current(this);" required />
                                                                <label for="note_2"><img src="img/ด่วน.png"
                                                                        style="height: 50px; width: 50px"> ด่วน</label>
                                                            </li>&emsp;&emsp;
                                                            <li style="display: inline-block;"><input type="radio"
                                                                    class="custom-control-input" id="note_3" name="note"
                                                                    value='3' onchange="show_current(this);" required />
                                                                <label for="note_3"><img
                                                                        style="height: 50px; width: 50px"
                                                                        src="img/ด่วนที่สุด.png"> ด่วนที่สุด</label>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div id="note_danger" class="panel-collapse collapse">
                                                        <div class="panel-body">
                                                            <label class='title'>***กรณีด่วนที่สุด***
                                                            </label>
                                                            <select type="text" class="form-control" id="danger_note"
                                                                name="danger_note">
                                                                <option selected hidden value=''>--เลือกประเภท--
                                                                </option>
                                                                <option value="ผู้ป่วยเป็นลม">ผู้ป่วยเป็นลม</option>
                                                                <option value="อุบัติเหตุในโรงพยาบาล">
                                                                    อุบัติเหตุในโรงพยาบาล</option>
                                                                <option value="ผู้ป่วยWard เข้า ICU">ผู้ป่วยWard เข้า
                                                                    ICU</option>
                                                                <option value="ER  เข้า ICU">ER เข้า ICU</option>
                                                                <option value="ช่วย CPR">ช่วย CPR</option>
                                                                <option value="ออก EMS">ออก EMS</option>
                                                                <option value="ผู้ป่วยคลอดฉุกเฉิน">ผู้ป่วยคลอดฉุกเฉิน
                                                                </option>
                                                                <option value="ผู้ป่วย CT ด่วน เช่น Stroke fast track">
                                                                    ผู้ป่วย CT ด่วน เช่น
                                                                    Stroke fast track</option>
                                                                <option value="ผู้ป่วยเข้า OR">ผู้ป่วยเข้า OR</option>
                                                                <option value="ผู้ป่วยมีอาการจาก OPD มา ER">
                                                                    ผู้ป่วยมีอาการจาก OPD มา ER
                                                                </option>
                                                                <option value="ยาฉุกเฉินที่ต้องใช่เพิ่ม">
                                                                    ยาฉุกเฉินที่ต้องใช่เพิ่ม</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class='row form-group'>
                                            <div class='col-lg'>
                                                <label class='title' id="book_tel_label">7) หมายเหตุ</label>
                                                <div class="col-lg">
                                                    <input class="form-control" size="25" type="text" name="other"
                                                        id="other" placeholder="อื่นๆ.." autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row  align-middle">
                                            <div class="col text-right">
                                                <button onclick="mySubmitClick();" type="button" name="book"
                                                    class="btn btn-success w-100" style="">
                                                    <span style="font-size: 24px;"><i class='fas fa-check'></i>
                                                        ตกลง</span>
                                                </button>
                                            </div>
                                            <div class='col'>
                                                <button type="reset" class="btn btn-danger w-100"
                                                    onclick="window.location.href='Cribbooking';"><span
                                                        style="font-size: 24px;"><i class='fas fa-undo'></i>
                                                        เริ่มใหม่</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- root card -->
                            </div>
                            <!-- root col -->
                        </div>
                        <!-- root row -->
                    </div>
                    <!-- root container -->
                </form>
            </main>
            <br>
            @include('admin.footer')
        </div>
    </div>
    <div class="modal fade" data-keyboard="false" data-backdrop="static" id="note_modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="note-form" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">กรุณากรอกข้อมูล</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="clearModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="">ผู้ยืนยัน</label>
                        <input type="text" class="form-control " id="confirm_doc" name="confirm_doc"
                            value="{{ Auth::user()->name }}" readonly>
                        <br>
                        <label for="">ประเมินความพึงพอใจ</label><br>
                        <div style="text-align: center">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate1" name="rate" class="custom-control-input" value="1"
                                    required>
                                <label class="custom-control-label" for="rate1">แย่มาก</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate2" name="rate" class="custom-control-input" value="2"
                                    required>
                                <label class="custom-control-label" for="rate2">แย่</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate3" name="rate" class="custom-control-input" value="3"
                                    required>
                                <label class="custom-control-label" for="rate3">ปกติ</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate4" name="rate" class="custom-control-input" value="4"
                                    required>
                                <label class="custom-control-label" for="rate4">ดี</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate5" name="rate" class="custom-control-input" value="5"
                                    required>
                                <label class="custom-control-label" for="rate5">ดีมาก</label>
                            </div>
                        </div>
                        <br>
                        <input type="text" class="form-control " id="note_doc" name="note_doc"
                            placeholder="หมายเหตุ...">
                        <br>
                        <input type="hidden" name="id_hidden" id="id_hidden">
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">ยืนยัน</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"
                            onclick="clearModal()">ปิด</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.asset.js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    @include('admin.asset.jcon')
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script type="text/javascript" src="datetime/bootstrap/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script type="text/javascript" src="datetime/bootstrap/locales/bootstrap-datetimepicker.th.js" charset="UTF-8">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
    <script src="regis/js/bootstrap-datepicker.js"></script>
    <script src="regis/js/bootstrap-datepicker-thai.js"></script>
    <script src="regis/js/locales/bootstrap-datepicker.th.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="template/dist/js/scripts.js"></script>


    <script src="dist/js/bootstrap-select.js"></script>

    <script>
        $(".readonly").keydown(function (e) {
            e.preventDefault();
        });

    </script>

    <script type="text/javascript">
        $('.form_datetime').datetimepicker({
            language: 'th',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            showMeridian: 1,
            format: "dd MM yyyy - hh:ii"
        });

    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>

    <script>
        function show_current(f) {
            f.checked == true
            $("#note_danger").show();
            $("#danger_note").prop('required', true);

        }

        function hide_current(f) {
            f.checked == true
            $("#note_danger").hide();
            $("#danger_note").val('');
            $("#danger_note").prop('required', false);
        }


        $('#myform').submit(function (submit) {
            submit.preventDefault();
            let formData = new FormData(this)
            $.ajax({
                type: "post",
                url: "send_drive",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response == 'success') {

                    } else {

                    }
                }
            });
        });

        function mySubmitClick() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function (form) {
                if (form.checkValidity() === false) {
                    form.classList.add('was-validated');

                    var control_top = null;

                    if ($.trim($("#patient_tname").val()) == "") control_top = $("#patient_name_label");
                    else if ($.trim($("#patient_fname").val()) == "") control_top = $("#patient_name_label");
                    else if ($.trim($("#patient_lname").val()) == "") control_top = $("#patient_name_label");

                    else if (!$("input[name='instype']:checked").val()) control_top = $(
                        "#patient_instype_label");

                    else if ($.trim($("#date_admit").val()) == "") control_top = $("#patient_admit_label");

                    else if ($.trim($("#patient_cur").val()) == "") control_top = $("#patient_place_label");

                    else if (!$("input[name='room']:checked").val()) control_top = $("#patient_bookroom_label");

                    else if ($.trim($("#book_tname").val()) == "") control_top = $("#book_name_label");
                    else if ($.trim($("#book_fname").val()) == "") control_top = $("#book_name_label");
                    else if ($.trim($("#book_lname").val()) == "") control_top = $("#book_name_label");


                    else if ($.trim($("#book_tel").val()) == "") control_top = $("#book_tel_label");

                    errorMsg("โปรดระบุข้อมูลให้ครบทุกข้อ", control_top);

                } else {
                    //alert("submit");
                    confirmMsg("ยืนยันการเรียกพนักงาน", function () {
                        $("#myform").submit();
                    });
                    // successMsg("ยืนยันการจองห้องพิเศษ", function(){$("#myform").submit();});

                }
            });
        }

        function confirmMsg(par_msg, par_func) {
            //function confirmMsg(par_msg) {
            $.confirm({
                title: "<i class='fas fa-question-circle text-info' style='font-size:24px'></i> ยืนยัน",
                content: "<span>" + par_msg + "</span>",
                buttons: {
                    ok: {
                        text: "ยืนยัน",
                        btnClass: 'bg-white text-success',
                        action: function () {
                            //document.getElementById("myform").submit();
                            successMsg("เรียกพนักงานสำเร็จ");
                            par_func();

                        }
                    },
                    info: {
                        text: "ยกเลิก",
                        btnClass: 'bg-white text-danger',
                        action: function () {
                            //return false;
                        }
                    },

                }
            });
            /** jquery-confirm */
        }

        function successMsg(par_msg, par_func) {
            $.confirm({
                title: "<i class='fas fa-check-circle text-success' style='font-size:24px'></i> สำเร็จ",
                content: "<span>" + par_msg + "</span>",
                buttons: {
                    info: {
                        text: "ตกลง",
                        btnClass: 'btn btn-success',
                        action: function () {
                            // par_func();
                            window.open('drive_refer_tables','_parent');
                        }
                    },
                }
            });
        }

        function errorMsg(par_msg, par_control) {
            $.confirm({
                title: "<i class='fas fa-times-circle text-danger' style='font-size:24px'></i> ผิดพลาด",
                content: "<span>" + par_msg + "</span>",
                buttons: {
                    info: {
                        text: "ปิด",
                        btnClass: 'bg-white text-secondary',
                        action: function () {

                            window.setTimeout(
                                function () { // input timeout sleep because alert popup scrollbar loop
                                    $([document.documentElement, document.body]).animate({
                                        scrollTop: par_control.offset().top
                                    }, 1500);

                                    //
                                }, 300); // 500 is 1/2 sec

                            //alert(par_control.text());
                        }
                    },
                }
            });
        }

    </script>
</body>

</html>
