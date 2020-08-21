@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'holiday';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">國定假日表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/holiday" class="text-info">國定假日列表</a></li>
                        <li class="active">國定假日表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/holiday/'.$data->date, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/holiday/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">國定假日表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 假日名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">假日名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="holiday" name="holiday" placeholder="請輸入假日名稱" value="{{ old('holiday', (isset($data->holiday))? $data->holiday : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="date[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->date))? mb_substr($data->date, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required {{ (isset($data))? 'readonly' : '' }}>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="date[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->date))? mb_substr($data->date, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required {{ (isset($data))? 'readonly' : '' }}>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="date[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->date))? mb_substr($data->date, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required {{ (isset($data))? 'readonly' : '' }}>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>




                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/holiday">
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