@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'user_group';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">權限群組維護表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/user_group" class="text-info">權限群組維護列表</a></li>
                        <li class="active">權限群組維護表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/user_group/'.$data->id,  'enctype'=>'multipart/form-data','id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/user_group/','enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">權限群組維護表單</h3></div>
                    <div class="card-body pt-4">

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">群組名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="name" name="name" <?=(isset($data->name))?'readonly="readonly"':'';?> placeholder="請輸入帳號" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" maxlength="15" required>
                            </div>
                        </div>

                        <link href="/plugin/lou-multi-select/css/multi-select.css" rel="stylesheet" type="text/css">


                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">群組權限</label>
                            <div class="col-sm-10">

                                <p>
                                    <button type="button" class="btn btn-link" id="select-all">選擇全部</button> /
                                    <button type="button" class="btn btn-link" id="deselect-all">移除全部</button>
                                </p>

                                <style>
                                .ms-container {
                                    width: 100%;
                                }
                                .ms-container .ms-list  {
                                    height: 300px;
                                }
                                </style>

                                <select id="auth" name="auth[]" style="height: 200px;" multiple="multiple">
                                    @foreach(config('app.system_menu') as $key => $va)
                                        <option value="{{ $key }}" <?=(in_array($key, $user_group_auth))?'selected':'';?> >{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/user_group">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/user_group/{{ $data->id }}/from');" data-toggle="modal" data-target="#del_modol" >
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
        selectableHeader: "<div class='custom-header'>可選權限</div>",
        selectionHeader: "<div class='custom-header'>選定權限</div>",
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