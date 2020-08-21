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
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_schedule/siteedit/', 'id'=>'form']) !!}

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">調整主教室</h3></div>
                    <div class="card-body pt-4">

                        <input type="hidden" name="class" value="{{ $data->class }}">
                        <input type="hidden" name="term" value="{{ $data->term }}">

                        <!-- 開始時間 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">主教室<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select class="form-control select2 " name="branch" onchange="getbranch(this.value)">
                                    <option value="">未定</option>
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ $data->site_branch == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                        <option value="3" {{ $data->site_branch == '3'? 'selected' : '' }}>外地上課</option>
                                </select>
                            </div>
                            <div class="col-md-6" id ="Taipeilist" 
                            @if(old('site_branch', (isset($data)) ? $data->site_branch : '') != 1)
                                style="display:none"
                            @endif 
                            >
                                <select id="site" name="siteT" class="select2 form-control select2-single input-max">
                                    @foreach($Taipeilist as $va)
                                        <option value="{{ $va->site }}" {{ old('stime', (isset($data->site))? $data->site : 1) == $va->site? 'selected' : '' }}>{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id ="Nantoulist" 
                            @if(old('site_branch', (isset($data)) ? $data->site_branch : '') != 2)
                                style="display:none"
                            @endif 
                            >
                                <select id="site" name="siteN" class="select2 form-control select2-single input-max">
                                    @foreach($Nantoulist as $va)
                                        <option value="{{ $va->roomno }}" {{ old('stime', (isset($data->site))? $data->site : 1) == $va->roomno? 'selected' : '' }}>{{ $va->roomname }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id ="otherlocation" 
                            @if(old('site_branch', (isset($data)) ? $data->site_branch : '') != 3)
                                style="display:none"
                            @endif 
                            >
                                <div class="input-max">
                                    <input type="text" class="form-control input-max" id="location" name="location" placeholder="外地上課地點" value="{{ old('location', (isset($data->location))? $data->location : '') }}" autocomplete="off" maxlength="255">
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <span style="color:red;font-size:25px;"><b>注意：使用此調整主教室的功能，本班期的教室會以新的主教室重新預約教室，各課程如果已經有分別設定實際教室，也會一併清空重新預約。</font>
                            </b></span>
                        </fieldset>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
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
@section('js')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
function getbranch(site_branch){
    $("select[name='siteT']").val("").trigger("change");
    $("select[name='siteN']").val("").trigger("change");
    if (site_branch == 1){
        $("#Taipeilist").css("display", "flex");
        $("#Nantoulist").css("display", "none");
        $("#otherlocation").css("display", "none");
    }else if (site_branch == 2){
        $("#Taipeilist").css("display", "none");
        $("#Nantoulist").css("display", "flex");
        $("#otherlocation").css("display", "none");
    }else if (site_branch == 3){
        $("#Taipeilist").css("display", "none");    
        $("#Nantoulist").css("display", "none");
        $("#otherlocation").css("display", "flex");
    }else{
        $("#Taipeilist").css("display", "none");    
        $("#Nantoulist").css("display", "none");
        $("#otherlocation").css("display", "none");
    }
}
// $(document).ready(function(){
//     getbranch();
// })
</script>