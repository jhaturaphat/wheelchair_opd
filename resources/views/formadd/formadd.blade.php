<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ระบบ{{ Auth::user()->service }} | โรงพยาบาลพระยุพราชเดชอุดม</title>
    @include('admin.asset.css')
    <link rel="icon" type="image/png" href="img/logo_am.png">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    {{-- <link href="datetime/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> --}}
    <link href="datetime/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun">
    <link rel="stylesheet" href="dist/css/bootstrap-select.css">
    @include('admin.asset.jconcss')
    <style>
        body {
            font-family: Sarabun;
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

</head>

<body class="sb-nav-fixed">
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
        <a class="navbar-brand" href="Cribbooking">
            <img src="img/logo.png" width="35" height="30" class="d-inline-block align-top" alt="">
            ระบบ{{ Auth::user()->service }}
        </a>

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
                        สวัสดีคุณ {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="showdata">ดูตารางการขอ</a>
                        <a class="dropdown-item" href="showsucc">ดูรายการที่สำเร็จ</a>
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
                                    <label class='title' id="patient_instype_label">1) HN (กรอกเสร็จกด enter เพื่อเรียกชื่อคนไข้)</label>
                                    <div class="col-lg">
                                        <input type="text" inputmode="numeric" class="form-control "
                                            id="hn" name="hn" placeholder="กรอก hn 9 หลัก...." maxlength="9"
                                            autocomplete="off" onkeypress="key(event);">
                                        {{-- <input type="text" class="form-control " id="hn" name="hn" placeholder="ชื่อ..." > --}}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class='row form-group'>
                                <div class='col'>
                                    <label class='title' id="patient_instype_label">2) ชื่อ-นามสุกล คนไข้
                                        *(ไม่กรอกกรณีออก ems)</label>
                                    <div class="col-lg">
                                        <input type="text" class="form-control " id="name" name="name"
                                            placeholder="ชื่อ...">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class='row'>
                                <div class='col'>
                                    <label class='title' id="patient_name_label">3) แผนก</label>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col">
                                                <input class="form-control" id="section" name="section" type="text"
                                                    value="{{ Auth::user()->depart }}" readonly>
                                                {{-- <input class="form-control" id="section" name="section"  type="text" placeholder="แผนก..." > --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class='row form-group'>
                                <div class='col'>
                                    <label class='title' id="patient_place_label">4) สถานที่รับ *(เลือกรับที่อื่น
                                        กรุณาลบแล้วเลือกใหม่)</label>
                                    <div class="col">
                                        <select class="selectpicker form-control" data-size="15" data-live-search="true"
                                            data-dropup-auto="true" title="กรุณาเลือกแผนก" name="pickup" id="pickup">
                                            <option selected value="{{ Auth::user()->depart }}">
                                                {{ Auth::user()->depart }}</option>
                                            @foreach($data as $row)
                                                <option value="{{ $row->department }}">{{ $row->department }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class='row form-group'>
                                <div class='col'>
                                    <label class='title' id="patient_instype_label">5) หมายเลขเตียง</label>
                                    <div class="col-lg">
                                        <input type="text" class="form-control " id="bednumber" name="bednumber"
                                            placeholder="โปรดระบุหมายเลขเตียง...">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col">
                                    <label class="title" id="patient_bookroom_label">6) อุปกรณ์ </label>
                                    <div class="col">
                                        <div class="col-auto">
                                            <ul>&emsp;
                                                <li style="display: inline-block;"><input type="checkbox"
                                                        class="custom-control-input" id="equipment_1" name="equipment[]"
                                                        value='เปลนอน' />
                                                    <label for="equipment_1"><img src="img/เตียง.png" /></label>
                                                </li>&emsp;&emsp;
                                                <li style="display: inline-block;"><input type="checkbox"
                                                        class="custom-control-input" id="equipment_2" name="equipment[]"
                                                        value='รถนั่ง' />
                                                    <label for="equipment_2"><img src="img/รถเข็น.png" /></label>
                                                </li>&emsp;&emsp;
                                                <li style="display: inline-block;"><input type="checkbox"
                                                        class="custom-control-input" id="equipment_type_1"
                                                        name="equipment_type[]" value='น้ำเกลือ' />
                                                    <label for="equipment_type_1"><img src="img/น้ำเกลือ.png" /></label>
                                                </li>&emsp;&emsp;
                                                <li style="display: inline-block;"> <input type="checkbox"
                                                        class="custom-control-input" id="equipment_type_2"
                                                        name="equipment_type[]" value='ถังออกซิเจน' />
                                                    <label for="equipment_type_2"><img
                                                            src="img/ถังออกซิเจน.png" /></label>
                                                </li>
                                            </ul>
                                        </div>
                                        <input type="text" class="form-control" id="equipment_note"
                                            name="equipment_note" placeholder="หมายเหตุ">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class='row form-group'>
                                <div class='col'>
                                    <label class='title' id="book_name_label">7) สถานที่ส่ง</label>
                                    <div class="row">
                                        <div class="col-lg">
                                            <div class="col">
                                                <select class="selectpicker form-control" data-size="15"
                                                    data-live-search="true" data-dropup-auto="true"
                                                    title="กรุณาเลือกแผนก" id="send" name="send">
                                                    <option selected hidden value=''>--เลือกแผนก--</option>
                                                    <option value="จุดเกิดเกิดเหตุ">จุดเกิดเกิดเหตุ</option>
                                                    <option value="กลับบ้าน">กลับบ้าน</option>
                                                    @foreach($data as $row)
                                                        <option value="{{ $row->department }}">
                                                            {{ $row->department }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class='row form-group'>
                                <div class='col-auto'>
                                    <label class='title' id="book_tel_label">8) วันเวลารับ</label>
                                    <div class="col">
                                        <input class="form-control bg-white datepicker date form_datetime readonly"
                                            size="25" type="text" name="datetime" id="date_admit"
                                            placeholder="กรุณาระบุวันเวลา.." required autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row form-group">
                                <div class="col">
                                    <label class='title'>9) หมายเหตุ</label>
                                    <div class="col">
                                        <div class="col-auto">
                                            <ul>
                                                <li style="display: inline-block;"><input type="radio"
                                                        class="custom-control-input" id="note_1" name="note" value='1'
                                                        onchange="hide_current(this);" required />
                                                    <label for="note_1"><img src="img/ปกติ.png"
                                                            style="height: 50px; width: 50px"> ปกติ</label>
                                                </li>&emsp;&emsp;
                                                <li style="display: inline-block;"><input type="radio"
                                                        class="custom-control-input" id="note_2" name="note" value='2'
                                                        onchange="hide_current(this);" required />
                                                    <label for="note_2"><img src="img/ด่วน.png"
                                                            style="height: 50px; width: 50px"> ด่วน</label>
                                                </li>&emsp;&emsp;
                                                <li style="display: inline-block;"><input type="radio"
                                                        class="custom-control-input" id="note_3" name="note" value='3'
                                                        onchange="show_current(this);" required />
                                                    <label for="note_3"><img style="height: 50px; width: 50px"
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
                                                    <option selected hidden value=''>--เลือกประเภท--</option>
                                                    <option value="ผู้ป่วยเป็นลม">ผู้ป่วยเป็นลม</option>
                                                    <option value="อุบัติเหตุในโรงพยาบาล">อุบัติเหตุในโรงพยาบาล</option>
                                                    <option value="ผู้ป่วยWard เข้า ICU">ผู้ป่วยWard เข้า ICU</option>
                                                    <option value="ER เข้า ICU">ER เข้า ICU</option>
                                                    <option value="ช่วย CPR">ช่วย CPR</option>
                                                    <option value="ออก EMS">ออก EMS</option>
                                                    <option value="ผู้ป่วยคลอดฉุกเฉิน">ผู้ป่วยคลอดฉุกเฉิน</option>
                                                    <option value="ผู้ป่วย CT ด่วน เช่น Stroke fast track">ผู้ป่วย CT
                                                        ด่วน เช่น Stroke fast track</option>
                                                    <option value="ผู้ป่วยเข้า OR">ผู้ป่วยเข้า OR</option>
                                                    <option value="ผู้ป่วยมีอาการจาก OPD มา ER">ผู้ป่วยมีอาการจาก OPD มา
                                                        ER</option>
                                                    <option value="ยาฉุกเฉินที่ต้องใช่เพิ่ม">ยาฉุกเฉินที่ต้องใช่เพิ่ม
                                                    </option>
                                                    <option value="ส่งต่อผู้ป่วย (Refer)">ส่งต่อผู้ป่วย (Refer)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row  align-middle">
                                <div class="col text-right">
                                    <button onclick="mySubmitClick();" type="button" name="book"
                                        class="btn btn-success w-100" style="">
                                        <span style="font-size: 24px;"><i class='fas fa-check'></i> ตกลง</span>
                                    </button>
                                </div>
                                <div class='col'>
                                    <button type="reset" class="btn btn-danger w-100"
                                        onclick="window.location.href='Cribbooking';"><span style="font-size: 24px;"><i
                                                class='fas fa-undo'></i> เริ่มใหม่</span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- root card -->
                </div>
                <!-- root col -->
            </div>
            <!-- root row -->
            <div class="row mt-3 mb-3">
                <!--small-->
                <div class='col text-center' style="padding-bottom: 20px;">
                    <i class='far fa-copyright'></i> <script>
                        document.write(new Date().getFullYear())
                    </script> <a class='text-dark' target='_blank'>ศูนย์ข้อมูล</a>
                    <a target='_blank' class='text-dark'>รพ.อำนาจเจริญ</a>
                </div>
            </div>
        </div>
        <!-- root container -->
    </form>
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
        var str = null;

        function key(event) {
            number = $('#hn').val();
            if (event.keyCode == 13) {
                var str = number
                var pad = "000000000"
                var data = pad.substring(0, pad.length - str.length) + number
                $('#hn').prop('value',data);
                $.ajax({
                    type: "post",
                    url: "search_hn",
                    data: {
                        "hn": data,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (res) {
                        if (res.data.an == null) {
                            $('#name').prop('value', res.data.pname + " " + res.data.fname + " " + res.data
                                .lname);
                            // $('#section').prop('value',res.data.name);
                            $('#case').prop('value', 'OPD');
                        } else {
                            $('#name').prop('value', res.data.pname + " " + res.data.fname + " " + res.data
                                .lname);
                            // $('#section').prop('value',res.data.name);
                            $('#case').prop('value', 'IPD');
                        }
                    }
                });
            } else {
                return false;
            }
        }

        // function Myhn(){
        //     number = $('#hn').val();
        //     if(number.length < 7){
        //         var str = number
        //         var pad = "0000000"
        //         var data = pad.substring(0, pad.length - str.length) + number
        //         $('#hn').prop('value',data);
        //         $.ajax({
        //             type: "post",
        //             url: "search_hn",
        //             data: {
        //                 "hn": data,
        //                 "_token": "{{ csrf_token() }}"
        //             },
        //             success: function (res) {
        //                 console.log(res.data);
        //                 if (res.data.an == null) {
        //                     $('#name').prop('value', res.data.pname + " " + res.data.fname + " " + res.data
        //                         .lname);
        //                     // $('#section').prop('value',res.data.name);
        //                     $('#case').prop('value', 'OPD');
        //                 } else {
        //                     $('#name').prop('value', res.data.pname + " " + res.data.fname + " " + res.data
        //                         .lname);
        //                     // $('#section').prop('value',res.data.name);
        //                     $('#case').prop('value', 'IPD');
        //                 }
        //             }
        //         });
        //     }else{
        //     }
        // }

        //  $('#hn').on("keypress", function () {
        //         var data = this.value;
        //         console.log(data);
        //         if(data.length > 8 ){
        //             $.ajax({
        //                 type: "post",
        //                 url: "search_hn",
        //                 data:{
        //                         "hn": data,
        //                         "_token": "{{ csrf_token() }}"
        //                     },
        //                 success: function (res) {
        //                     console.log(res.data);
        //                     if(res.data.an == null ){
        //                         $('#name').prop('value',res.data.pname +" "+ res.data.fname +" "+ res.data.lname);
        //                         // $('#section').prop('value',res.data.name);
        //                         $('#case').prop('value','OPD');
        //                     }else{
        //                         $('#name').prop('value',res.data.pname +" "+ res.data.fname +" "+ res.data.lname);
        //                         // $('#section').prop('value',res.data.name);
        //                         $('#case').prop('value','IPD');
        //                     }
        //                 }
        //             });
        //         }else{

        //         }

        //     });



        // $('#hn').on("input", function () {
        //     var data = this.value;
        //     if(data.length > 8 ){
        //         $.ajax({
        //             type: "post",
        //             url: "search_hn",
        //             data:{
        //                     "hn": data,
        //                     "_token": "{{ csrf_token() }}"
        //                 },
        //             success: function (res) {
        //                 console.log(res.data);
        //                 if(res.data.an == null ){
        //                     $('#name').prop('value',res.data.pname +" "+ res.data.fname +" "+ res.data.lname);
        //                     // $('#section').prop('value',res.data.name);
        //                     $('#case').prop('value','OPD');
        //                 }else{
        //                     $('#name').prop('value',res.data.pname +" "+ res.data.fname +" "+ res.data.lname);
        //                     // $('#section').prop('value',res.data.name);
        //                     $('#case').prop('value','IPD');
        //                 }
        //             }
        //         });
        //     }else{

        //     }

        // });


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
                url: "save",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response == 'success') {
                        successMsg("เรียกเวรเปล สำเร็จ");
                    } else {
                        errorMsg("ไม่สามารถทำรายการได้กรุณาลองใหม่อีกครั้ง");
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    errorMsg("ไม่สามารถทำรายการได้กรุณาลองใหม่อีกครั้ง");
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
                    confirmMsg("ยืนยันการเรียกพนักงานเวรเปล", function () {
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
                            window.open('showdata', '_parent');
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
                            // location.reload();
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
