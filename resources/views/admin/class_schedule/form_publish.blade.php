@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">課程表處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_schedule" class="text-info">課程表處理列表</a></li>
                        <li class="active">課程表處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_schedule/publishedit/', 'id'=>'form']) !!}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">課程表處理</h3></div>
                    <div class="card-body pt-4">

                        <input type="hidden" name="class" value="{{ $data->class }}">
                        <input type="hidden" name="term" value="{{ $data->term }}">


                        <!-- 班別 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" placeholder="請輸入班別" value="{{ $data->class }} {{ $data->name or '' }}" autocomplete="off" readonly maxlength="255">
                            </div>
                        </div>

                        <!-- 期別 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" placeholder="請輸入期別" value="{{ $data->term }}" autocomplete="off" readonly maxlength="255">
                            </div>
                        </div>

                        <!-- 網頁公告 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">網頁公告</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" placeholder="請輸入網頁公告" value="{{ $data->publish2 == 'Y'? '已公告' : '未公告' }}" autocomplete="off" readonly maxlength="255">
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>公告</button>
                        <a href="/admin/class_schedule/{{$data->class.$data->term}}/edit">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection