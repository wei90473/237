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
                        <li><a href="/admin/parameter_setting_1" class="text-info">講座服務參數列表</a></li>
                        <li class="active">講座服務參數維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/parameter_setting_1/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/parameter_setting_1/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">計程車呼號維護</h3></div>
                    <div class="card-body pt-4">

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">種類</label>
                            <div class="col-md-3">
                                <select id="type" name="type" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.license_plate_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 呼號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">呼號<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="call" name="call" placeholder="請輸入呼號" value="{{ old('call', (isset($data->call))? $data->call : '') }}" autocomplete="off" required maxlength="50">
                            </div>
                        </div>

                        <!-- 姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入姓名" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="50">
                            </div>
                        </div>


                        <!-- 車牌 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">車牌<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="license_plate" name="license_plate" placeholder="請輸入車牌" value="{{ old('license_plate', (isset($data->license_plate))? $data->license_plate : '') }}" autocomplete="off" required maxlength="10">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">勤務聯絡電話</label>
                            <div class="col-sm-3">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control lecture_text" maxlength="3" autocomplete="off" id="tel1" name="tel1"  value="{{ old('tel1', (isset($data->tel1))? $data->tel1 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control lecture_text" maxlength="10" autocomplete="off" id="tel2" name="tel2"  value="{{ old('tel2', (isset($data->tel2))? $data->tel2 : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">


                                </div>

                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">個人電話</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="mobile" name="mobile" placeholder="請輸入行動電話" value="{{ old('mobile', (isset($data->mobile))? $data->mobile : '') }}" autocomplete="off" maxlength="12">
                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/parameter_setting_1?search=search">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/parameter_setting_1/{{ $data->id }}');" data-toggle="modal" data-target="#del_modol" >
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