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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Tables - Admin</title>
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
    @include('admin.navbar')
    @include('admin.sidenav')
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
                                ตารางคำขอพนักงานสนับสนุน(สสน)</div>
                            <div>
                                <span id="count" class="badge badge-danger" style="font-size:16px;"></span>
                                <!-- <span id="count_wait" class="badge badge-warning" style="font-size:16px;"></span> -->
                                <span id="count_succ" class="badge badge-success" style="font-size:16px;"></span>
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
                                        <!-- <th>#</th> -->
                                        <th scope="col">ลำดับ</th>
                                        <th scope="col">แผนก</th>
                                        <th scope="col">ชื่อ-นามสกุล คนไข้</th>
                                        <th scope="col">สถานที่รับ</th>
                                        <th scope="col">อุปกรณ์</th>
                                        <th scope="col">หมายเหตุ</th>
                                        <th scope="col">สถานที่ส่ง</th>
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
</body>

</html>
<form id="submit-form" method="post">
    @csrf
    <div class="modal fade" data-keyboard="false" data-backdrop="static" id="exampleModalCenter" tabindex="-1"
        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">จ่ายงาน</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="">ผู้จ่ายงาน</label>
                    <input class="form-control" type="text" name="ssn_assign" id="ssn_assign"
                        value="{{ Auth::user()->name }}" readonly>
                    <hr>
                    <label for="">เลือกพนักงาน สสน.</label>
                    <select class="selectpicker form-control" data-size="15" data-live-search="true"
                        data-dropup-auto="true" title="--เลือกพนักงาน--" id="token_id" name="token_id"
                        data-search="true" theme="google" style="margin-top: 5px;" required>
                        @foreach($token as $item)
                            <option value='{{ $item->id }}'>{{ $item->ssn_name }}</option>
                        @endforeach
                    </select>
                    <hr>
                    <label for="">เลือกเวร</label>
                    <select class="form-control" id="shift" name="shift" data-search="true" theme="google"
                        style="margin-top: 5px;" required>
                        <option selected hidden value=''>--เลือกเวร--</option>
                        <option value="เช้า"> เช้า</option>
                        <option value="บ่าย"> บ่าย</option>
                        <option value="ดึก"> ดึก</option>
                    </select>
                    <input type="hidden" name="idHidden" id="id-hidden">
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
                $('#count').text('รอการยืนยัน ' + response.book);
            }
        });
    }

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
            "url": "dataTable/booking",
            "method": "POST",
            "data": {
                "_token": "{{ csrf_token() }}",
            },
        },
        "rowCallback": function (row, data) {
            // console.log(data.);
            if (data.verify == 0 && data.complete < data.now) {
                $(row).addClass('Highlight');
            }
        },
        "columns": [              

            {
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
                        return `<span class="badge badge-pill badge-warning">ยืนยันแล้ว รอ สสน.</span>`;
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
                    if (full.verify == 1) {
                        return `<div class="row pl-2">
                                <a href="javascript:void(0)"><button class="btn btn-outline-danger" onclick="delete_soft('${full.ID}');" >ยกเลิก</button></a>
                                </div>

                                <div class="row pl-2">
                                <a href="javascript:void(0)"><button type="button" data-toggle="modal"
                                data-target="#exampleModalCenter" class="btn btn-outline-warning" onclick="change('${full.ID}');" >แก้ไข</button></a>
                                </div>`
                        // return `<button type="button" class="btn btn-outline-danger">ลบ</button>`
                    } else if (full.verify == 0) {

                        return `<div class="row pl-2">
                                <a href="javascript:void(0)"<button type="button" data-toggle="modal"
                                data-target="#exampleModalCenter"  class="btn btn-outline-success" onclick="change('${full.ID}');" >ยืนยัน</button></a>

                                <a href="javascript:void(0)"><button class="btn btn-outline-danger" onclick="delete_soft('${full.ID}');" >ยกเลิก</button></a>
                                </div>`

                    } else if (full.verify == 2) {
                        return `<div class="row pl-2">
                                <a href="javascript:void(0)"><button class="btn btn-outline-danger" onclick="delete_soft('${full.ID}');" >ยกเลิก</button></a>
                                </div>`


                    } else {
                        return ``;
                    }
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
            .column(10, {
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
        // And map it to the format highcharts uses
        // return $.map(counts, function (val, key) {
        //     // console.log(key);
        //     return {
        //         name: key,
        //         y: val,
        //     };
        //     // console.log(counts);
        // });

    }


    $(document).ready(function () {
        // console.log(salary);
        noticount();
        // noticount_wait();
        noticount_succ();
        setInterval(function () {
            noticount();
            // noticount_wait();
            noticount_succ();
            console.log('reload');
            table.ajax.reload();
        }, 15000);
    });

    function reload() {
        table.ajax.reload();
        noticount();
        // noticount_wait();
        noticount_succ();
    }

    function hide() {
        $('#exampleModalCenter').modal('hide');
        $('#id-hidden').prop('value', null);
        $('#id-hidden2').prop('value', null);
        $('#submit-form').find('select').val('');
        $('#id-hidden2').find('select').val('');
        // $('#token_id').prop('value',null);
        $('.selectpicker').selectpicker('refresh');
    }

    function change(id) {
        console.log(id);
        $('#id-hidden').prop('value', id);
    }

    function change2(id) {
        console.log(id);
        $('#id-hidden2').prop('value', id);
    }

    function clearModal() {
        $('#id-hidden').prop('value', null);
        $('#id-hidden2').prop('value', null);
        $('#submit-form').find('select').val('');
        $('#id-hidden2').find('select').val('');
        // $('#token_id').prop('value',null);
        $('.selectpicker').selectpicker('refresh');
    }


    //จ่ายงาน
    $('#submit-form').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
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
                    url: "confirm",
                    processData: false,
                    contentType: false,
                    data: formData,
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
                            reload();
                            hide();
                            // clearModal();
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
                            // noticount_wait();
                            noticount_succ();
                        } else {

                        }
                    }
                });
            }
        })
    }

</script>
