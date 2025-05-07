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
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>ตารางบันทึกงานของ{{ Auth::user()->service }} - Admin</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.datatables.net/searchpanes/1.2.1/css/searchPanes.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css">
    <link href="template/dist/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="dist/css/bootstrap-select.css">

    @include('admin.asset.js')
    <style>
        body {
            background-image: url("/img/bg.jpg");
            background-size: cover;
            height: 100 !important;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        td {

            text-align: center;
            vertical-align: middle;
        }

        table#dataTable.dataTable tbody tr.Highlight {
            background-color: rgb(243, 150, 138);
        }

    </style>

</head>

<body class="sb-nav-fixed sb-sidenav-toggled">
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
                        <a class="nav-link" href="drive_refer_from">
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
                <div class="container-fluid">
                    <h1 class="mt-4">Tables</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="home">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tables</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="row d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-table mr-1"></i>
                                    ตารางคำขอ{{ Auth::user()->service }}</div>
                                <div>
                                    {{-- <span id="count_wait" class="badge badge-warning" style="font-size:16px;"></span>
                                <span id="count_succ" class="badge badge-success" style="font-size:16px;"></span> --}}
                                    {{-- <span type="button" class="btn btn-info" style="font-size:16px;" id="reload" onclick="reload()"
                                        >รีโหลด</span> --}}
                                </div>
                            </div>
                        </div>
                        {{-- {{dd($data->ID) }} --}}
                        <div class="card-body">
                            <div id=chart></div>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th scope="col">ลำดับ</th>
                                            <th scope="col">แผนก</th>
                                            <th scope="col">สถานที่รับ</th>
                                            <th scope="col">หมายเหตุ</th>
                                            <th scope="col">วันเวลารับ</th>
                                            <th scope="col">ความเร่งด่วน</th>
                                            <th scope="col">เวลารับเรื่อง</th>
                                            <th scope="col">ชื่อ สนน.</th>
                                            <th scope="col">เวร</th>
                                            <th scope="col">สถานะการรับ</th>
                                            <th scope="col">function</th>
                                            {{-- <th scope="col" >ทดสอบเตือน</th> --}}
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                        <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>

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

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // function noticount_wait() {
    //     $.ajax({
    //         type: "post",
    //         url: "/countBook_wait",
    //         data: {
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function (response) {
    //             $('#count_wait').text('ยืนยันแล้ว รอ สสน. ' + response.book);
    //         }
    //     });
    // }

    // function noticount_succ() {
    //     $.ajax({
    //         type: "post",
    //         url: "/countBook_succ",
    //         data: {
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function (response) {
    //             $('#count_succ').text('ดำเนินการสำเร็จ ' + response.book);
    //         }
    //     });
    // }


    var table = $('#dataTable').DataTable({
        "ordering": true,
        "bPaginate": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "bFilter": false,
        "bLengthChange": true,
        "destroy": true,
        "pageLength": 100,
        "order": [
            [0, "desc"]
        ],
        "ajax": {
            "url": "refer_tables",
            "method": "POST",
            "data": {
                "_token": "{{ csrf_token() }}",
            },
        },
        "rowCallback": function (row, data) {
            // console.log(data.);
            if (data.verify == 1 && data.complete < data.now) {
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
                "data": "pickup"

            },
            {
                "data": "other"

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
                        // return `<span class="badge badge-danger"style="font-size: 20px" >ด่วนที่สุด</span>`;
                        return `<img src="img/giphy.gif" width="100" class="d-inline-block align-top" alt="">`;
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
                "data": "shift"

            },
            {
                "data": null,
                "render": function (data, type, full) {
                    if (full.verify == 1) {
                        return `<span class="badge badge-pill badge-warning">รอพนักงานมาถึง</span>`;
                    } else if (full.verify == 0) {
                        return `<span class="badge badge-pill badge-danger">รอการยืนยัน</span>`;
                    } else if (full.verify == 2) {
                        return `<span class="badge badge-pill badge-success">ดำเนินการเสร็จสิ้น</span>`;
                    } else {
                        return ``;
                    }
                }
            }, {
                "data": "ID",
                "render": function (data, type, full) {
                    return `<div class="row pl-2">
                        <a href="javascript:void(0)"<button type="button" data-toggle="modal" data-target="#note_modal" class="btn btn-outline-success" onclick="confirm('${full.ID}');" >ยืนยัน</button></a>

                        <a href="javascript:void(0)"><button class="btn btn-outline-danger" onclick="delete_soft('${full.ID}');" >ยกเลิก</button></a>
                        </div>
                        `;
                }

            }
        ],
    });


    Highcharts.setOptions({
        lang: {
            thousandsSep: ','
        }
    });

    var fulldata = chartData(table);
    // console.log(Object.keys(fulldata));
    var container = $('<div class="col-auto"><div/>').insertBefore(table.table().container());
    var chart = Highcharts.chart(container[0], {
        title: {
            text: 'กราฟบันทึกจำนวนงาน',
        },
        xAxis: {
            categories: Object.keys(fulldata),
        },
        yAxis: {
            min: 0,
            title: {
                text: 'จำนวน'
            }
        },
        series: [{
            type: 'column',
            name: 'จำนวน',
            data: Object.values(fulldata),
        }]
    });


    // On each draw, update the data in the chart
    table.on('draw', function () {
        var fulldata = chartData(table);
        chart.axes[0].categories = Object.keys(fulldata);
        chart.series[0].setData(Object.values(fulldata));
    });


    function chartData(table) {
        var counts = {};
        // Count the number of entries for each position
        table
            .column(7, {
                search: 'applied'
            })
            .data()
            .each(function (val) {
                if (counts[val]) {
                    counts[val] += 1;
                } else {
                    counts[val] = 1;
                }
            });

        console.log(counts);
        return counts;
    }

    function confirm(id) {
        $('#id_hidden').prop('value', id);
    }


    $(document).ready(function () {
        // console.log(salary);
        noticount_wait();
        noticount_succ();
        setInterval(function () {
            noticount_wait();
            noticount_succ();
            console.log('reload');
            table.ajax.reload();
        }, 15000);
    });

    function reload() {
        table.ajax.reload();
        noticount_wait();
        noticount_succ();
    }

    function hide() {
        $('#note_modal').modal('hide');
        document.getElementById("note-form").reset();
    }

    function change(id) {
        console.log(id);
        $('#id-hidden').prop('value', id);
    }

    function change2(id) {
        console.log(id);
        $('#id-hidden2').prop('value', id);
    }

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
                    url: "conFirm_drive",
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
                            table.ajax.reload();
                            hide();
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

    function delete_soft(id) {
        get_id = id;

        Swal.fire({
            title: 'คุณต้องการลบใช่หรือไม่',
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
                    url: "soft_delete",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "get_id": get_id,
                    },
                    success: function (response) {
                        if (response.status == true) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'ลบเรียบร้อย',
                                showConfirmButton: false,
                                width: 600,
                                higth: 100,
                                timer: 2000
                            })
                            table.ajax.reload();
                            noticount();
                            noticount_wait();
                            noticount_succ();
                        } else {

                        }
                    }
                });
            }
        })
    }

</script>
