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

    <!-- table排序-->
    <link rel="stylesheet" href="/backend/assets/css/theme.default.css">
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
        color: #FFFFFF;
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
    <div class="topbar">
        <!-- LOGO -->
        <div class="topbar-left" >
            <!-- <div class="text-center"> -->
                <!-- 選單上方標題 -->
                <!-- <a href="/admin" class="logo"><span id="web_head_title">{{ config('app.menu_title') }}</span></a>-->
                                <img class="logo" style="display:block; margin:auto;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAm8AAABkCAYAAADQbtdiAAAACXBIWXMAAAAnAAAAJwEqCZFPAAAMHGlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNi4wLWMwMDIgNzkuMTY0NDYwLCAyMDIwLzA1LzEyLTE2OjA0OjE3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIiB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoTWFjaW50b3NoKSIgeG1wOkNyZWF0ZURhdGU9IjIwMjAtMDctMDlUMTc6NTc6MTErMDg6MDAiIHhtcDpNb2RpZnlEYXRlPSIyMDIwLTA3LTEzVDE0OjI4OjExKzA4OjAwIiB4bXA6TWV0YWRhdGFEYXRlPSIyMDIwLTA3LTEzVDE0OjI4OjExKzA4OjAwIiBkYzpmb3JtYXQ9ImltYWdlL3BuZyIgcGhvdG9zaG9wOkNvbG9yTW9kZT0iMyIgcGhvdG9zaG9wOklDQ1Byb2ZpbGU9InNSR0IgSUVDNjE5NjYtMi4xIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjBmNGIzMjA2LWI5ZjYtNGI0NC05NDJjLTZmNDQ4M2IzNzdjNiIgeG1wTU06RG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOjhkYzgzNWVmLTcyNDMtMjk0OS05NWYzLTM4MDkzZDNmMGNhNCIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ4bXAuZGlkOmZkYjUyYWQ3LTg4NDgtNDJlYi05M2E4LTllNDc2NDM2NTU2NiIgdGlmZjpPcmllbnRhdGlvbj0iMSIgdGlmZjpYUmVzb2x1dGlvbj0iMTAwMDAvMTAwMDAiIHRpZmY6WVJlc29sdXRpb249IjEwMDAwLzEwMDAwIiB0aWZmOlJlc29sdXRpb25Vbml0PSIyIiBleGlmOkNvbG9yU3BhY2U9IjEiIGV4aWY6UGl4ZWxYRGltZW5zaW9uPSI2MjMiIGV4aWY6UGl4ZWxZRGltZW5zaW9uPSIxMDAiPiA8cGhvdG9zaG9wOlRleHRMYXllcnM+IDxyZGY6QmFnPiA8cmRmOmxpIHBob3Rvc2hvcDpMYXllck5hbWU9IuaVmeiCsuiok+e3tOizh+ioiuezu+e1sSIgcGhvdG9zaG9wOkxheWVyVGV4dD0i5pWZ6IKy6KiT57e06LOH6KiK57O757WxIi8+IDxyZGY6bGkgcGhvdG9zaG9wOkxheWVyTmFtZT0i5pWZ6IKy6KiT57e06LOH6KiK57O757WxIiBwaG90b3Nob3A6TGF5ZXJUZXh0PSLmlZnogrLoqJPnt7Tos4foqIrns7vntbEiLz4gPC9yZGY6QmFnPiA8L3Bob3Rvc2hvcDpUZXh0TGF5ZXJzPiA8eG1wTU06SGlzdG9yeT4gPHJkZjpTZXE+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJjcmVhdGVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOmZkYjUyYWQ3LTg4NDgtNDJlYi05M2E4LTllNDc2NDM2NTU2NiIgc3RFdnQ6d2hlbj0iMjAyMC0wNy0wOVQxNzo1NzoxMSswODowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIDIxLjIgKE1hY2ludG9zaCkiLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImNvbnZlcnRlZCIgc3RFdnQ6cGFyYW1ldGVycz0iZnJvbSBpbWFnZS9wbmcgdG8gYXBwbGljYXRpb24vdm5kLmFkb2JlLnBob3Rvc2hvcCIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6ZTIyOGM1YjUtZTE4Yi00ZDdkLWE0MjItNjI1MjNiYjhlZjYxIiBzdEV2dDp3aGVuPSIyMDIwLTA3LTEwVDE4OjMwOjAxKzA4OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6ZDliODFmZjUtMmQyMy00NzFmLTliNGUtMTAwNGViZjIzYjE2IiBzdEV2dDp3aGVuPSIyMDIwLTA3LTEzVDE0OjI4OjExKzA4OjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgMjEuMiAoTWFjaW50b3NoKSIgc3RFdnQ6Y2hhbmdlZD0iLyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0iY29udmVydGVkIiBzdEV2dDpwYXJhbWV0ZXJzPSJmcm9tIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3AgdG8gaW1hZ2UvcG5nIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJkZXJpdmVkIiBzdEV2dDpwYXJhbWV0ZXJzPSJjb252ZXJ0ZWQgZnJvbSBhcHBsaWNhdGlvbi92bmQuYWRvYmUucGhvdG9zaG9wIHRvIGltYWdlL3BuZyIvPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MGY0YjMyMDYtYjlmNi00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"></img>


<!-- </div> -->
        </div>

        <!-- 上方右側 -->
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <ul class="list-inline menu-left mb-0" style=" z-index: 99;margin-left: 20px;">
                    <li class="float-left">
                        <a class="button-menu-mobile open-left text-white pointer">
                            <i class="fa fa-bars"></i>
                        </a>
                        <?php
                            $lock=session()->get('lock_class');
                            if(!empty($lock)){
                        ?>

                        <!-- <button type="submit" form="unlock_class" class="btn btn-light" onclick="refresh_page();" > -->
                        <button type="submit" form="unlock_class" class="btn btn-light" >
                        目前鎖定班期:{{$lock['name']}}第{{$lock['term']}}期</br>
                        <i class="fa fa-unlock fa-lg"></i>取消鎖定

                        <!-- <form id="unlock_class" method="post" action="/admin/lockclass/unlock" target="_blank"> -->
                        <form id="unlock_class" method="post" action="/admin/lockclass/unlock" >
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                        <script>
                            function refresh_page()
                            {
                                window.location.reload();
                                setTimeout('refresh_page()',3000);
                            }
                            //setTimeout('refresh_page()',3000);
                        </script>
                        <?php }else{?>

                        <a href="/admin/lockclass"><button type="button" class="btn btn-danger">
                        目前鎖定的班期：無
                            <i class="fa fa-lock fa-lg"></i>
                         前往鎖定
                        </button></a>
                        <?php }?>
                    </li>

                </ul>

                <ul class="nav navbar-right float-right list-inline">

                    <li class="dropdown open" id="user_profile">
                        <a href="" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-user pr-1"></i>
                            @if (\Session::get('simulate_origin_user') != null)
                            <font color="yellow">目前正在模擬使用者：{{ Auth::guard('managers')->user()->username }}</font>
                            @else
                            目前登入的使用者：{{ Auth::guard('managers')->user()->username }}
                            @endif
                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="/admin/profile" class="dropdown-item"><i class="fa fa-key mr-2"></i> 修改密碼</a></li>
                            <li><a href="/admin/goToTrain" target="_blank" class="dropdown-item"><i class="fa fa-share mr-2"></i>前往訓練需求及學習服務系統</a></li>
                            <li><a href="/admin/goToEApp" target="_blank" class="dropdown-item"><i class="fa fa-share mr-2"></i>前往App管理後台</a></li>

                            @if (\Session::get('simulate_origin_user') != null)
                                <li><a href="#" onclick="$('#returnOriginUser').submit()" class="dropdown-item"><i class="md md-reply mr-2"></i>切回原使用者({{ \Session::get('simulate_origin_user')->username }})</a></li>
                                {{ Form::open(['method' => 'post', 'url' => '/admin/role_simulate/returnOriginUser', 'id' => 'returnOriginUser']) }}
                                {{ Form::close() }}
                            @endif
                            <li><a href="/admin/logout" class="dropdown-item"><i class="md md-settings-power mr-2"></i> 登出</a></li>
                        </ul>

                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <!-- Top Bar End -->

    <!-- 選單 -->
    @include('admin/layouts/menu')


    <div class="content-page">
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
<script src="/backend/plugins/select2/i18n/zh-TW.js"></script>
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
<!-- table排序 -->
<script src="/backend/assets/js/jquery.tablesorter.js"></script>
@yield('js')
<script src="/backend/assets/js/jquery.app.js"></script>
<script src="/backend/assets/js/jquery.cookie.js"></script>'
<script src="/backend/project/project.js"></script>'

<script>
    // 閒置過久
    function idle()
    {
        swal('這個頁面閒置時間太長，請重新整理頁面').then(function () {

            window.location.reload();
        })
    }

    var idleT =setTimeout('idle()', 3600000);
    $(function() {
        $(".table").tablesorter();
    });
</script>
</body>
</html>