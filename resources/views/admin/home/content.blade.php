@extends('admin/layouts/layouts')
@section('content')

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <ol class="breadcrumb pull-right">
                        <li class="active">首頁</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')


            <div class="form-card-box">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Welcome</h3></div>
                    <div class="card-body pb-5">
                        <p class="lead">{{ Auth::guard('managers')->user()->username }} 歡迎回來！</p>
                        <p>上次登入時間 <strong>{{ Auth::guard('managers')->user()->Last_login_time }}</strong> , 登入前密碼輸入錯誤次數 <strong>{{ Auth::guard('managers')->user()->Last_logins }}</strong></p>
                        <p>現在時間是 <strong>{{ date('Y-m-d H:i:s') }}</strong> , 您所登入的帳號為 <strong>{{ Auth::guard('managers')->user()->userid }}</strong></p>
                        <p>修改密碼請<a href="/admin/profile">點擊這裡</a> , 提醒您操作結束時請將帳號<a href="/admin/logout">登出</a>.</p>
                        <?php if(!empty($Process_non_complete)){ ?>
                        <p>尚有未完成班務流程請<a href="/admin/term_process">點擊這裡</a> </p>
                        <?php } ?>
                    </div>
                </div>


            </div>

            {!! Form::close() !!}

        </div>
    </div>

@endsection

