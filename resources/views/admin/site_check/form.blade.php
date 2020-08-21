@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'site_check';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地審核處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/site_check" class="text-info">場地審核處理列表</a></li>
                        <li class="active">場地審核處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">場地審核處理表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 申請編號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">申請編號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->meet.$data->serno }}" readonly>
                            </div>
                        </div>

                        <!-- 申請日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">申請日期</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->applydate }}" readonly>
                            </div>
                        </div>

                        <!-- 活動名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">活動名稱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->name }}" readonly>
                            </div>
                        </div>

                        <!-- 人數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">人數</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->cnt }}" readonly>
                            </div>
                        </div>

                        <!-- 單位類型 -->
                        <?php
                            if ($data->type == '1') {
                                $type_text = '政府機關';
                            } elseif ($data->type == '2') {
                                $type_text = '民間單位';
                            } else {
                                $type_text = '受政府機關委託';
                            }
                        ?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">單位類型</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $type_text }}" readonly>
                            </div>
                        </div>

                        <!-- 申請單位 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">申請單位</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->payer }}" readonly>
                            </div>
                        </div>

                        <!-- 收據抬頭 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">收據抬頭</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->payer }}" readonly>
                            </div>
                        </div>

                        <!-- 單位地址 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">單位地址</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->address }}" readonly>
                            </div>
                        </div>

                        <!-- 聯絡人 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->liaison }}" readonly>
                            </div>
                        </div>

                        <!-- 職稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">職稱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->position }}" readonly>
                            </div>
                        </div>

                        <!-- 電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->telno }}" readonly>
                            </div>
                        </div>

                        <!-- 傳真 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">傳真</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->faxno }}" readonly>
                            </div>
                        </div>

                        <!-- 行動電話 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">行動電話</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->mobiltel }}" readonly>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->email }}" readonly>
                            </div>
                        </div>

                        <!-- 回覆意見 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">回覆意見</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ $data->replynote }}" readonly>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <a href="/admin/site_check">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>


@endsection