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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sarabun">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    {{-- <link href="datetime/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> --}}
    <link href="datetime/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="dist/css/bootstrap-select.css">

    <style>
        body {
            font-family: Sarabun;
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

        .sweet_loader {
            width: 140px;
            height: 140px;
            margin: 0 auto;
            animation-duration: 0.5s;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
            animation-name: ro;
            transform-origin: 50% 50%;
            transform: rotate(0) translate(0, 0);
        }

        @keyframes ro {
            100% {
                transform: rotate(-360deg) translate(0,0);
            }
        }

        .swal-modal .swal-text {
            text-align: left;
        }

        .note {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #afe7eb;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
            text-align: left;
        }

        table#dataTable.dataTable tbody tr.Highlight {
            background-color: rgb(243, 150, 138);
        }

    </style>
    @include('admin.asset.jconcss')
</head>

<body class="sb-nav-fixed">
    <nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
        <a class="navbar-brand" href="Cribbooking">
            <img src="img/logo.png" width="35" height="30" class="d-inline-block align-top" alt="">
            <!--i class='fas fa-bed'></i-->
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
                        <a class="dropdown-item" href="Cribbooking">แบบคำขอ สสน.</a>
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
    {{-- <div class="col-md-4">
            <form action="/search" method="GET">
                <div class="input-grup">
                    <input type="search" name="query" id="query" class="form-control">
                    <span class="input-grup-preped">
                        <button type="submit" class="btn btn-primary">search</button>
                    </span>
                </div>
            </form>
        </div> --}}

    <form method="POST" id="myform" action="" class="needs-validation" novalidate>
        @csrf
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col center-auto" style="padding-top: 20px;padding-right: 0px;padding-left: 0px;">
                    <input class="form-control" type="hidden" id="section" name="section"
                        value="{{ Auth::user()->depart }}">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-table mr-1"></i>
                                    ตารางแสดงข้อมูลการขอใช้พนักงานที่ดำเนินการยังไม่จบกระบวนการ(หน่วยงาน)</div>
                                <div>
                                    <span id="count" class="badge badge-danger" style="font-size:16px;"></span>
                                    <span id="count_wait" class="badge badge-warning" style="font-size:16px;"></span>
                                    <span id="count_succ" class="badge badge-success" style="font-size:16px;"></span>
                                    <span type="button" class="btn btn-info" style="font-size:16px;" id="reload"
                                        onclick="reload()">คลิ๊กเพื่อ reload</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="11">ลำดับ</th>
                                            <th scope="col" width="140">แผนก</th>
                                            <th scope="col" width="150">ชื่อ-นามสกุล คนไข้</th>
                                            <th scope="col" width="140">สถานที่รับ</th>
                                            <th scope="col" width="50">อุปกรณ์</th>
                                            <th scope="col" width="100">หมายเหตุ</th>
                                            <th scope="col" width="140">สถานที่ส่ง</th>
                                            <th scope="col" width="120">วันเวลารับ</th>
                                            <th scope="col" width="10">ความเร่งด่วน</th>
                                            <th scope="col" width="10">เวลารับเรื่อง</th>
                                            <th scope="col" width="100">ชื่อ สสน.</th>
                                            <th scope="col" width="50">สถานะการรับ</th>
                                            {{-- <th scope="col" width="50">เวลาสำเร็จ</th> --}}
                                            <th scope="col" width="50">action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class='col text-center'>
                            <i class='far fa-copyright'></i>
                            <script>
                                document.write(new Date().getFullYear())

                            </script> <a class='text-dark' target='_blank'>ศูนย์ข้อมูล</a>
                            <a target='_blank' class='text-dark'>รพ.อำนาจเจริญ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- root container -->
    </form>

    {{-- modal-success --}}

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


    {{-- modal-sent_to --}}
    <form id="sent_to_form" method="post">
        @csrf
        <div class="modal fade" data-keyboard="false" data-backdrop="static" id="sent_to_modal" tabindex="-1"
            role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">กรุณากรอกข้อมูล</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="clearModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class='row form-group'>
                            <div class='col'>
                                <label for="">ผู้ยืนยัน</label>
                                <div class="col-lg">
                                    <input type="text" class="form-control " id="confirm_doc" name="confirm_doc"
                                        value="{{ Auth::user()->name }}" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- <div class='row form-group'> --}}
                        <div class='col'>
                            {{-- <label class='title' id="patient_instype_label">2) ชื่อ-นามสุกล คนไข้</label> --}}
                            <div class="col-lg">
                                <input type="hidden" class="form-control "id="hn" name="hn">
                                <input type="hidden" class="form-control " id="name" name="name" placeholder="ชื่อ...">
                                <input class="form-control" id="case" name="case" type="hidden">
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <hr> --}}
                        <div class='row form-group'>
                            <div class='col'>
                                <label class='title' id="patient_place_label"> สถานที่รับ </label>
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
                        {{-- <div class='row form-group'> --}}
                        <div class='col'>
                            {{-- <label class='title' id="patient_instype_label">4) หมายเลขเตียง</label> --}}
                            <div class="col-lg">
                                <input type="hidden" class="form-control " id="bednumber" name="bednumber"
                                    placeholder="โปรดระบุหมายเลขเตียง..." required>
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <hr> --}}
                        {{-- <div class="row"> --}}
                        <div class="col">
                            {{-- <label class="title" id="patient_bookroom_label">5) อุปกรณ์ </label> --}}
                            <div class="col">
                                <input type="hidden" class="form-control" id="service" name="service" value="{{ Auth::user()->service }}" required>
                                <input type="hidden" class="form-control" id="equipment" name="equipment" required>
                                <input type="hidden" class="form-control" id="equipment_type" name="equipment_type"
                                    data-search="true" theme="google" style="margin-top: 5px;" required>
                            </div>
                        </div>
                        {{-- </div> --}}

                        {{-- <hr> --}}
                        <div class='row form-group'>
                            <div class='col'>
                                <label class='title' id="book_name_label"> สถานที่ส่ง</label>
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="col">
                                            <select class="selectpicker form-control" data-size="15"
                                                data-live-search="true" data-dropup-auto="true" title="กรุณาเลือกแผนก"
                                                name="send" id="send">
                                                <option selected value="" hidden>-เลือกสถานที่ส่ง-</option>
                                                @foreach($data as $row)
                                                    <option value="{{ $row->department }}">{{ $row->department }}
                                                    </option>
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
                                <label class='title' id="book_tel_label"> วันเวลาส่งต่อ</label>
                                <div class="col">
                                    <input class="form-control bg-white datepicker date form_datetime readonly"
                                        type="text" name="datetime" id="datetime" placeholder="กรุณาระบุวันเวลา.."
                                        required autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{-- <div class="row form-group"> --}}
                        <div class="col">
                            {{-- <label class='title'>8) หมายเหตุ</label> --}}
                            <div class="col">
                                <input type="hidden" class="form-control" id="note" name="note" >
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <hr> --}}
                        <div class="row form-group">
                            <div class="col">
                                <label class='title'>ตัวเลือกพนักงานเปล</label>
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="select_ssn"
                                            id="select_ssn1" onchange="hide_current(this);" value="1" required >
                                        <label class="form-check-label" for="select_ssn1">
                                            พนักงานคนใหม่
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="select_ssn"
                                            id="select_ssn2"  onchange="show_current(this);" value="2" required >
                                        <label class="form-check-label" for="select_ssn2">
                                            พนักงานคนเดิม
                                        </label>
                                    </div>
                                    <input type="text" class="form-control" id="name_ssn" name="name_ssn" readonly>
                                </div>
                            </div>
                        </div>
                        {{-- <hr> --}}
                        {{-- <div class="row form-group"> --}}
                        <div class="col">
                            {{-- <label class='title'>***กรณีด่วนที่สุด***</label> --}}
                            <div class="col">
                                <input type="hidden" class="form-control" id="danger_note" name="danger_note">
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <hr> --}}
                        {{-- <div class="row form-group"> --}}
                        <div class="col">
                            {{-- <label class='title'>***หมายเหตุ***</label> --}}
                            <div class="col">
                                <input type="hidden" class="form-control" id="equipment_note" name="equipment_note">
                            </div>
                        </div>
                        {{-- </div> --}}
                        <hr>
                        {{-- <div class="row form-group"> --}}
                        <div class="col">
                            {{-- <label class='title'>10) เวร</label> --}}
                            <div class="col">
                                <input type="hidden" class="form-control" id="shift" name="shift" required>
                                <input type="hidden" class="form-control" id="section01" name="section01" required>
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <hr> --}}
                        <input type="hidden" name="hiddenEdit" id="hiddenEdit">
                        <label for="">ประเมินความพึงพอใจ</label><br>
                        <div style="text-align: center">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate01" name="rate" class="custom-control-input" value="1"
                                    required>
                                <label class="custom-control-label" for="rate01">แย่มาก</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate02" name="rate" class="custom-control-input" value="2"
                                    required>
                                <label class="custom-control-label" for="rate02">แย่</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate03" name="rate" class="custom-control-input" value="3"
                                    required>
                                <label class="custom-control-label" for="rate03">ปกติ</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate04" name="rate" class="custom-control-input" value="4"
                                    required>
                                <label class="custom-control-label" for="rate04">ดี</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="rate05" name="rate" class="custom-control-input" value="5"
                                    required>
                                <label class="custom-control-label" for="rate05">ดีมาก</label>
                            </div>
                            <br>
                            <br>
                            <input type="text" class="form-control " id="note_doc" name="note_doc"
                                placeholder="หมายเหตุ...">
                            <br>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">ยืนยัน</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"
                            onclick="clearModal()">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



</body>

</html>

{{-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="template/dist/js/scripts.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script> --}}
{{-- <script src="assets/demo/datatables-demo.js"></script>
            <script src="template/dist/assets/demo/datatables-demo.js"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript" src="datetime/bootstrap/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="datetime/bootstrap/locales/bootstrap-datetimepicker.th.js" charset="UTF-8"></script> --}}
@include('admin.asset.js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@include('admin.asset.jcon')
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript" src="datetime/bootstrap/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="datetime/bootstrap/locales/bootstrap-datetimepicker.th.js" charset="UTF-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
<script src="regis/js/bootstrap-datepicker.js"></script>
<script src="regis/js/bootstrap-datepicker-thai.js"></script>
<script src="regis/js/locales/bootstrap-datepicker.th.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="template/dist/js/scripts.js"></script>
<script src="dist/js/bootstrap-select.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="chart/jquery.highchartTable.js" type="text/javascript"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>


<script type="text/javascript">
    $(".readonly").keydown(function (e) {
        e.preventDefault();
    });


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

    function reload() {
        data_table.ajax.reload();
        noticount();
        // noticount_wait();
        // noticount_succ();
    }


    function noticount() {
        section = $('#section').val();
        var sweet_loader =
            '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
        $.ajax({
            type: "post",
            url: "/countBook2",
            data: {
                "section": section,
                "_token": "{{ csrf_token() }}"
            },
            beforeSend: function () {
                swal.fire({
                    html: '<h5>กำลังโหลดข้อมูล...</h5>',
                    showConfirmButton: false,
                    onRender: function () {
                        $('.swal2-content').prepend(sweet_loader);
                    }
                });
            },
            success: function (response) {
                swal.close();
                $('#count').text('รอดำเนินการ ' + response.book);
            }
        });
    }

    function show_current(f) {
        f.checked == true
        $("#name_ssn").show();
    }

    function hide_current(f) {
        f.checked == true
        $("#name_ssn").hide();
    }


    // function noticount_wait() {
    //     section = $('#section').val();
    //     $.ajax({
    //         type: "post",
    //         url: "/countBook_wait2",
    //         data: {
    //             "section": section,
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function (response) {
    //             $('#count_wait').text('ยืนยันแล้ว รอ สสน. ' + response.book);
    //         }
    //     });
    // }

    // function noticount_succ() {
    //     section = $('#section').val();
    //     $.ajax({
    //         type: "post",
    //         url: "/countBook_succ2",
    //         data: {
    //             "section": section,
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function (response) {
    //             $('#count_succ').text('ดำเนินการสำเร็จ ' + response.book);
    //         }
    //     });
    // }

    function table() {
        section = $('#section').val();
        data_table = $('#dataTable').DataTable({
            "ordering": true,
            "bPaginate": true,
            "searching": true,
            "info": true,
            "responsive": true,
            "bFilter": false,
            "bLengthChange": true,
            "destroy": true,
            "pageLength": 25,
            "order": [
                [0, "desc"]
            ],
            "ajax": {
                "url": "dataTable/booking2",
                "method": "post",
                "data": {
                    "section": section,
                    "_token": "{{ csrf_token() }}",

                },
            },
            "rowCallback": function (row, data) {
                // console.log(data.);
                if (data.complete < data.now) {
                    $(row).addClass('Highlight');
                }
            },
            "columns": [{
                    "data": "ID"

                },
                {
                    "data": "section"

                },
                {
                    "data": "name"

                },
                {
                    "data": "pickup"

                },
                {
                    "data": null,
                    "render": function (data, type, full) {

                        if (full.equipment != null && full.equipment_type != null) {
                            return `${full.equipment} , ${full.equipment_type}`;
                        } else if (full.equipment != null) {
                            return `${full.equipment}`;
                        } else if (full.equipment_type != null) {
                            return `${full.equipment_type}`;
                        } else {
                            return ``;
                        }
                    }

                },
                {
                    "data": null,
                    "render": function (data, type, full) {

                        if (full.equipment_note != null && full.danger_note != null) {
                            return `${full.equipment_note} , ${full.danger_note}`;
                        } else if (full.equipment_note != null) {
                            return `${full.equipment_note}`;
                        } else if (full.danger_note != null) {
                            return `${full.danger_note}`;
                        } else {
                            return ``;
                        }
                    }
                },
                {
                    "data": "send"

                },
                {
                    "data": "datetime"

                },
                {
                    "data": null,
                    "render": function (data, type, full) {
                        if (full.note == 1) {
                            return `<span class="badge badge-success" style="font-size: 20px" >ปกติ</span>`;
                        } else if (full.note == 2) {
                            return `<span class="badge badge-warning" style="font-size: 20px" >ด่วน</span>`;
                        } else if (full.note == 3) {
                            return `<span class="badge badge-danger"style="font-size: 20px" >ด่วนที่สุด</span>`;
                        } else {
                            return ``;
                        }
                    }
                },
                {
                    "data": "time_in"

                },
                {
                    "data": "name_ssn"

                },
                {
                    "data": null,
                    "render": function (data, type, full) {
                        if (full.verify == 1) {
                            return `<span class="badge badge-pill badge-warning">ยืนยันแล้ว รอ สสน.</span>`;
                        } else if (full.verify == 0) {
                            return `<span class="badge badge-pill badge-danger">รอการยืนยัน</span>`;
                        } else if (full.verify == 2) {
                            return `<span class="badge badge-pill badge-success">ดำเนินการเสร็จสิ้น</span>`;
                        } else {
                            return ``;
                        }
                    }

                },
                // {
                //     "data": "time_out"

                // },
                {
                    "data": "ID",
                    "render": function (data, type, full) {
                        if (full.verify == 2) {
                            return `<span class="badge badge-pill badge-success">เสร็จสิ้นกระบวนการ</span>`
                            // return `<button type="button" class="btn btn-outline-danger">ลบ</button>`
                        } else if (full.verify == 1) {
                            return `<div class="row">
                                <a href="javascript:void(0)" type="button" data-toggle="modal"
                                    data-target="#note_modal" class="btn btn-outline-success" onclick="change1('${full.ID}');" >จบงาน</a>
                                </div>

                                <div class="row">
                                <a href="javascript:void(0)" type="button" data-toggle="modal"
                                    data-target="#sent_to_modal" class="btn btn-outline-danger" onclick="change('${full.ID}');" >ส่งต่อ</a>
                                </div>`

                        } else if (full.verify == 0) {
                            return `<div class="row">
                                <a href="javascript:void(0)" type="button"  class="btn btn-outline-danger" onclick="delete_soft('${full.ID}');" >ยกเลิก</a>
                                </div> `
                        } else {
                            return ``;
                        }
                    }

                },
            ],
        })
    }


    var id_hidden = null;
    var note_doc = null;


    function hide() {
        $('#note_modal').modal('toggle');
    }

    function change1(id) {
        console.log(id);
        $('#id_hidden').prop('value', id);
    }

    function change(id) {
        console.log(id);
        id_ = id;
        $.ajax({
            type: "get",
            url: "getEdit",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id_
            },
            success: function (res) {
                $('#hiddenEdit').val(res.ID);
                $('#name').val(res.name);
                $('#hn').val(res.hn);
                $('#case').val(res.case);
                $('#bednumber').val(res.bednumber);
                $('#section01').val(res.section);
                $('#equipment').val(res.equipment);
                $('#equipment_type').val(res.equipment_type);
                $('#note').val(res.note);
                $('#name_ssn').val(res.name_ssn);
                $('#shift').val(res.shift);
                $('#danger_note').val(res.danger_note);
                $('#equipment_note').val(res.equipment_note);

            }
        });
    }

    function clearModal() {
        $('#note_modal').modal('hide');
        $("#send").val('default').selectpicker("refresh");
        document.getElementById("sent_to_form").reset();
    }

    function clearModal2() {
        $('#sent_to_modal').modal('hide');
        $("#send").val('default').selectpicker("refresh");
        document.getElementById("sent_to_form").reset();
    }


    $(document).ready(function () {
        // $('#dataTable').DataTable();
        // var table = $('#dataTable').DataTable();
        // $('#min, #max').keyup(function () {
        //     table.draw();
        // });
        table();
        noticount();
        $("#name_ssn").hide();
        // setInterval(function () {
        //     data_table.ajax.reload();
        //     noticount();
        // }, 15000);
        // noticount_wait();
        // noticount_succ();
    });


    $('#note-form').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this)
        Swal.fire({
            title: 'คุณต้องการยืนยันใช่หรือไม่',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "confirm2",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                        if (response.status == true) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'ยืนยันเรียบร้อย',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                            })
                            data_table.ajax.reload();
                            clearModal();
                            // noticount();
                            // noticount_wait();
                            // noticount_succ();
                        } else {

                        }
                    }
                });
            }
        })

    });


    $('#sent_to_form').submit(function (e) {
        e.preventDefault();

        let formData = new FormData(this)
        Swal.fire({
            title: 'คุณต้องการยืนยันการส่งต่อใช่หรือไม่',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "sent_to",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                        if (response.status == true) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'ยืนยันเรียบร้อย',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                            })
                            clearModal2();
                            data_table.ajax.reload();
                            noticount();
                        } else {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'error',
                                title: 'ไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                            })
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                                // position: 'top-end',
                                icon: 'error',
                                title: 'ไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                        })
                    }
                });
            }
        })

    });


    function delete_soft(id) {
        get_id = id;
        Swal.fire({
            title: 'คุณต้องการยกเลิกใช่หรือไม่',
            text: "",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ตกลง'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "soft_delete2",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "get_id": get_id,
                    },
                    success: function (response) {
                        if (response.status == true) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'ยกเลิกเรียบร้อย',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                            })
                            data_table.ajax.reload();
                            noticount();
                            noticount_wait();
                            noticount_succ();
                        } else {

                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                                // position: 'top-end',
                                icon: 'error',
                                title: 'ไม่สามารถทำรายการได้ กรุณาลองใหม่อีกครั้ง',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                        })
                    }
                });
            }
        })
    }

</script>
