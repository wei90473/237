@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'ClassCalendar';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">行事表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">行事表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>行事表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form method="get" action="/admin/classCalendar/export" id="search_form">

                                    <div class="form-group row">
                                        <label class="col-sm-1 control-label text-md-right pt-2">院區</label>
                                        <div class="col-2">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select name="branch" class="custom-select input-max" required>
                                                    <option value="">請選擇</option>
                                                    <option value="1">臺北院區</option>
                                                    <option value="2">南投院區</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 control-label text-md-right pt-2">年度</label>
                                        <div class="col-2">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select name="yerly" class="custom-select input-max" required>
                                                    @for($yerly=date('Y')-1910; $yerly >= 90; $yerly--)
                                                    <option value="{{ $yerly }}">{{ $yerly }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-1 control-label text-md-right pt-2">月份</label>
                                        <div class="col-2">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select name="month" class="custom-select input-max" required>
                                                    @for($month=1; $month <= 12; $month++)
                                                    <option value="{{ $month }}">{{ $month }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <div class="form-group row col-6 align-items-center justify-content-center">
                                        <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                        <label id="download"></label>
                                    </div>
                                </form>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>

</script>
@endsection