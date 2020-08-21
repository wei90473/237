@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'program';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">程式代碼表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/program" class="text-info">程式代碼列表</a></li>
                        <li class="active">程式代碼表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/program/'.$data->progid, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/program/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">程式代碼表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 程式代碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">程式代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="progid" name="progid" placeholder="請輸入程式代碼" value="{{ old('progid', (isset($data->progid))? $data->progid : '') }}" autocomplete="off" required maxlength="255" {{ (isset($data))? 'readonly' :'' }}>
                            </div>
                        </div>

                        <!-- 程式名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">程式名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="progname" name="progname" placeholder="請輸入程式名稱" value="{{ old('progname', (isset($data->progname))? $data->progname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 狀態 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">狀態<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="logmk" name="logmk" class="select2 form-control select2-single input-max" required>
                                    @foreach(config('app.logmk') as $key => $va)
                                        <option value="{{ $key }}" {{ old('logmk', (isset($data->logmk))? $data->logmk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/program">
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