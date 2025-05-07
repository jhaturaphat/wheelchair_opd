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

    <style>
        body {
            font-family: Sarabun;
            background-size:cover;
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
                        <a class="dropdown-item" href="Showdata">ดูตารางการขอ</a>


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
                    <input class="form-control" type="hidden" id="send" name="send"
                        value="{{ Auth::user()->depart }}">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-table mr-1"></i>
                                    ตารางแสดงข้อมูลการขอใช้พนักงานที่ดำเนินการสำเร็จ(หน่วยงาน)</div>
                                <div>
                                    {{-- <span id="count" class="badge badge-danger" style="font-size:16px;"></span> --}}
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
                                            <th scope="col" width="1">ลำดับ</th>
                                            <th scope="col" width="140">แผนก</th>
                                            <th scope="col" width="150">ชื่อ-นามสกุล คนไข้</th>
                                            <th scope="col" width="140">สถานที่รับ</th>
                                            <th scope="col" width="50">อุปกรณ์</th>
                                            <th scope="col" width="100">หมายเหตุ</th>
                                            <th scope="col" width="50">สถานที่ส่ง</th>
                                            <th scope="col" width="10">ความเร่งด่วน</th>
                                            <th scope="col" width="30">เวลารับเรื่อง</th>
                                            <th scope="col" width="100">ชื่อ สสน.</th>
                                            <th scope="col" width="20">สถานะการรับ</th>
                                            <th scope="col" width="70">เวลาสำเร็จ</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class='col text-center'>
                            <i class='far fa-copyright'></i> <script>
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

</body>

</html>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
</script>
<script src="template/dist/js/scripts.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
{{-- <script src="assets/demo/datatables-demo.js"></script>
            <script src="template/dist/assets/demo/datatables-demo.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript" src="datetime/bootstrap/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="datetime/bootstrap/locales/bootstrap-datetimepicker.th.js" charset="UTF-8"></script>





<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function reload() {
        data_table.ajax.reload();
        noticount();
    }


    function noticount() {
        section = $('#section').val();
        var sweet_loader =
            '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
        $.ajax({
            type: "post",
            url: "/countBook2",
            data: {
                "section" : section,
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

    function noticount_wait() {
        section = $('#section').val();
        $.ajax({
            type: "post",
            url: "/countBook_wait2",
            data: {
                "section" : section,
                "_token": "{{ csrf_token() }}"
            },
            success: function (response) {
                $('#count_wait').text('ยืนยันแล้ว รอ สสน. ' + response.book);
            }
        });
    }

    function noticount_succ() {
        section = $('#section').val();
        $.ajax({
            type: "post",
            url: "/countBook_succ2",
            data: {
                "section" : section,
                "_token": "{{ csrf_token() }}"
            },
            success: function (response) {
                $('#count_succ').text('ดำเนินการสำเร็จ ' + response.book);
            }
        });
    }

    function table(){
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
                "url": "dataTable/booking_succ",
                "method": "post",
                "data": {
                    "section" : section,
                    "_token": "{{ csrf_token() }}",

                },
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
                {
                    "data": "time_out"

                },

            ],
        })
    }


    // $.fn.dataTable.ext.search.push(
    //     function (settings, data, dataIndex) {
    //         var send = $('#send').val();
    //         var section = $('#section').val();
    //         var section_q = (data[1]) || 0; // use data for the age column
    //         var send_q = (data[6]) || 0;

    //         if ((section == section_q ||
    //             send == send_q)) {
    //             return true;
    //         }
    //         return false;
    //     }
    // );

    $(document).ready(function () {
        table();
        noticount();
        // $('#min, #max').keyup(function () {
        //     table.draw();
        // });
    });

</script>
