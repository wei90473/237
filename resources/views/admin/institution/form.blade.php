@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'institution';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">機關資料表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/institution" class="text-info">機關資料列表</a></li>
                        <li class="active">機關資料表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/institution/'.$data->organ, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/institution/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">機關資料表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 機關代碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">機關代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="organ" name="organ" placeholder="請輸入機關代碼" value="{{ old('organ', (isset($data->organ))? $data->organ : '') }}" autocomplete="off" required maxlength="10" {{ (isset($data))? 'readonly' : '' }} onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                        </div>

                        <!-- 機關名稱(全銜) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">機關全銜<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="lname" name="lname" placeholder="請輸入機關全銜" value="{{ old('lname', (isset($data->lname))? $data->lname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 機關名稱(簡稱) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">機關簡稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="sname" name="sname" placeholder="請輸入機關簡稱" value="{{ old('sname', (isset($data->sname))? $data->sname : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 聯絡單位 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡單位</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="division" name="division" placeholder="請輸入聯絡單位" value="{{ old('division', (isset($data->division))? $data->division : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 聯絡人(一) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人(一)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="sponsor1" name="sponsor1" placeholder="請輸入聯絡人(一)" value="{{ old('sponsor1', (isset($data->sponsor1))? $data->sponsor1 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 聯絡人(一)電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人(一)電話</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="telnoa1" name="telnoa1"  value="{{ old('telnoa1', (isset($data->telnoa1))? $data->telnoa1 : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">電話</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="255" autocomplete="off" id="telnob1" name="telnob1"  value="{{ old('telnob1', (isset($data->telnob1))? $data->telnob1 : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="telnoc1" name="telnoc1"  value="{{ old('telnoc1', (isset($data->telnoc1))? $data->telnoc1 : '') }}">

                                </div>

                            </div>
                        </div>

                        <!-- 聯絡人(一)傳真 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人(一)傳真</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="faxnoa1" name="faxnoa1"  value="{{ old('faxnoa1', (isset($data->faxnoa1))? $data->faxnoa1 : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">電話</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="255" autocomplete="off" id="faxnob1" name="faxnob1"  value="{{ old('faxnob1', (isset($data->faxnob1))? $data->faxnob1 : '') }}">

                                </div>

                            </div>
                        </div>

                        <!-- 聯絡人(二) -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人(二)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="sponsor2" name="sponsor2" placeholder="請輸入聯絡人(二)" value="{{ old('sponsor2', (isset($data->sponsor2))? $data->sponsor2 : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 聯絡人(二)電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人(二)電話</label>
                            <div class="col-sm-10">
                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="telnoa2" name="telnoa2"  value="{{ old('telnoa2', (isset($data->telnoa2))? $data->telnoa2 : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">電話</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="255" autocomplete="off" id="telnob2" name="telnob2"  value="{{ old('telnob2', (isset($data->telnob2))? $data->telnob2 : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="telnoc2" name="telnoc2"  value="{{ old('telnoc2', (isset($data->telnoc2))? $data->telnoc2 : '') }}">

                                </div>
                            </div>
                        </div>

                        <!-- 聯絡人(二)傳真 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人(二)傳真</label>
                            <div class="col-sm-10">
                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="faxnoa2" name="faxnoa2"  value="{{ old('faxnoa2', (isset($data->faxnoa2))? $data->faxnoa2 : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">電話</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="255" autocomplete="off" id="faxnob2" name="faxnob2"  value="{{ old('faxnob2', (isset($data->faxnob2))? $data->faxnob2 : '') }}">

                                </div>
                            </div>
                        </div>

                        <!-- 地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">地址</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">郵遞區號</span>
                                    </div>

                                    <input type="text" style="max-width:74px;" class="form-control" maxlength="5" autocomplete="off" id="zip" name="zip"  value="{{ old('zip', (isset($data->zip))? $data->zip : '') }}">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">地址</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="255" autocomplete="off" id="address" name="address"  value="{{ old('address', (isset($data->address))? $data->address : '') }}">
                                </div>

                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入Email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 分類 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">分類<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="type" name="type" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.institution_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{--<!-- 網路登入密碼 -->--}}
                        {{--<div class="form-group row">--}}
                            {{--<label class="col-sm-2 control-label text-md-right pt-2">網路登入密碼</label>--}}
                            {{--<div class="col-sm-10">--}}
                                {{--<input type="text" class="form-control input-max" id="password" name="password" placeholder="請輸入網路登入密碼" value="{{ old('password', (isset($data->password))? $data->password : '') }}" autocomplete="off" maxlength="255">--}}
                            {{--</div>--}}
                        {{--</div>--}}


                        <!-- 網路使用狀態 -->
                        {{--<div class="form-group row">--}}
                            {{--<label class="col-md-2 col-form-label text-md-right">網路使用狀態<span class="text-danger">*</span></label>--}}
                            {{--<div class="col-md-10">--}}
                                {{--<select id="active" name="active" class="select2 form-control select2-single input-max">--}}
                                    {{--@foreach(config('app.active') as $key => $va)--}}
                                        {{--<option value="{{ $key }}" {{ old('active', (isset($data->active))? $data->active : 1) == $key? 'selected' : '' }}>{{ $va }}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <!-- 統計與否 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">統計與否<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                @foreach(config('database_fields.m13tb.kind') as $key => $va)
                                    <label class="col-form-label">
                                        <input type="radio" name="kind" value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'checked' : '' }} required >{{ $va }}
                                    </label>                                             
                                @endforeach
                            </div>
                        </div>

                        <!-- 啟用日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">啟用日期<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" id="effdate" name="effdate" class="form-control" autocomplete="off" value="{{ isset($data['effdate']) ? $data['effdate'] : null }}" required >
                                    <div class="input-group-prepend">
                                        <span class="input-group-addon" style="cursor: pointer;" id="effdate_datepicker"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>  
                            </div>                             

                        </div>

                        <!-- 停用日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">停用日期</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" id="expdate" name="expdate" class="form-control" autocomplete="off" value="{{ isset($data['expdate']) ? $data['expdate'] : null }}" >
                                    <div class="input-group-prepend">
                                        <span class="input-group-addon" style="cursor: pointer;" id="expdate_datepicker"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>  
                            </div>            
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/institution">
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

@section('js')
<script>
$(document).ready(function() {
    $("#expdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
    });
    $('#expdate_datepicker').click(function(){
        $("#expdate").focus();
    });

    $("#effdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
    });
    $('#effdate_datepicker').click(function(){
        $("#effdate").focus();
    });    
});



    effdate
</script>
@endsection