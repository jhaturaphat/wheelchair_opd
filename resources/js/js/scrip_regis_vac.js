
    // function consent2() {
    //     if ($('#conse').checked(true)) {

    //     }
    // }





    // function location_limit(keyreset) {
    //     document.getElementById("location").value = '';
    //     if (keyreset == 'บัตรประชาชน') {
    //         document.getElementById('showalertidpp').innerHTML = "ตัวเลข 13 หลัก";
    //         document.getElementById("idps").disabled = false;
    //     } else
    //     if (keyreset == 'พาสปอร์ต') {
    //         document.getElementById('showalertidpp').innerHTML = "(A-Z)+(A-Z หรือ 0-9)";
    //         document.getElementById("idps").disabled = false;
    //     } else {
    //         document.getElementById('showalertidpp').innerHTML = "เลือกบัตร !";
    //         document.getElementById("idps").disabled = true;
    //     }
    // }

    // function currentDate() {
    //     var currentDate = new Date();
    //     $("#vcdate").datepicker("setDate", currentDate);
    // }

    // $('#location').on('change', function () {
    //     currentDate();
    //     // var currentDate = new Date();
    //     // $('#vcdate').prop('val',currentDate);
    // });


    // $('#vcdate').change(function () {
    //     location = $('#location').val();
    //     if ($(this).val() != '') {
    //         var location = $('#location').val();
    //         var vcdate = $(this).val();
    //         $.ajax({
    //             type: "post",
    //             url: "{{ route('check.count') }}",
    //             data: {
    //                 "location": location,
    //                 "vcdate": vcdate,
    //                 "_token": "{{ csrf_token() }}"
    //             },
    //             success: function (response) {
    //                 console.log(response.post);
    //                 if (response.post >= 100) {
    //                     $('#count').html('<span style="color: red">เต็มแล้ว</span>');
    //                     $('#succ').hide();

    //                 } else {
    //                     $('#count').text(response.post);
    //                     $('#succ').show();
    //                 }
    //             }
    //         });
    //     }
    // });

    // function count() {
    //     vcdate = $('#vcdate').val();
    //     // location = $('#location').val();
    //     $.ajax({
    //         type: "post",
    //         url: "{{ route('check.count') }}",
    //         data: {
    //             "vcdate": vcdate,
    //             // "location": location,
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function (response) {
    //             console.log(response.post);
    //             if (response.post >= 100) {
    //                 $('#count').html('<span style="color: red" >เต็มแล้ว</span>');
    //                 $('#succ').hide();

    //             } else {
    //                 $('#count').text(response.post);
    //                 $('#succ').show();
    //             }
    //         }
    //     });
    // }



    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function () {
        $('#staticBackdrop').modal('show');
        $('#idps').prop('readonly', true);
    });

    function showPass(s) {
        if (s.value == 'พาสปอร์ต') {
            document.getElementById('idps2').style.display = '';
            document.getElementById('idps1').style.display = 'none';
            $('#idps_').prop('required', true);
            $('#idps').prop('required', false);
            $('#idps').prop('value', null);
            $('#subother').prop('required', true);
        } else if (s.value == 'บัตรประชาชน') {
            document.getElementById('idps2').style.display = 'none';
            document.getElementById('idps1').style.display = '';
            $('#idps').prop('readonly', false);
            $('#idps').prop('required', true);
            $('#idps_').prop('value', null);
            $('#idps_').prop('required', false);
        } else {

        }
    }



    $('.form_datetime').datepicker({
        autoclose: 1
    });

    function forceNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    function show(e) {
        if (e.value == 'กลุ่มโรคประจำตัว 7 กลุ่มโรค') {
            document.getElementById('divsubtypes').style.display = '';
            document.getElementById('divsubtypes2').style.display = 'none';
            $('#subtypps1').prop('required', true);
            $('#subtypps').prop('required', false);
            $('#subother').prop('required', false);
            $('#subtypps').prop('value', null);
            $('#subother').prop('value', null);
        } else if (e.value == 'กลุ่มบุคลากรด่านหน้า(ปกครอง,ตำรวจ,ทหารฯลฯ)') {
            document.getElementById('divsubtypes').style.display = 'none';
            document.getElementById('divsubtypes2').style.display = '';
            $('#subtypps1').prop('required', false);
            $('#subtypps').prop('required', true);
            $('#subother').prop('required', false);
            $('#subtypps1').prop('value', null);
            $('#subother').prop('value', null);
        } else {
            document.getElementById('divsubtypes2').style.display = 'none';
            document.getElementById('divsubtypes').style.display = 'none';
            document.getElementById('divsubtypes3').style.display = 'none';
            $('#subtypps1').prop('required', false);
            $('#subtypps').prop('required', false);
            $('#subother').prop('required', false);
            $('#subtypps').prop('value', null);
            $('#subtypps1').prop('value', null);
            $('#subother').prop('value', null);

        }
    }


    function show2(t) {
        if (t.value == 'บุคคลด่านหน้าอื่นๆ') {
            document.getElementById('divsubtypes3').style.display = '';
            $('#subother').prop('required', true);
        } else {
            document.getElementById('divsubtypes3').style.display = 'none';
            $('#subother').prop('required', false);
            $('#subother').prop('value', null);
        }
    }


    $('#submit-form').submit(function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $idps = $('#idps').val();
        console.log($idps);
        Swal.fire({
            title: 'คุณต้องการลงทะเบียนใช่หรือไม่',
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
                    url: "save",
                    processData: false,
                    contentType: false,
                    data: formData,
                    'idps': $idps,
                    success: function (response) {
                        console.log(response);
                        if (response.status == 1) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'success',
                                title: 'ลงทะเบียนสำเร็จ',
                                showConfirmButton: true,
                                showCancelButton: true,
                                width: 600,
                                higth: 100,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                cancelButtonText: 'ปิด',
                                confirmButtonText: '<a href="search" style="color: white">ตรวจสอบรายชื่อของท่าน</a>',
                                timer: 50000
                            })
                        } else if (response.status == 3) {
                            Swal.fire({
                                // position: 'top-end',
                                icon: 'error',
                                title: 'ไม่สามารถบันทึกได้',
                                text: 'เนื่องจากเลขบัตรนี้ได้มีอยู่ในระบบแล้ว กรุณาตรวจสอบชื่อของท่าน',
                                showConfirmButton: true,
                                showCancelButton: true,
                                width: 600,
                                higth: 100,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                cancelButtonText: 'ปิด',
                                confirmButtonText: '<a href="search" style="color: white">ตรวจสอบรายชื่อของท่าน</a>',
                                timer: 50000
                            })
                        } else {
                            Swal.fire(
                                'ไม่สามารถบันทึกได้',
                                'กรุณาตรวจสอบเลขบัตรที่ท่านกรอกให้ถูกต้อง หรือติดต่อ Call Center 0889919091 (9.00 น. - 16.00 น. )',
                                'question'

                            )
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                                icon: 'error',
                                title: 'ไม่สามารถบันทึกได้',
                                text: 'เนื่องจากไม่เกินข้อผิดพลาดบางอย่าง กรุณาทำรายการใหม่',
                                showConfirmButton: true,
                                showCancelButton: true,
                                width: 600,
                                higth: 100,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                cancelButtonText: '<a href="regis_vac" style="color: white">ปิด</a>',
                                confirmButtonText: '<a href="regis_vac" style="color: white">ตกลงเพื่อทำรายการใหม่</a>',
                                timer: 50000
                            })
                    }
                });
            }
        })

    });

    function bannedKey(evt) {
        var allowedEng = true;
        var allowedThai = true;
        var allowedNum = false;

        var k = event.keyCode;

        /* เช็คตัวเลข 0-9 */
        if (k >= 48 && k <= 57) {
            return allowedNum;
        }

        /* เช็คคีย์อังกฤษ a-z, A-Z */
        if ((k >= 65 && k <= 90) || (k >= 97 && k <= 122)) {
            return allowedEng;
        }

        /* เช็คคีย์ไทย ทั้งแบบ non-unicode และ unicode */
        if ((k >= 161 && k <= 255) || (k >= 3585 && k <= 3675)) {
            return allowedThai;
        }
    }

    function resetfrm() {
        resetcard('0');
    }

    function resetcard(keyreset) {
        document.getElementById("idps").value = '';
        document.getElementById("hididps").value = '0';
        document.getElementById("divsubtypes").style.display = "none";
        if (keyreset == 'บัตรประชาชน') {
            document.getElementById('showalertidpp').innerHTML = "ตัวเลข 13 หลัก";
            document.getElementById("idps").disabled = false;
        } else
        if (keyreset == 'พาสปอร์ต') {
            document.getElementById('showalertidpp').innerHTML = "(A-Z)+(A-Z หรือ 0-9)";
            document.getElementById("idps").disabled = false;
        } else {
            document.getElementById('showalertidpp').innerHTML = "เลือกบัตร !";
            document.getElementById("idps").disabled = true;
        }
    }

    function CheckPersonID(id) {
        if (id.length != 13) return false;
        for (i = 0, sum = 0; i < 12; i++)
            sum += parseFloat(id.charAt(i)) * (13 - i);
        if ((11 - sum % 11) % 10 != parseFloat(id.charAt(12)))
            return false;
        return true;
    }

    function numtext(ele) {
        if (event.keyCode == 32) {
            return false;
            return true;
        }
        if (event.keyCode < 48 || event.keyCode > 57) {
            event.returnValue = false;
        }
    }

    function checkidpp(validpp, keyidpp) {
        if (keyidpp == 1) {
            if (validpp != "") {
                document.getElementById('showalertidpp').innerHTML = "ตัวเลข 13 หลัก";
                document.getElementById('hididps').value = '0';
            } else {
                document.getElementById('showalertidpp').innerHTML = "ตัวเลข 13 หลัก";
                document.getElementById('hididps').value = '0';
            }
        } else
        if (keyidpp == 2) {
            if (validpp != "") {
                document.getElementById('showalertidpp').innerHTML = "(A-Z)+(A-Z หรือ 0-9)";
                document.getElementById('hididps').value = '0';
            } else {
                document.getElementById('showalertidpp').innerHTML = "(A-Z)+(A-Z หรือ 0-9)";
                document.getElementById('hididps').value = '0';
            }
        } else
        if (keyidpp == 0) {
            document.getElementById('showalertidpp').innerHTML = "<font color='red'>เลือกบัตร !</font>";
            document.getElementById('hididps').value = '0';
        }
    }


    function change2() {
        $('.districts').val('');
        $('.zip').val('');
    }


    function change() {
        $('.amphures').val('');
        $('.districts').val('');
        $('.zip').val('');
    }


    $('.province').change(function () {
        if ($(this).val() != '') {
            var select = $(this).val();
            $.ajax({
                type: "post",
                url: "{{ route('dropdown.fetch') }}",
                data: {
                    'select': select,
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('.amphures').html(response);
                }
            });
        }
    });

    $('.amphures').change(function () {
        if ($(this).val() != '') {
            var select = $(this).val();
            $.ajax({
                type: "post",
                url: "{{ route('dropdown.amphures') }}",
                data: {
                    'select': select,
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $('.districts').html(response);

                }
            });
        }
    });

    $('.districts').change(function () {
        if ($(this).val() != '') {
            var select = $(this).val();
            $.ajax({
                type: "post",
                url: "{{ route('dropdown.zip') }}",
                data: {
                    'select': select,
                    "_token": "{{ csrf_token() }}"
                },
                success: function (response) {
                    $.each(response, function (key, val) {
                        $('.zip').prop('value', val.zip_code);
                    });
                }
            });
        }
    });


