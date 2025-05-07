<?php
define('CLIENT_ID', 'BYX77OURfPg1EpMpDkbRyJ'); // เปลี่ยนเป็น Client ID ของเรา
define('CLIENT_SECRET', 'Unug3wNvRH3y02UhjmVo0EqfDlvMHjIxY0mQp1RWtZx'); // เปลี่ยนเป็น Client Secret ของเรา
define('LINE_API_URI', 'https://notify-bot.line.me/oauth/token');
define('CALLBACK_URI', 'http://opd.detudomhospital.local/notify');
parse_str($_SERVER['QUERY_STRING'], $queries);
$fields = [
    'grant_type' => 'authorization_code',
    'code' => $queries['code'],
    'redirect_uri' => CALLBACK_URI,
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET
];

try {

} catch(Exception $e) {
    var_dump($e);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>สมัครสมาชิกรับการแจ้งเตือน | โรงพยาบาลสมเด็จพระยุพราชเดชอุดม</title>
    @include('admin.asset.css')
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js" crossorigin="anonymous">
    </script>
    {{-- <link href="datetime/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen"> --}}
    <link href="datetime/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <style>
        body {
            font-family: Kanit;
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

        .cs {
            background-color: #ebf0fb;
        }

    </style>
    @include('admin.asset.jconcss')
</head>

<body>

    <body class="sb-nav-fixed" >
        <nav class="navbar navbar-expand-lg  navbar-dark bg-dark">
            <a class="navbar-brand"
            @if( Auth::user()->service == 'เคลื่อนย้ายผู้ป่วย')
                href="home"
            @else
                href="drive_refer_from"
            @endif>
                <img src="img/logo.png" width="35" height="30" class="d-inline-block align-top" alt="">
                ระบบสมัครสมาชิกรับการแจ้งเตือน
            </a>
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
                            <div class='card-body'>
                                <div class='row form-group'>
                                    <div class='col'>
                                        <label class='title' id="patient_name_label">1) เลขบัตร ปชช.</label>
                                        <div class="col-lg">
                                            <input type="text" pattern="[0-9]*" inputmode="numeric"
                                                class="form-control " id="ssn_id" name="ssn_id"
                                                placeholder="เลขบัตรประชาชน 13 หลัก"
                                                onkeypress="return forceNumberKey(event)" maxlength="13" minlength="13"
                                                required="">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class='row form-group'>
                                    <div class='col'>
                                        <label class='title' id="patient_instype_label">2) ชื่อ-นามสุกล</label>
                                        <div class="col-lg">
                                            <input type="text" class="form-control " id="ssn_name" name="ssn_name"
                                                placeholder="กรุณากรอก..." required>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                {{-- <div class='row form-group'>
                                    <div class='col'>
                                        <label class='title' id="patient_place_label">4) E-Mail </label>
                                        <div class="col-lg">
                                            <input type="email" class="form-control " id="ssn_email" name="ssn_email" placeholder="Email..." >
                                        </div>
                                    </div>
                                </div>
                                <hr> --}}
                                <div class='row form-group'>
                                    <div class='col'>
                                        <label class='title' id="patient_instype_label">3) เบอร์โทรศัพท์</label>
                                        <div class='col'>
                                            <input type="text" pattern="[0-9]*" inputmode="numeric"
                                                class="form-control " id="ssn_tel" name="ssn_tel"
                                                placeholder="ตัวเลข 0 - 9 เท่านั้น..."
                                                onkeypress="return forceNumberKey(event)" maxlength="10" minlength="9">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class='row form-group'>
                                    <div class='col-auto'>
                                        <label class='title' id="patient_admit_label">4) ประเภทงาน
                                        </label>
                                        <div class="col">
                                            <select name="role" id="role" class="form-control">
                                                <option selected hidden value=''>--เลือกประเภทงาน--</option>
                                                <option value="เคลื่อนย้ายผู้ป่วย">เคลื่อนย้ายผู้ป่วย</option>
                                                <option value="ตามรับยา">ตามรับยา</option>
                                                <option value="พนักงานขับรถ">พนักงานขับรถ</option>
                                                <option value="พยาบาลรีเฟอร์">พยาบาลรีเฟอร์</option>
                                            </select>
                                        </div>
                                     </div>
                                </div>
                                <div class="row">
                                    <div class='col'>
                                        <div class="col-lg">
                                            <input type="hidden" class="form-control" id="ssn_token" name="ssn_token"
                                                value="<?php
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, LINE_API_URI);
                                                curl_setopt($ch, CURLOPT_POST, count($fields));
                                                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                                $res = curl_exec($ch);
                                                curl_close($ch);
                                                $json = json_decode($res, true);
                                                $access_token = $json['access_token'];
                                                echo $access_token; ?>"
                                                 readonly>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class='row form-group'>
                                    <div class="col">
                                        <div class="card border-primary mb-3" id="card">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="ยินยอม"
                                                        id="consent" name="consent" onclick="myFunction()" required />
                                                    <label class="form-check-label" for="flexCheckDefault">
                                                        ข้าพเจ้ายินยอมเปิดเผยข้อมูลส่วนบุคคลของข้าพเจ้าเพื่อรับบริการกับโรงพยาบาลสมเด็จพระยุพราชเดชอุดม
                                                        และข้าพเจ้าขอยืนยันว่าข้อมูลที่ให้กับโรงพยาบาลถูกต้องตรงตามความเป็นจริงทุกประการ
                                                    </label>
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
                                        <button type="reset" class="btn btn-danger w-100"><span
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
                <div class="row mt-3 mb-3">
                    <!--small-->
                    <div class='col text-center' style="padding-bottom: 20px;">
                        <i class='far fa-copyright'></i> <script>
                            document.write(new Date().getFullYear())
                        </script>
                        <a target='_blank' class='text-dark'>รพ.อำนาจเจริญ</a>
                    </div>
                </div>
            </div>
            <!-- root container -->
        </form>
        @include('admin.asset.js')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        @include('admin.asset.jcon')
        <script type="text/javascript" src="datetime/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="datetime/bootstrap/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
        <script type="text/javascript" src="datetime/bootstrap/locales/bootstrap-datetimepicker.th.js" charset="UTF-8">
        </script>





        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        </script>

        <script>
            $('#myform').submit(function (submit) {
                submit.preventDefault();
                let formData = new FormData(this)
                $.ajax({
                    type: "post",
                    url: "save_token",
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
                        else if ($.trim($("#patient_fname").val()) == "") control_top = $(
                        "#patient_name_label");
                        else if ($.trim($("#patient_lname").val()) == "") control_top = $(
                        "#patient_name_label");

                        else if (!$("input[name='instype']:checked").val()) control_top = $(
                            "#patient_instype_label");

                        else if ($.trim($("#date_admit").val()) == "") control_top = $("#patient_admit_label");

                        else if ($.trim($("#patient_cur").val()) == "") control_top = $("#patient_place_label");

                        else if (!$("input[name='room']:checked").val()) control_top = $(
                            "#patient_bookroom_label");

                        else if ($.trim($("#book_tname").val()) == "") control_top = $("#book_name_label");
                        else if ($.trim($("#book_fname").val()) == "") control_top = $("#book_name_label");
                        else if ($.trim($("#book_lname").val()) == "") control_top = $("#book_name_label");


                        else if ($.trim($("#book_tel").val()) == "") control_top = $("#book_tel_label");

                        errorMsg("โปรดระบุข้อมูลให้ครบทุกข้อ", control_top);

                    } else {
                        //alert("submit");
                        confirmMsg("ยืนยันการสมัคร", function () {
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
                            text: "ตกลง",
                            btnClass: 'bg-white text-success',
                            action: function () {
                                //document.getElementById("myform").submit();
                                successMsg("สมัครสำเร็จ");
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
                                window.location.reload();
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

            function myFunction() {
                var checkBox = document.getElementById("consent");
                var card = document.getElementById("card");
                if (checkBox.checked == true) {
                    card.classList = "card border-primary mb-3 cs";
                } else {
                    card.classList = "card border-primary mb-3";
                }
            }

        </script>

    </body>

</html>
