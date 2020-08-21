@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求調查表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_survey" class="text-info">需求調查列表</a></li>
                        <li class="active">需求調查表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_survey/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_survey/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">需求調查表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-sm-10">


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

                        @if(isset($data))
                        <!-- 第幾次調查 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查</label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="times" name="times" min="1" placeholder="請輸入第幾次調查" value="{{ old('times', (isset($data->times))? $data->times : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" disabled>

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- 需求調查名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">需求調查名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="purpose" name="purpose" placeholder="請輸入需求調查名稱" value="{{ old('purpose', (isset($data->purpose))? $data->purpose : '') }}" autocomplete="off" required maxlength="255">
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
                                <!--<div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>
                                     
                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="sdate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->sdate))? mb_substr($data->sdate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="sdate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->sdate))? mb_substr($data->sdate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="sdate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->sdate))? mb_substr($data->sdate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>-->

                            </div>
                        </div>

                        <!-- 填報結束日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">填報結束日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
								<input class="date form-control" value="{{$edate}}" type="text" id="edate" name="edate">
                              <!--   <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="edate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->edate))? mb_substr($data->edate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="edate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->edate))? mb_substr($data->edate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="edate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->edate))? mb_substr($data->edate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div> -->

                            </div>
                        </div>

                        <!-- 班別 -->
                        <?php
                            $yerly = isset($data->yerly)? $data->yerly : '';
                            $times = isset($data->times)? $data->times : '';
                        ?>
                        <?php $list = $base->getDemandSurveyClasses($yerly, $times);?>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班別</label>
                            <div class="col-md-10">
                                <select id="class" name="class[]" class="select2 form-control select2-single input-max" multiple="multiple">
                                    @foreach($list as $va)
                                        <option value="{{ $va->class }}" {{  ($yerly == $va->yerly && $times == $va->times && $va->yerly)? 'selected' : '' }}>{{ $va->class }} {{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/demand_survey">
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
    $('#sdate').datepicker();
	$('#edate').datepicker();
  } );
</script>
@endsection