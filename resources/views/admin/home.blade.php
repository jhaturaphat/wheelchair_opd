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

        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 320px;
            max-width: 800px;
            margin: 1em auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }

        input[type="number"] {
            min-width: 50px;
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

<body class="sb-nav-fixed">
    @include('admin.navbar')
    @include('admin.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <div class="card">
                    {{-- {{dd($months) }} --}}
                </div>
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="home">Dashboard</a></li>
                </ol>
                <div class="col-12 d-flex justify-content-end mb-4">
                    <input type="text" class="form-control col-2 form_datetime" placeholder="ป/ด/ว"
                        data-provide="datepicker" autocomplete="off" name="date_from" id="date_from">
                    <input type="text" class="form-control col-2 form_datetime" placeholder="ป/ด/ว"
                        data-provide="datepicker" autocomplete="off" name="date_to" id="date_to">
                    <button type="button" class="btn btn-primary" onclick="search_1();">ค้นหา</button>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-pie mr-1"></i>
                               กราฟสรุปหน่วยงานที่ใช้งาน
                            </div>
                            <div id="container" style="height: 400px"></div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-bar mr-1"></i>
                                กราฟสรุปยอดการประเมิน
                            </div>
                            <div id="container4" style="height: 400px"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-area mr-1"></i>
                                กราฟสรุปยอดการทำงานของพนักงานเวรเปล
                            </div>
                            <div class="card-body">
                                <div id="container3" style="height: 400px"></div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-chart-area mr-1"></i>
                                กราฟสรุปยอดการใช้งาน
                            </div>
                            <div class="card-body">
                                <div id="container1" style="height: 400px"></div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                {{-- <div class="col">
                    <div class="col">
                        <div class="card">
                            <div class="col-auto" id="container">
                            </div>
                        </div>
                        <br>
                    </div>
                    <br>
                    <div class="col">
                        <div class="card">
                            <div class="col-auto" id="container2">
                            </div>
                        </div>
                        <br>
                    </div> --}}
            </div>
        </main>
        @include('admin.footer')
    </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/1.2.1/js/dataTables.searchPanes.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="template/dist/js/scripts.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>

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
    <script src="template/dist/js/scripts.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.22/dataRender/datetime.js"></script>

    <script src="template/dist/js/scripts.js"></script>
    <script src="regis/js/bootstrap-datepicker.js"></script>
    <script src="regis/js/bootstrap-datepicker-thai.js"></script>
    <script src="regis/js/locales/bootstrap-datepicker.th.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script src="https://code.highcharts.com/highcharts.js"></script> --}}
    <script src="https://code.highcharts.com/highcharts.src.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        function search_1() {
            date_from = $('#date_from').val();
            date_to = $('#date_to').val();
            var sweet_loader =
            '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';
            $.ajax({
                method: "post",
                url: "search_1",
                data: {
                    "date_from": date_from,
                    "date_to": date_to,
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
                    var _ydata1 = res.months;
                    var _xdata1 = res.monthCount;
                    var sections = res.sections;
                    var rate = res.rate;
                    var rate_coun = res.rate_coun;

                    Highcharts.setOptions({
                        lang: {
                        thousandsSep: ','
                        }
                    });
                    swal.close();
                    Highcharts.chart('container3', {
                        chart: {
                            type: 'area'
                        },
                        title: {
                            text: 'สรุปยอดงานพนักงานเวรเปล'
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

                    Highcharts.chart('container1', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'สรุปยอดงานแต่ละเดือน'
                        },
                        xAxis: {
                            categories: _ydata1,
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'จำนวน'
                            }
                        },
                        series: [{
                            name: 'จำนวนงาน',
                            data: _xdata1
                        }]
                    });

                    Highcharts.chart('container', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'สรุปหน่วยงานที่ใช้งาน'
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer'
                            }
                        },
                        series: [{
                            name: 'จำนวนงาน',
                            colorByPoint: true,
                            data: sections
                        }]
                    });

                    Highcharts.chart('container4', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'สรุปยอดการประเมิน'
                        },
                        xAxis: {
                            categories: rate,
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'จำนวน'
                            }
                        },
                        series: [{
                            name: 'คะแนน',
                            data: rate_coun
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

        function search_2() {
            $.ajax({
                method: "post",
                url: "search_2",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function (res) {
                    var _ydata = res.months;
                    var _xdata = res.monthCount;

                    Highcharts.chart('container1', {
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
                }
            });

        }

    </script>
    <script>
        // Highcharts.chart('container', {
        //     chart: {
        //         type: 'column'
        //     },
        //     title: {
        //         text: 'สรุปยอดงาน'
        //     },
        //     subtitle: {
        //         text: 'พนักงานสนับสนุนเวรเปล'
        //     },
        //     xAxis: {
        //         categories: _ydata,
        //         crosshair: true
        //     },
        //     yAxis: {
        //         min: 0,
        //         title: {
        //             text: 'จำนวน'
        //         }
        //     },
        //     plotOptions: {
        //         column: {
        //             pointPadding: 0.2,
        //             borderWidth: 0
        //         }
        //     },
        //     series: [{
        //         name: 'จำนวนงาน',
        //         data: _xdata

        //     }]
        // });

        // Highcharts.chart('container2', {
        //     chart: {
        //         type: 'area'
        //     },
        //     title: {
        //         text: 'สรุปยอดงาน'
        //     },
        //     subtitle: {
        //         text: 'พนักงานสนับสนุนเวรเปล'
        //     },
        //     xAxis: {
        //         categories: _ydata,
        //     },
        //     yAxis: {
        //         min: 0,
        //         title: {
        //             text: 'จำนวน'
        //         }
        //     },
        //     series: [{
        //         name: 'จำนวนงาน',
        //         data: _xdata
        //     }]
        // });

        // Highcharts.chart('container3', {
        //     chart: {
        //         plotBackgroundColor: null,
        //         plotBorderWidth: null,
        //         plotShadow: false,
        //         type: 'pie'
        //     },
        //     title: {
        //         text: 'Browser market shares in January, 2018'
        //     },
        //     plotOptions: {
        //         pie: {
        //             allowPointSelect: true,
        //             cursor: 'pointer'
        //         }
        //     },
        //     series: [{
        //         name: 'จำนวนงาน',
        //         colorByPoint: true,
        //         data: _ydata
        //     }]
        // });

    </script>


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
            $.ajax({
                type: "post",
                url: "/countBook_sum",
                data: {
                    "date_from": date_from,
                    "date_to": date_to,
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('#count_succ').text('ดำเนินการสำเร็จ ' + response.book);
                }
            });
        }

        $('.form_datetime').datepicker({
            autoclose: 1,
            format: 'yyyy-mm-dd'
        });

    </script>



    <script>
        $(document).ready(function () {
            // table_load();
            // noticount_succ();
            // search_2();
            search_1();
        });

    </script>

</html>
