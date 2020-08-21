@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'leave';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員請假處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/leave" class="text-info">學員請假處理列表</a></li>
                        <li class="active">學員請假處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($t14tb) )
                {!! Form::model($t14tb, [ 'method'=>'put', 'url'=>'/admin/leave/'.$t14tb->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', "url"=> "/admin/leave/{$t04tb->class}/{$t04tb->term}", 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">學員請假處理表單</h3></div>
                    <div class="card-body pt-4">
                        <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px; ">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div> 
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">學員<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                @if ( isset($t14tb) )
                                {{ Form::text('idno', $students[$t14tb->idno], ['class' => 'form-control input-max' , 'readOnly' => 'true']) }}
                                @else
                                {{ Form::select('idno', $students, null, ['class' => 'select2 form-control select2-single input-max']) }}
                                @endif 
                            </div>
                        </div>

                        <!-- 請假開始日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">起始日期<span class="text-danger">*</span></label>
                            <div class="input-group col-2">
                                {{ Form::text('sdate', null, ['id' => 'sdate', 'class' => 'form-control', 'autocomplete' => 'off']) }}
                                <!-- <input type="text" id="training_start_date" name="training_start_date" class="form-control" autocomplete="off" value=""> -->
                                <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="sdate_datepicker"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">截止日期<span class="text-danger">*</span></label>
                            <div class="input-group col-2">
                                {{ Form::text('edate', null, ['id' => 'edate', 'class' => 'form-control', 'autocomplete' => 'off']) }}
                                <!-- <input type="text" id="edate" name="edate" class="form-control " autocomplete="off" value=""> -->
                                <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="edate_datepicker"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>


                        <!-- 請假結束時間 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">起始時間<span class="text-danger">*</span></label>
                            <div class="col-sm-2">

                                <div class="input-group roc-date input-max">
                                    <!-- <input type="text" class="form-control" maxlength="2" name="stime[hour]" placeholder="時" autocomplete="off" value="{{ (isset($t14tb->stime))? mb_substr($t14tb->stime, 0, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required onchange="if(parseInt($(this).val()) > 23){$(this).val('23')}"> -->
                                    {{ Form::text('stime_hour', null, [
                                        'id' => 'edate', 
                                        'class' => 'form-control', 
                                        'autocomplete' => 'off', 
                                        'maxlength' => 2,
                                        'onchange' => 'if(parseInt($(this).val()) > 23){$(this).val("23")}',
                                        'required' => 'required']) }}
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">時</span>
                                    </div>
                                    
                                    {{ Form::text('stime_minute', null, [
                                        'id' => 'edate', 
                                        'class' => 'form-control', 
                                        'autocomplete' => 'off', 
                                        'maxlength' => 2,
                                        'onchange' => 'if(parseInt($(this).val()) > 59){$(this).val("59")}',
                                        'required' => 'required']) }}

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分</span>
                                    </div>
                                </div>

                            </div>                        
                            <label class="col-sm-2 control-label text-md-right pt-2">截止時間<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <div class="input-group roc-date input-max">
                                    {{ Form::text('etime_hour', null, [
                                        'id' => 'edate', 
                                        'class' => 'form-control', 
                                        'autocomplete' => 'off', 
                                        'maxlength' => 2,
                                        'onchange' => 'if(parseInt($(this).val()) > 23){$(this).val("23")}',
                                        'required' => 'required']) }}                                
                                    

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">時</span>
                                    </div>
                                    {{ Form::text('etime_minute', null, [
                                        'id' => 'edate', 
                                        'class' => 'form-control', 
                                        'autocomplete' => 'off', 
                                        'maxlength' => 2,
                                        'onchange' => 'if(parseInt($(this).val()) > 59){$(this).val("59")}',
                                        'required' => 'required']) }}  
                                    
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 假別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">假別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                {{ Form::select('type', $types, null, ['class' => 'select2 form-control select2-single input-max']) }}
                            </div>
                        </div>

                        <!-- 時數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">時數<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="hour" name="hour" min="1" placeholder="請輸入時數" value="{{ old('hour', (isset($t14tb->hour))? $t14tb->hour : 1) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="255" required>

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 事由 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">事由</label>
                            <div class="col-md-10">
                                {{ Form::textarea('reason', null, ['maxlength' => 255, 'class' => "form-control input-max"])}}
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if (isset($t14tb))
                        <a href="/admin/leave/{{$t14tb->class}}/{{$t14tb->term}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        @endif 
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
        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#sdate_datepicker').click(function(){
            $("#sdate").focus();
        });

        $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#edate_datepicker').click(function(){
            $("#edate").focus();
        });


    </script>
@endsection