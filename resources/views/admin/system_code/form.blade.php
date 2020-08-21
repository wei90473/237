@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'system_code';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">系統代碼表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/system_code" class="text-info">系統代碼列表</a></li>
                        <li class="active">系統代碼表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/system_code/'.$data->type.'/'.$data->code, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/system_code/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">系統代碼表單</h3></div>
                    <div class="card-body pt-4">
                        <!-- 分類 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">分類<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                @if( ! isset($data))
                                <select id="type" name="type" class="select2 form-control select2-single input-max" onchange="typeChange();">
                                    @foreach(config('app.system_code_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'selected' : '' }}>{{ $key.' '.$va }}</option>
                                    @endforeach
                                </select>
                                @else
                                    <select id="type" name="type" class="select2 form-control select2-single input-max" onchange="typeChange();" disabled>
                                    @foreach(config('app.system_code_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'selected' : '' }}>{{ $key.' '.$va }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <!-- 代碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2" id="cc">代碼<span class="text-danger">*</span></label>
                            <label class="col-sm-2 control-label text-md-right pt-2" id="year" style="display: none">年份<span class="text-danger">*</span></label>
                            <label class="col-sm-2 control-label text-md-right pt-2" id="postal" style="display: none">郵遞區號<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="code" name="code" placeholder="請輸入相關代碼" value="{{ old('code', (isset($data->code))? $data->code : '') }}" autocomplete="off" required maxlength="255" {{ (isset($data))? 'readonly' : '' }}>
                            </div>
                        </div>
                        <!-- 名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入名稱" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 費用 -->
                        <div class="form-group row" id="cost_div" style="display:none">
                            <label class="col-sm-2 control-label text-md-right pt-2">費用</label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="fee" name="fee" min="1" placeholder="請輸入費用" value="{{ old('fee', (isset($data->fee))? $data->fee : NULL) }}" autocomplete="off" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>
                            </div>
                        </div>
                        <!-- 類別1 -->
                        <div class="form-group row" id="category_div" style="display:none">
                            <label class="col-sm-2 control-label text-md-right pt-2">類別1-大類<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="category" name="category" class="select2 form-control select2-single input-max" onchange="typeChange();">
                                    @foreach($categoryone as $key => $va)
                                        <option value="{{ $va['code'] }}" {{ old('category', (isset($data->category))? $data->category : '') == $va['code']? 'selected' : '' }}>{{ $va['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/system_code">
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
        // 分類切換:分類為E(火車費)時顯示費用欄位,其餘隱藏
        function typeChange() {

            if($('#type').val() == 'B' || $('#type').val() == 'C'|| $('#type').val() == 'F' || $('#type').val() == 'H' || $('#type').val() == 'J' || $('#type').val() == 'L' ) {
                $('#year').hide();
                $('#cc').show();
                $('#postal').hide();
                $('#cost_div').hide();
                $('#category_div').hide();    
            }else if ($('#type').val() == 'M') {
                $('#year').hide();
                $('#cc').show();
                $('#postal').hide();
                $('#cost_div').hide();
                $('#category_div').show();    
            }else if ($('#type').val() == 'E') {
                $('#year').hide();
                $('#cc').show();
                $('#postal').hide();
                $('#cost_div').show();
                $('#category_div').hide();
            }else if($('#type').val() == 'D'){
                $('#year').hide();
                $('#cc').hide();
                $('#postal').show();
                $('#cost_div').hide();
                $('#category_div').hide();
            }else{
                $('#year').show();
                $('#cc').hide();
                $('#postal').hide();
                $('#cost_div').hide();
                $('#category_div').hide();
            }
        }

        // 分類初始化
        typeChange();
    </script>
@endsection