@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'system_account';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">系統帳號維護表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/system_account" class="text-info">系統帳號維護列表</a></li>
                        <li class="active">系統帳號維護表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/system_account/'.$data->id,  'enctype'=>'multipart/form-data','id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/system_account/','enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">系統帳號維護表單</h3></div>
                    <div class="card-body pt-4">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">帳號<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="userid" name="userid" <?=(isset($data->userid))?'readonly="readonly"':'';?> placeholder="請輸入帳號" value="{{ old('userid', (isset($data->userid))? $data->userid : '') }}" autocomplete="off" maxlength="15" required>
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">姓名<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="username" name="username" <?=(isset($data->username))?'readonly="readonly"':'';?> placeholder="請輸入姓名" value="{{ old('username', (isset($data->username))? $data->username : '') }}" autocomplete="off" maxlength="10" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">身分證</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="idno" name="idno" placeholder="請輸入身分證" value="{{ old('idno', (isset($data->idno))? $data->idno : '') }}" autocomplete="off" maxlength="10" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">部門<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="deptid" name="deptid" required class="select2 form-control select2-single input-max">
                                    <option value=''>請選擇部門</option>
                                    <?php foreach($section as $row){ ?>
                                    <option value="<?=$row->deptid;?>" {{ old('deptid', (isset($data->deptid))? $data->deptid : 0) == $row->deptid? 'selected' : '' }}  ><?=$row->section;?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">分機</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="ext" name="ext" placeholder="請輸入分機" value="{{ old('ext', (isset($data->ext))? $data->ext : '') }}" autocomplete="off" maxlength="6">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">Email</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="email" name="email" placeholder="請輸入Email" value="{{ old('email', (isset($data->email))? $data->email : '') }}" autocomplete="off" maxlength="50">
                            </div>
                            <label class="col-md-2 col-form-label text-md-right">離職與否</label>
                            <div class="col-md-3">
                                <input type="radio" id="dimission" name="dimission" value="Y" {{ old('dimission', (isset($data->dimission))? $data->dimission : 1) == 'Y'? 'checked' : '' }}>是
                                <input type="radio" id="dimission" name="dimission" value="N" {{ old('dimission', (isset($data->dimission))? $data->dimission : 1) == 'N'? 'checked' : '' }}>否
                            </div>
                        </div>
                        <?php if(isset($data)){ ?>
                        <!-- 密碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" for="old_password">舊密碼</label>
                            <div class="col-md-10">
                                <input type="password" id="old_password" name="old_password" class="form-control input-max" placeholder="請輸入舊密碼" autocomplete="old_password" autocomplete="off">
                            </div>
                        </div>
                        <?php } ?>
                        <!-- 密碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" for="password">新密碼</label>
                            <div class="col-md-10">
                                <input type="password" id="password" name="password" class="form-control input-max" placeholder="請輸入新密碼" autocomplete="new-password" autocomplete="off">
                            </div>
                        </div>

                        <!-- 確認密碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" for="password_confirmation">確認密碼</label>
                            <div class="col-md-10">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control input-max" placeholder="請再次輸入密碼" autocomplete="new-password" autocomplete="off">
                            </div>
                        </div>

                        <link href="/plugin/lou-multi-select/css/multi-select.css" rel="stylesheet" type="text/css">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">權限群組</label>
                            <div class="col-sm-10">

                                <p>
                                    <button type="button" class="btn btn-link" id="select-all">選擇全部</button> /
                                    <button type="button" class="btn btn-link" id="deselect-all">移除全部</button>
                                </p>

                                <select id="auth" name="auth[]" style="height: 200px;" multiple="multiple">
                                    @foreach($user_group as $row)
                                        <option value="{{ $row['id'] }}" <?=(in_array($row['id'], $user_group_auth))?'selected':'';?> >{{ $row['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/system_account">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/system_account/{{ $data->id }}/from');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
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
<script src="/plugin/lou-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script>
$(function() {
    $('select[name="auth[]"]').multiSelect({
        selectableHeader: "<div class='custom-header'>可選群組</div>",
        selectionHeader: "<div class='custom-header'>選定群組</div>",
    });
    $('#select-all').click(function(){
        $('select[name="auth[]"]').multiSelect('select_all');
        return false;
    });
    $('#deselect-all').click(function(){
        $('select[name="auth[]"]').multiSelect('deselect_all');
        return false;
    });
});
</script>
@endsection

@include('admin/layouts/list/del_modol')