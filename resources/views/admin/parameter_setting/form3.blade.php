@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'parameter_setting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座服務參數維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/parameter_setting_3" class="text-info">講座服務參數列表</a></li>
                        <li class="active">講座服務參數維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/parameter_setting_3/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/parameter_setting_3/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">接送地點車資維護</h3></div>
                    <div class="card-body pt-4">

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">縣市</label>
                            <div class="col-md-3">
                                <select id="county" name="county" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.county') as $key => $va)
                                        <option value="{{ $key }}" {{ old('county', (isset($data->county))? $data->county : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">接送地點<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="area" name="area" placeholder="請輸入接送地點" value="{{ old('area', (isset($data->area))? $data->area : '') }}" autocomplete="off" required maxlength="50">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">契約車資</label>
                            <div class="col-sm-3">
                                <input type="number" class="form-control input-max" id="fare" name="fare" placeholder="請輸入契約車資" value="{{ old('fare', (isset($data->fare))? $data->fare : '') }}" autocomplete="off" required maxlength="12">
                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/parameter_setting_3?search=search{{ (isset($data->county))? '&county='.$data->county : '&county=1' }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/parameter_setting_3/{{ $data->id }}');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    @include('admin/layouts/list/del_modol')

@endsection