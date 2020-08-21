@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_survey_commissioned';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">委訓班需求調查匯入</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_survey_commissioned" class="text-info">委訓班需求調查列表</a></li>
                        <li class="active">委訓班需求調查匯入</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'file' => true, 'url'=>'/admin/demand_survey_commissioned/import_save/'.$data->id, 'enctype'=>'multipart/form-data','id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'file' => true, 'url'=>'/admin/demand_survey_commissioned/import_save/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">委訓班需求調查匯入</h3></div>
                    <div class="card-body pt-4">

                       <input type="hidden" name="_token" value="{{csrf_token()}}"/>
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
                            <label class="col-sm-2 control-label text-md-right pt-2">專碼</label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" min=1 id="item_id" name="item_id"  placeholder="委訓班專碼" value="{{ old('item_id', (isset($data->item_id))? $data->item_id : NULL) }}" autocomplete="off" required {{ (isset($data))? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                        <!-- 匯入檔案 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">匯入檔案</label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                     <input class="form-control" type="file"  id="import_file" name="import_file">
                                    
                                </div>


                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/demand_survey_commissioned">
                            <button type="button" onclick="window.history.go(-1); return false;" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
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
