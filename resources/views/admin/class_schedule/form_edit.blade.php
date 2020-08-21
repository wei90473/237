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
                        <li class="active">課程表詳細資料</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_schedule/'.$queryData->class.$queryData->term.$queryData->course, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/class_schedule/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">課程表詳細資料</h3></div>
                    <div class="card-body pt-4">
                        <input type="hidden" name="class" value="{{ $queryData->class }}">
                        <input type="hidden" name="term" value="{{ $queryData->term }}">
                        <input type="hidden" name="sdate" value="{{ $queryData->sdate }}">
                        <input type="hidden" name="edate" value="{{ $queryData->edate }}">
                        <fieldset style="border:groove; padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-3 ">班號：{{$queryData->class}}</label>
                                <label class="col-sm-2 ">期別：{{$queryData->term}}</label>
                                <label class="col-sm-4 ">辦班院區：{{ config('app.branch.'.$queryData->branch) }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">訓練班別：{{$queryData->name}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">分班名稱：{{$queryData->branchname}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班別類型：{{ config('app.process.'.$queryData->process) }}</label>
                                <label class="col-sm-4 ">班務人員：{{$queryData->username}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">起迄期間：{{$queryData->sdate}}～{{$queryData->edate}}</label>
                            </div>
                        </fieldset>
                        <!-- 課程名稱 -->
                        <div class="form-group row pt-2">
                            <label class="col-sm-2 control-label text-md-right pt-2">課程名稱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="classname" name="classname" placeholder="請輸入課程名稱" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" readonly maxlength="255" >
                            </div>
                        </div>
                        <!-- 日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">日期<span class="text-danger">*</span></label>
                            <div class="input-group col-6">
                                <input type="text" id="sdate_begin" name="sdate_begin" class="form-control" autocomplete="off" value="{{ old('date', (isset($data->date))? $data->date : '') }}">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <!-- 開始時間 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">時間<span class="text-danger">*</span></label>
                            @if($queryData->Scale==10)
                            <div class="col-md-4">
                                <select id="stime" name="stime" class="form-control select2" >
                                    <option value="">請選擇</option>
                                    @foreach(config('time.start_ten') as $va)
                                        <option value="{{ $va }}" {{ old('stime', (isset($data->stime))? $data->stime : 1) == $va? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <i>～</i>
                            <div class="col-md-4">
                                <select id="etime" name="etime" class="form-control select2" >
                                    <option value="">請選擇</option>
                                    @foreach(config('time.end_ten') as $va)
                                        <option value="{{ $va }}" {{ old('etime', (isset($data->etime))? $data->etime : 1) == $va? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else
                            <div class="col-md-4">
                                <select id="stime" name="stime" class="form-control select2" >
                                    <option value="">請選擇</option>
                                    @foreach(config('time.start_five') as $va)
                                        <option value="{{ $va }}" {{ old('stime', (isset($data->stime))? $data->stime : 1) == $va? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <i>～</i>
                            <div class="col-md-4">
                                <select id="etime" name="etime" class="form-control select2" >
                                    <option value="">請選擇</option>
                                    @foreach(config('time.end_five') as $va)
                                        <option value="{{ $va }}" {{ old('etime', (isset($data->etime))? $data->etime : 1) == $va? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <!-- 時數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">時數<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="hour" name="hour" placeholder="請輸入時數" value="{{ old('hour', (isset($data->hour))? $data->hour : '0') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>
                        <!-- 實際教室 -->
                        <div class="form-group row" id="Actual_classroom" style="display: flex;">
                            <label class="col-sm-2 control-label text-md-right pt-2">實際教室<span class="text-danger">*</span></label>
                                <div class="col-md-4">
                                    <select class="form-control select2" name="branch" onchange="getbranch()">
                                        <option value="">未定</option>
                                        @foreach(config('app.branch') as $key => $va)
                                            <option value="{{ $key }}" {{ $data->branch == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4" style="display: inline-flex;"id ="Taipeilist">
                                    <select id="site" name="Tsite" class="form-control select2">
                                        @foreach($Taipeilist as $va)
                                            <option value="{{ $va->site }}" {{ old('stime', (isset($data->site))? $data->site : 1) == $va->site? 'selected' : '' }}>{{ $va->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4" style="display: none;" id ="Nantoulist" >
                                    <select id="site" name="Nsite" class="form-control select2">
                                        @foreach($Nantoulist as $va)
                                            <option value="{{ $va->roomno }}" {{ old('stime', (isset($data->site))? $data->site : 1) == $va->roomno? 'selected' : '' }}>{{ $va->roomname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="checkbox" id="mergeclass" name="mergeclass" {{ old('mergeclass', (isset($data->mergeclass))? $data->mergeclass : '') == 'Y'? 'checked' : '' }} >合堂上課
                                </div>
                            </div>
                            <!-- 外地上課 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2"></label>
                                <div>
                                    <input type="checkbox" id="otherlocation" name='otherlocation' value="Y" onclick="Choose_place()" {{ old('site', (isset($data->site))? $data->site : '') == 'oth'? 'checked' : '' }} >外地上課，地點：
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control input-max" id="location" name="location" placeholder="外地上課地點" value="{{ old('location', (isset($data->location))? $data->location : '') }}" autocomplete="off" maxlength="255">
                                </div>
                            </div>
                            <!-- 講座 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">講座</label>
                                <div class="col-sm-2">
                                    <input type="text" name="teacher_name" class="form-control input-max"  value="{{ old('cname', (isset($data->cname))? $data->cname : '') }}" autocomplete="off" readonly maxlength="255">
                                </div>
                                <label class="control-label pt-2">教材</label>
                                <div class="col-sm-4">
                                    <select id="teachingmaterial" name="teachingmaterial" class="form-control select2">
                                        <option value="">請選擇</option>
                                        @if($teachingmaterial != '')
                                        @foreach($teachingmaterial as $va)
                                            <option value="{{ $va->id }}" {{ old('stime', (isset($data->teachingmaterial))? $data->teachingmaterial : '') == $va->id? 'selected' : '' }}>{{ $va->filename }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <!-- 維護教材 -->
                                <div class="float-md-left">
                                    <a href="/admin/teaching_material?keyword={{ isset($data->cname)? $data->cname : '' }}&search=search" target="_blank">
                                        <button type="button" class="btn btn-primary btn-sm pt-2" style=" margin-right:10px;">維護教材</button>
                                    </a>
                                </div>
                            </div>

                        </div>
                        <!-- 備註 -->
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="edit()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <!-- 刪除 -->
                        <span onclick="$('#del_form').attr('action', '/admin/class_schedule/{{$queryData->class.$queryData->term.$queryData->course }}');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>
                        </span>
                        <a href="/admin/class_schedule/{{$queryData->class.$queryData->term}}/edit">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
    function Choose_place(){
        var check = $(':checkbox[name="otherlocation"]:checked').val();
        if(check=='Y'){
            $('#Actual_classroom').css('display','none');
        }else{
            $('#location').removeAttr('value');
            $('#Actual_classroom').css('display','flex');
        }
    }

    function edit(){
        if($('input[name=sdate]').val() > $('input[name=sdate_begin]').val() ||  $('input[name=edate]').val() < $('input[name=sdate_begin]').val() ){
            alert('該日期不為上課日期');
            return false;
        }else if($('select[name=stime]').val() >= $('select[name=etime]').val() ){
            alert('結束時間不得早於(或等於)開始時間!');
            return false;
        }else{
            console.log(1);
            $("#form").submit();
        }
    }
    function getbranch(){
        var title = $("select[name=branch]").val();
        console.log(title);
        if(title =='2'){
            $('#Nantoulist').css('display','inline-flex');
            $('#Taipeilist').css('display','none');
        }else if(title =='1'){
            $('#Nantoulist').css('display','none');
            $('#Taipeilist').css('display','inline-flex');
        }else{
            $('#Nantoulist').css('display','none');
            $('#Taipeilist').css('display','none');
        }
    }
    $(document).ready(function() {
        $("#sdate_begin").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate_begin").focus();
        });
    });
    $(document).ready(function(){
        getbranch();
        Choose_place();
    });

</script>