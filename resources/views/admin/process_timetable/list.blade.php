@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'process_timetable';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">辦理流程期限表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">辦理流程期限表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>辦理流程期限表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                            <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班完成狀態</span>
                                                    </div>
                                                    <select class="form-control select2" name="process_complete">
                                                        @foreach(config('app.class_complete') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['process_complete'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <input type="checkbox" id="job_complete" name="job_complete" style="min-width:20px; margin-left:5px;" <?=($queryData['job_complete']=='N')?'checked':'';?> value="N" >
                                                    <span>僅顯示未完成的工作</span>
                                                </div>
                                            </div>

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                <!-- 重設條件 -->
                                                <!-- <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button> -->

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
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection