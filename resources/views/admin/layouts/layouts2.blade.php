<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.admin_title') }}</title>

    <meta content="{{ config('app.admin_title') }}" name="description" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    {{-- Icon --}}
    {{--<link rel="shortcut icon" href="/backend/assets/images/favicon_1.ico">--}}
    <!-- 禁止搜尋引擎登錄 -->
    <meta name="robots" content="noindex">
    <!-- Custom Files -->
    <link href="/backend/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/backend/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/backend/assets/css/style.css" rel="stylesheet" type="text/css" />
    <!-- 下拉選單 -->
    <link href="/backend/plugins/select2/select2.min.css" rel="stylesheet">
    <!-- 日期選擇器 -->
    <link href="/backend/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <!-- 日期區間選擇器 -->
    <link rel="stylesheet" href="/backend/plugins/daterangepicker/daterangepicker.css">
    <!-- 時間選擇 -->
    <link href="/backend/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

    <!-- 開關 -->
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/css/bootstrap4-toggle.min.css" rel="stylesheet">
    @yield('css')

    <!-- sweetalert2 -->
    <link href="/backend/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <!-- 專案 -->
    <link href="/backend/project/project.css" rel="stylesheet" type="text/css" />

    <!-- 階層樹 -->
    <link href="/backend/assets/css/jquery.treeview.css" rel="stylesheet" type="text/css" />

</head>

{{-- 字體放大 --}}
<style>
    #sidebar-menu span {
        font-size: 20px;
        color: #000;
    }

    #web_head_title {
        font-size: 22px;
    }

    .card-title {
        font-size: 20px;
        color: #000;
    }

    body {
        font-size: 18px;
        color: #000;
    }

    th {
        font-size: 18px;
        color: #000;
    }

    .search-float .input-group-text {
        font-size: 16px;
        color: #000;
    }

    .form-control {
        font-size: 16px;
        color: #000;
    }
</style>

<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <!--<div class="topbar">
        <div class="topbar-left">
            <div class="text-center">
                <a href="/admin" class="logo"><span id="web_head_title">{{ config('app.menu_title') }}</span></a>
            </div>
        </div>

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <ul class="list-inline menu-left mb-0">
                    <li class="float-left">
                        <a class="button-menu-mobile open-left text-white pointer">
                            <i class="fa fa-bars"></i>
                        </a>
                        目前鎖定的班期：無
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-lock fa-lg"></i>前往鎖定
                        </button>      
                    </li>
                  
                </ul>

                <ul class="nav navbar-right float-right list-inline">

                    <li class="dropdown open">
                        <a href="" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-user pr-1"></i>目前登入的使用者：
                            {{ Auth::guard('managers')->user()->username }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/admin/profile" class="dropdown-item"><i class="fa fa-key mr-2"></i> 修改密碼</a></li>
                            <li><a href="/admin/logout" class="dropdown-item"><i class="md md-settings-power mr-2"></i> 登出</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </div>-->
    <!-- Top Bar End -->



    <div class="content">
        <!-- 內容 -->
        @yield('content')

        <!-- 頁尾 -->
        <footer class="footer"></footer>
    </div>

</div>
<!-- END wrapper -->

<script>
    var resizefunc = [];
</script>

<!-- JS  -->
<script src="/backend/assets/js/modernizr.min.js"></script>
<script src="/backend/assets/js/jquery.min.js"></script>
<script src="/backend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/backend/assets/js/detect.js"></script>
<script src="/backend/assets/js/fastclick.js"></script>
<script src="/backend/assets/js/jquery.slimscroll.js"></script>
<script src="/backend/assets/js/jquery.blockUI.js"></script>
<script src="/backend/assets/js/waves.js"></script>
<script src="/backend/assets/js/wow.min.js"></script>
<script src="/backend/assets/js/jquery.nicescroll.js"></script>
<script src="/backend/assets/js/jquery.scrollTo.min.js"></script>
<script src="/backend/plugins/jquery-multi-select/jquery.multi-select.js"></script>
<script src="/backend/plugins/jquery-multi-select/jquery.quicksearch.js"></script>
<!-- 下拉選單 -->
<script src="/backend/plugins/select2/select2.full.min.js"></script>
<!-- 列表排序 -->
<script src="/backend/project/sort.js"></script>
<!-- 表單驗證 -->
<script src="/backend/project/verification.js"></script>
<!-- CK編輯器 -->
<script src="/backend/plugins/ckeditor/ckeditor.js"></script>
<script src="/backend/project/serviceimg.js"></script>
<!-- 日期選擇器 -->
<script src="/backend/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>
<script src="/backend/plugins/datepicker/locales/bootstrap-datepicker.zh-TW.js"></script>
<!-- 日期區間選擇器 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="/backend/plugins/daterangepicker/daterangepicker.js"></script>
<!-- 時間選擇 -->
<script src="/backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- 開關 -->
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.4.0/js/bootstrap4-toggle.min.js"></script>
<!-- 拖移 -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.6.1/Sortable.min.js"></script>
<!-- 上傳圖片 -->
<script src="/backend/project/upimg.js"></script>
<!-- 上傳檔案 -->
<script src="/backend/project/upfile.js"></script>
<!-- sweetalert2 -->
<script src="/backend/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="/backend/assets/pages/jquery.sweet-alert.init.js"></script>
<!-- 階層樹 -->
<script src="/backend/assets/js/jquery.treeview.js"></script>
<script src="/backend/assets/js/jquery.treeview.js"></script>

@yield('js')
<script src="/backend/assets/js/jquery.app.js"></script>
<script src="/backend/assets/js/jquery.cookie.js"></script>
<script src="/backend/project/project.js"></script>

<script>
    // 閒置過久
    function idle()
    {
        swal('這個頁面閒置時間太長，請重新整理頁面').then(function () {

            window.location.reload();
        })
    }

    var idleT =setTimeout('idle()', 3600000);
</script>
</body>
</html>