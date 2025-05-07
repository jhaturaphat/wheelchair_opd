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
$_SESSION['uri_line'] = $queryStrings
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>ตารางงานพนักงานเวรเปล. - Admin</title>
    <link rel="icon" type="image/png" href="img/logo.png">
    <link href="template/dist/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet"
        crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css"
        crossorigin="anonymous">
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css" crossorigin="anonymous"> --}}
    @include('admin.asset.js')
    <style>
        body {
            background-image: url("/img/bg.jpg");
            background-size: cover;
            height: 100 !important;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

    </style>
</head>

<body class="sb-nav-fixed">
    @include('admin.navbar')
    @include('admin.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="home">Dashboard</a></li>
                    <li class="breadcrumb-item active">ตาราง สสน.</li>
                </ol>
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row d-flex justify-content-between">
                            <div>
                                <i class="fas fa-table mr-1"></i>
                                ตารางงานพนักงานเวรเปล</div>
                            <div>

                                <span id="count_wait" class="badge badge-warning" style="font-size:16px;"></span>
                                <span id="count_succ" class="badge badge-success" style="font-size:16px;"></span>
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
                                        <th scope="col">ความเร่งด่วน</th>
                                        <th scope="col">วันเวลารับ</th>
                                        <th scope="col">เวลาสำเร็จ</th>
                                        <th scope="col">เวลาทังหมด</th>
                                        <th scope="col">สถานะการรับ</th>
                                        <th scope="col">action</th>
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
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="hide()">
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
                            onclick="hide()">ปิด</button>
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


        function noticount() {
            $.ajax({
                type: "post",
                url: "/countBook",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('#count').text('รอดำเนินการ ' + response.book);
                }
            });
        }

        function noticount_wait() {
            $.ajax({
                type: "post",
                url: "/countBook_wait",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('#count_wait').text('ยืนยันแล้ว รอ สสน. ' + response.book);
                }
            });
        }

        function noticount_succ() {
            $.ajax({
                type: "post",
                url: "/countBook_succ",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('#count_succ').text('ดำเนินการสำเร็จ ' + response.book);
                }
            });
        }


        $(document).ready(function () {
            setInterval(function () {
                console.log('reload');
                data_table.ajax.reload();
            }, 10000);
        });

        function hide() {
            $('#note_modal').modal('hide');
            document.getElementById("note-form").reset();
        }


        var data_table = $('#dataTable').DataTable({
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
                "url": "dataTable/data_custom",
                "method": "POST",
                "data": {
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
                    "data": null,
                    "render": function (data, type, full) {
                        return `<div class="row pl-2">
                                <a href="javascript:void(0)"<button type="button" data-toggle="modal" data-target="#note_modal" class="btn btn-outline-success" onclick="confirm('${full.ID}');" >ยืนยัน</button></a>
                                </div>`;
                        // <a href="javascript:void(0)" type="button" data-toggle="modal"
                        //     data-target="#note_modal" class="btn btn-outline-success" onclick="change1('${full.ID}');" >จบงาน</a>
                        // </div>
                        // if(full.pickup === 'เดินยา'){
                        //     return `<div class="row pl-2">
                        //         <a href="javascript:void(0)"<button type="button" class="btn btn-outline-success" onclick="confirm('${full.ID}');" >ยืนยัน</button></a>`;
                        // }else if(full.pickup === 'หน้าตึก'){
                        //     return `<div class="row pl-2">
                        //         <a href="javascript:void(0)"<button type="button" class="btn btn-outline-success" onclick="confirm('${full.ID}');" >ยืนยัน</button></a>`;
                        // }else if(full.pickup === 'เบิกเลือด'){
                        //     return `<div class="row pl-2">
                        //         <a href="javascript:void(0)"<button type="button" class="btn btn-outline-success" onclick="confirm('${full.ID}');" >ยืนยัน</button></a>`;
                        // }else if(full.pickup === 'อื่นๆ'){
                        //     return `<div class="row pl-2">
                        //         <a href="javascript:void(0)"<button type="button" class="btn btn-outline-success" onclick="confirm('${full.ID}');" >ยืนยัน</button></a>`;
                        // }else{
                        //     return ``;
                        // }
                    }

                },
            ],
        })

        function confirm(id) {
            $('#id_hidden').prop('value', id);
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
                        url: "confirm3",
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

    </script>
</body>

</html>
