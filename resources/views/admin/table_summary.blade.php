<?php
define('CLIENT_ID', env('CLIENT_ID')); // เปลี่ยนเป็น Client ID ของเรา
define('LINE_API_URI', env('LINE_API_URI'));
define('CALLBACK_URI', env('CALLBACK_URI')); // เปลี่ยนให้ตรงกับ Callback URL ของเรา
$queryStrings = [
    'response_type' => 'code',
    'client_id' => CLIENT_ID,
    'redirect_uri' => CALLBACK_URI,
    'scope' => 'notify',
    'state' => str_pad(mt_rand(1,99999999),8,'0',STR_PAD_LEFT)
];
$queryString = LINE_API_URI . http_build_query($queryStrings);
$_SESSION['uri_line'] = $queryString
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>ตารางสรุปผลงานพนักงานเวรเปล - Admin</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="template/dist/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css"
        crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/1.2.1/css/searchPanes.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
    <link type="text/css" href="regis/css/datepicker.css" rel="stylesheet">
    <style>
        body {
            background-image: url("/img/bg.jpg");
            background-size: cover;
            height: 100 !important;
            background-repeat: no-repeat;
            background-attachment: fixed;
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

    @include('admin.asset.js')
</head>

<body class="sb-nav-fixed sb-sidenav-toggled">
    @include('admin.navbar')
    @include('admin.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="home">Dashboard</a></li>
                    <li class="breadcrumb-item active">สรุปผลงาน</li>
                </ol>
                <div class="col-12 d-flex justify-content-end mb-4">
                    <select class="form-control col-1" name="score_from" id="score_from">
                        <option selected hidden value=''>-เลือกคะแนน-</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <select class="form-control col-1" name="score_to" id="score_to">
                        <option selected hidden value=''>-เลือกคะแนน-</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>&nbsp;
                    <select class="form-control col-1" name="shift" id="shift">
                        <option selected hidden value=''>-เวร-</option>
                        <option value="เช้า">เช้า</option>
                        <option value="บ่าย">บ่าย</option>
                        <option value="ดึก">ดึก</option>
                    </select>&nbsp;
                    <input type="text" class="form-control col-2 form_datetime" placeholder="ป/ด/ว"
                        data-provide="datepicker" autocomplete="off" name="date_from" id="date_from">
                    <input type="text" class="form-control col-2 form_datetime" placeholder="ป/ด/ว"
                        data-provide="datepicker" autocomplete="off" name="date_to" id="date_to">
                    <button type="button" class="btn btn-primary" onclick="table_load();search_1();">ค้นหา</button>
                </div>
                <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar mr-1"></i>
                            กราฟสรุปผลงานพนักงานเวรเปล
                        </div>
                        <div class="card-body">
                            <div id="container2" style="height: 400px"></div>
                        </div>
                    <br>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <i class="fas fa-table mr-1"></i>
                                ตารางงานสรุปผลงานพนักงานเวรเปล</div>
                            <div class="form-inline">
                                <span id="count_succ" class="badge badge-success" style="font-size:16px;"></span>&emsp;

                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered " id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col">ลำดับ</th>
                                        <th scope="col">ชื่อ สสน.</th>
                                        <th scope="col">เวร</th>
                                        <th scope="col">สถานที่รับ</th>
                                        <th scope="col">อุปกรณ์</th>
                                        <th scope="col">หมายเหตุ</th>
                                        <th scope="col">สถานที่ส่ง</th>
                                        <th scope="col">case</th>
                                        <th scope="col">ความเร่งด่วน</th>
                                        <th scope="col">วันเวลารับ</th>
                                        <th scope="col">เวลาสำเร็จ</th>
                                        <th scope="col">เวลาทังหมด</th>
                                        <th scope="col">คะแนน</th>
                                        <th scope="col">หมายเหตุ</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

    </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/1.2.1/js/dataTables.searchPanes.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="template/dist/js/scripts.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
    {{-- <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.22/dataRender/datetime.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="regis/js/bootstrap-datepicker.js"></script>
    <script src="regis/js/bootstrap-datepicker-thai.js"></script>
    <script src="regis/js/locales/bootstrap-datepicker.th.js"></script>
    <script src="https://code.highcharts.com/highcharts.src.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>
    <script>
        function noticount_succ() {
            date_from = $('#date_from').val();
            date_to = $('#date_to').val();
            score1 = $('#score_from').val();
            score2 = $('#score_to').val();
            shift = $('#shift').val();
            $.ajax({
                type: "post",
                url: "/countBook_sum",
                data: {
                    "date_from": date_from,
                    "date_to": date_to,
                    "score1" : score1,
                    "score2" : score2,
                    "shift" : shift,
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('#count_succ').text('ดำเนินการสำเร็จ ' + response.book);
                }
            });
        }

        $('.form_datetime').datepicker({
            autoclose: 1,
            format: 'yyyy-mm-dd',
            endDate: new Date()
        });

    </script>



    <script>
        function search_1() {
            date_from = $('#date_from').val();
            date_to = $('#date_to').val();
            score1 = $('#score_from').val();
            score2 = $('#score_to').val();
            shift = $('#shift').val();
            var sweet_loader =
            '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
            $.ajax({
                method: "post",
                url: "search_2",
                data: {
                    "date_from": date_from,
                    "date_to": date_to,
                    "score1" : score1,
                    "score2" : score2,
                    "shift" : shift,
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
                success: function (res) {
                    var _ydata = res.name_ssns;
                    var _xdata = res.name_ssnCount;
                    swal.close();
                    Highcharts.setOptions({
                        lang: {
                        thousandsSep: ','
                        }
                    });
                    Highcharts.chart('container2', {
                        chart: {
                            type: 'area'
                        },
                        title: {
                            text: 'สรุปยอดงาน'
                        },
                        subtitle: {
                            text: 'พนักงานสนับสนุนเวรเปล'
                        },
                        xAxis: {
                            categories: _ydata,
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'จำนวน'
                            }
                        },
                        series: [{
                            name: 'จำนวนงาน',
                            data: _xdata
                        }]
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire(
                        'เกิดข้อผิดพลาด',
                        'กรุณาเลือกวันที่ให้ถูกต้อง',
                        'error'
                        )
                }
            });

        }

        $(document).ready(function () {
            table_load();
            search_1();
            noticount_succ();
        });

        function table_load() {
            noticount_succ();
            date_from = $('#date_from').val();
            date_to = $('#date_to').val();
            score1 = $('#score_from').val();
            score2 = $('#score_to').val();
            shift = $('#shift').val();
            table = $('#dataTable').DataTable({
                "ordering": true,
                "bPaginate": true,
                "searching": true,
                "info": true,
                "responsive": true,
                "bFilter": false,
                "bLengthChange": true,
                "destroy": true,
                "pageLength": 25,
                "dom": 'Bfrtip',
                "buttons": ['excel'],
                "order": [
                    [0, "desc"]
                ],

                "ajax": {
                    "url": "dataTable/data_sum",
                    "method": "POST",
                    "data": {
                        "date_from": date_from,
                        "date_to": date_to,
                        "score1" : score1,
                        "score2" : score2,
                        "shift" : shift,
                        "_token": "{{ csrf_token() }}",
                    },
                },

                "columns": [{
                        "data": "ID"

                    },
                    {
                        "data": "name_ssn"

                    },
                    {
                        "data": "shift"

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
                        "data": "case"

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
                        "data": "time_out"

                    },
                    {
                        "data": "time_total"

                    },
                    {
                        "data": "rate"

                    },
                    {
                        "data": "note_doc"

                    },
                ],
            })

        }

    </script>

</html>
