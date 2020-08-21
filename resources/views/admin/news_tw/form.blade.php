@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'news_tw';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">中文最新消息維護表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/news_tw" class="text-info">中文最新消息維護列表</a></li>
                        <li class="active">中文最新消息維護表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/news_tw/'.$data->serno, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/news_tw/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">中文最新消息維護表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 發佈日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">發佈日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
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
                                </div>

                            </div>
                        </div>

                        <!-- 失效日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">失效日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
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
                                </div>

                            </div>
                        </div>

                        <!-- 分類 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">分類<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="type" name="type" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.news_tw_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data->type))? $data->type : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 標題 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">標題<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="title" name="title" placeholder="請輸入標題" value="{{ old('title', (isset($data->title))? $data->title : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <!-- 內容 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">內容</label>
                            <div class="col-sm-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="content" id="content">{{ old('content', (isset($data->content))? $data->content : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 是否連結 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">是否連結</label>
                            <div class="col-md-10">
                                <select id="link" name="link" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('link', (isset($data->link))? $data->link : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 連結網址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">連結網址</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="url" name="url" placeholder="請輸入連結網址" value="{{ old('url', (isset($data->url))? $data->url : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>




                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/news_tw">
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