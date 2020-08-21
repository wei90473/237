@extends('admin/layouts/layouts')
@section('content')
    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">修改密碼</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/manager" class="text-info">首頁</a></li>
                        <li class="active">修改密碼</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')


            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/profile/', 'id'=>'form']) !!}


            <div class="col-md-8 offset-md-2 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">修改密碼</h3></div>
                    <div class="card-body pt-4">


                        <!-- 姓名 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">姓名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ Auth::guard('managers')->user()->username }}" disabled="disabled">
                            </div>
                        </div>

                        <!-- 帳號 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">帳號</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" value="{{ Auth::guard('managers')->user()->userid }}" disabled="disabled">
                            </div>
                        </div>

                        <!-- 密碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" for="old_password">舊密碼</label>
                            <div class="col-md-10">
                                <input type="password" id="old_password" name="old_password" class="form-control input-max" placeholder="請輸入舊密碼" required autocomplete="new-password" autocomplete="off">
                            </div>
                        </div>

                        <!-- 密碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" for="password">新密碼</label>
                            <div class="col-md-10">
                                <input type="password" id="password" name="password" class="form-control input-max" placeholder="請輸入新密碼" required autocomplete="new-password" autocomplete="off">
                            </div>
                        </div>

                        <!-- 確認密碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" for="password_confirmation">確認密碼</label>
                            <div class="col-md-10">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control input-max" placeholder="請再次輸入密碼" required autocomplete="new-password" autocomplete="off">
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
@endsection

