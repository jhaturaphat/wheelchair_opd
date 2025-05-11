<!DOCTYPE html>
<html lang="th">
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
        @yield('content')
    </div>
<body>
</html>