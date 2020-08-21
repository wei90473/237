@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student';?>
<style>
button:disabled{
    display: block !important;
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員基本資料登錄</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員基本資料登錄</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')
        @include('admin.layouts.list.enrollorg_modal')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員基本資料登錄</h3>
                    </div>
                    {{ Form::model($student, ["method" => "put", "url" => "/admin/student/{$student->des_idno}"]) }}
                    <div class="card-body">
                        @if ($student->identity == 1)
                            @include('admin/student/form_government')
                        @elseif ($student->identity == 2)
                            @include('admin/student/form_general')
                        @endif 
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i>儲存</button>
                        <a href="/admin/student">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div> 
                    {{ Form::close() }}                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 刪除確認視窗 -->
<div id="modify_idno" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content p-0 b-0">
            <div class="card mb-0">
                <div class="card-header">
                    <h3 class="card-title float-left">修改身分證</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="text-white">&times;</span>
                    </button>
                </div>
                {!! Form::open([ 'method'=>'put', 'url'=>"/admin/student/modifyIdno/{$student->des_idno}"]) !!}
                <div class="card-body">
                    <label>新身分證字號</label>
                    <input type="text" class="form-control" name="new_idno">
                </div>
                <div class="modal-footer py-2">
                    <button type="submit" class="btn mr-3 btn-info pull-left">送出</button>
                    <button type="button" class="btn mr-2 btn-danger" data-dismiss="modal">取消</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

{{ Form::open(['method' => 'put', 'url' => "/admin/student/resetPassword/{$student->des_idno}", "id" => "resetPassCnt"]) }}
    <input type="hidden" name="resetType">
    <input type="hidden" name="resetIdentity">
{{ Form::close() }}
@endsection

@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script>
    function hideOrShowAccount(id, status)
    {
        status = (status == true) ? '' : 'none';
        $('#' + id).css('display', status);
    }

    function chooseM17tb(enrollorg, enrollname)
    {
        console.log("#" + select_m17tb + '_name');
        $("#" + select_m17tb).val(enrollorg);
        $("#" + select_m17tb + '_name').val(enrollname);
    }

    function resetPassCnt(resetIdentity, resetType)
    {
        $("input[name=resetIdentity]").val(resetIdentity);
        $("input[name=resetType]").val(resetType);
        $("#resetPassCnt").submit();
    }

    // function showM17tbModol()
    // {
    //     select_m17tb = id;
    //     $("#m17tb").modal('show'); 
    // }
    function showM22tb()
    {
        let status = 'none';

        if ($('input[name="m22tb[usertype1]"]')[0].checked){
            status = '';
        }

        if ($('input[name="m22tb[usertype3]"]')[0].checked){
            status = '';
        }

        $('#m22tb').css('display', status);        
    }
</script>
@endsection