@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'recommend_user';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">機關個人帳號表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/recommend_user" class="text-info">機關個人帳號列表</a></li>
                        <li class="active">機關個人帳號</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/recommend_user/'.$data->enrollorg.'/'.$data->userid, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/recommend_user/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">機關個人帳號</h3></div>
                    <div class="card-body pt-4">
                        {{-- 取得薦送機關列表 --}}
                        <?php $list = $base->getDBList('M17tb', ['enrollorg', 'enrollname']);?>
                        <div class="form-group row institution">
                            <!-- 薦送機關代碼 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">薦送機關代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                @if( ! isset($data))
                                    <select id="enrollorg" name="enrollorg" class="browser-default custom-select">
                                        @foreach($list as $va)
                                            <option value="{{ $va->enrollorg }}" {{ old('enrollorg', (isset($data->enrollorg))? $data->enrollorg : '') == $va->enrollorg? 'selected' : '' }}>{{ $va->enrollorg }} {{ $va->enrollname }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control input-max" value="{{ old('enrollorg', (isset($data->enrollorg))? $data->enrollorg : '') }}" autocomplete="off" readonly>
                                @endif
                            </div>
                            <!-- 薦送機關名稱 -->
                            <label class="col-2 control-label pt-2">薦送機關名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" value="{{ old('enrollname', (isset($data->enrollname))? $data->enrollname : '') }}" autocomplete="off" required {{ (isset($data))? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 姓名 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="username" name="username" placeholder="請輸入姓名" value="{{ old('username', (isset($data->username))? $data->username : '') }}" autocomplete="off" required>
                            </div>
                            <!-- 身分證字號 -->
                            <label class="col-2 control-label pt-2">身分證字號<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" placeholder="請輸入身分證字號" value="{{ old('userid', (isset($data->userid))? $data->userid : '') }}" autocomplete="off" required {{ (isset($data))? 'readonly' : '' }}>
                            </div>
                        </div>
                        <!-- 聯絡單位 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡單位</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="section" name="section" placeholder="請輸入聯絡單位" value="{{ old('section', (isset($data->section))? $data->section : '') }}" autocomplete="off">
                            </div>
                        </div>
                        <!-- 電子信箱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">EMAIL</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入電子信箱" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off">
                            </div>
                            <!-- 性別 -->
                            <label class="col-2 control-label pt-2">性別<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" value="{{ config('app.sex')[$data->sex] }}" placeholder="系統判定" autocomplete="off" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 出生年月日 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">出生年月日</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="birthday" name="birthday" placeholder="請輸入生日" value="{{ old('birthday', (isset($data->birthday))? $data->birthday : '') }}" autocomplete="off">
                            </div>
                            <!-- 自訂帳號 -->
                            <label class="col-2 control-label pt-2">自訂帳號</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" name="selfid" value="{{ old('selfid', (isset($data->selfid))? $data->selfid : '') }}" autocomplete="off" {{ (isset($data))? 'readonly' : '' }}>
                            </div>
                        </div>
                        <!-- 電話 -->
                        <!-- <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">電話</label>
                            <div class="col-sm-10">

                                <div class="input-group group input-max">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">區碼</span>
                                    </div>

                                    <input type="text" style="max-width:80px;" class="form-control" maxlength="3" autocomplete="off" id="telnoa" name="telnoa"  value="{{ old('telnoa', (isset($data->telnoa))? $data->telnoa : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">號碼</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="10" autocomplete="off" id="telnob" name="telnob"  value="{{ old('telnob', (isset($data->telnob))? $data->telnob : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">分機</span>
                                    </div>

                                    <input type="text" class="form-control" maxlength="8" autocomplete="off" id="telnoc" name="telnoc"  value="{{ old('telnoc', (isset($data->telnoc))? $data->telnoc : '') }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>

                            </div>
                        </div> -->

                        <!-- 聯絡窗口 -->
                        <div class="form-group row">
                            <div class="col-sm-2 text-md-right">
                                <input type="checkbox" name="keyman" value="Y" {{ old('keyman', (isset($data->keyman))? $data->keyman : '') == 'Y'? 'checked' : '' }} disabled> 聯絡窗口
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/recommend_user">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>


@endsection

@section('js')
    <script>
        // 機關代碼
        function recommendChange() {

            var recommend_text = $("#recommend_id option:selected").data('recommend_text');

            if (recommend_text) {

                $('#recommend_text').html(recommend_text);
            } else {
                $('#recommend_text').html('');
            }
        }

        // 機關代碼 初始化
        recommendChange();

    </script>
@endsection