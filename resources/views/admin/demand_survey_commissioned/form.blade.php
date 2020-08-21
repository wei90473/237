@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_survey_commissioned';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">委訓班需求調查表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_survey_commissioned" class="text-info">委訓班需求調查列表</a></li>
                        <li class="active">委訓班需求調查表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_survey_commissioned/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_survey_commissioned/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">委訓班需求調查表單</h3></div>
                    <div class="card-body pt-4">

                      
                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-sm-4">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" value="{{ old('yerly', (isset($data->yerly))? $data->yerly : date('Y') - 1911) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" required {{ (isset($data))? 'disabled' : '' }}>
                                 
                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                     
                        <!-- 專碼 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">專碼<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" min=1 id="item_id" name="item_id"  placeholder="委訓班專碼為必填欄位" value="{{ old('item_id', (isset($data->item_id))? $data->item_id : NULL) }}" autocomplete="off" required {{ (isset($data))? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>

                        <?php
                            $sdate = isset($data->sdate)? $data->sdate : '';
                            $edate = isset($data->edate)? $data->edate : '';
                        ?>
                        <!-- 填報開始日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">填報開始日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
								<input class="date form-control" value="{{$sdate}}" type="text" id="sdate" name="sdate">
                        

                            </div>
                        </div>

                        <!-- 填報結束日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">填報結束日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
								<input class="date form-control" value="{{$edate}}" type="text" id="edate" name="edate">
                            

                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/demand_survey_commissioned">
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
$( function() {

    $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
    $('#datepicker1').click(function(){
        $("#sdate").focus();
    });
    $("#edate").datepicker({
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#datepicker2').click(function(){
        $("#edate").focus();
    });

  } );
</script>
@endsection