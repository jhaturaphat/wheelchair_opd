<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link" href="home">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
					
                    <a class="nav-link"  href="<?php echo $queryString; ?>">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        ลงทะเบียนรับแจ้งเตือน.
                    </a>
                    {{-- <a class="nav-link" href="Cribbooking">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        แบบคำขอ
                    </a> --}}
                    <a class="nav-link" href="otherform">
                        <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                        แบบฟอร์มอื่นๆ
                    </a>

                    <div class="sb-sidenav-menu-heading">Addons</div>
                    {{-- <a class="nav-link" href="charts">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                        Charts
                    </a> --}}
                    <a class="nav-link" href="tables">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        ตารางการจ่ายงาน
                    </a>
                    <a class="nav-link" href="tables_ssn">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        ตารางบันทึกงาน
                    </a>
                    <a class="nav-link" href="summary">
                        <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                        สรุปผลงาน
                    </a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                {{ Auth::user()->name }}
            </div>
        </nav>
    </div>
