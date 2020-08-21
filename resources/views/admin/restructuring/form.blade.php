@extends('admin.layouts.layouts')
@section('content')
<style>
    .th25{
        /*width:25%;*/
    }
    .restructuring th{
        vertical-align:middle !important;
        text-align: center;
    }
</style>
    <?php $_menu = 'restructuring';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">組織改制對照表維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">組織改制對照表維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>組織改制對照表維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if (isset($restructuring) && $action == "edit")
                                        {{ Form::open(['method' => 'put', 'url' => "/admin/restructuring/{$restructuring->id}"]) }}
                                    @else
                                        {{ Form::open(['method' => 'post', 'url' => '/admin/restructuring']) }}
                                    @endif 
                                        <div id="before">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="col-form-label">改制前</label>
                                                    <button type="button" class="btn btn-primary" onclick="addEnrollorg('before')"><i class="fa fa-plus fa-lg pr-2"></i>新增機關</button>
                                                </div>
                                            </div>  
                                            @if (isset($restructuring) && $action == "edit")
                                                @foreach ($restructuring->details['before'] as $before_detail)
                                                <div class="form-row"> 
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <label class="col-form-label">機關代碼：</label>
                                                            <input type="text" name="before_enrollorg[{{ $before_detail->enrollorg }}]" class="form-control" value="{{ $before_detail->enrollorg }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-left:10px;">
                                                        <div class="input-group">
                                                            <label class="col-form-label">機關名稱：</label>
                                                            <input type="text" name="before_enrollname[{{ $before_detail->enrollorg }}]" class="form-control" value="{{ $before_detail->m17tb->enrollname }}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-left:10px;">
                                                        <button type="button" class="btn btn-primary" onclick="showM17tbModolForRestructuring('{{ $before_detail->enrollorg }}', 'before')">···</button>
                                                    </div>
                                                    <div class="form-group" style="margin-left:10px;">
                                                        <button type="button" class="btn btn-danger" onclick="deleteDetail(this)">刪除</button>
                                                    </div>                                                    
                                                </div>
                                                @endforeach 
                                            @endif 
                                        </div> 
                                        <div id="after">
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <label class="col-form-label">改制後</label>
                                                    <button type="button" class="btn btn-primary" onclick="addEnrollorg('after')"><i class="fa fa-plus fa-lg pr-2"></i>新增機關</button>
                                                </div>
                                            </div> 
                                            @if (isset($restructuring) && $action == "edit")
                                                @foreach ($restructuring->details['after'] as $after_detail)
                                                <div class="form-row"> 
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <label class="col-form-label">機關代碼：</label>
                                                            <input type="text" name="after_enrollorg[{{ $after_detail->enrollorg }}]" class="form-control" value="{{ $after_detail->enrollorg }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-left:10px;">
                                                        <div class="input-group">
                                                            <label class="col-form-label">機關名稱：</label>
                                                            <input type="text" name="after_enrollname[{{ $after_detail->enrollorg }}]" class="form-control" value="{{ $after_detail->m17tb->enrollname }}" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" style="margin-left:10px;">
                                                        <button type="button" class="btn btn-primary" onclick="showM17tbModolForRestructuring('{{ $after_detail->enrollorg }}', 'after')">···</button>
                                                    </div>
                                                    <div class="form-group" style="margin-left:10px;">
                                                        <button type="button" class="btn btn-danger" onclick="deleteDetail(this)">刪除</button>
                                                    </div>   
                                                </div>
                                                @endforeach 
                                            @endif 
                                        </div>

                                        <button class="btn btn btn-primary">保存</button>
                                        @if(isset($restructuring) && $action=="edit")
                                        <button type="button" class="btn btn-danger" onclick="deleteRestructuring()">刪除</button>
                                        @endif
                                        <a href="/admin/restructuring"><button type="button" class="btn btn-danger">取消</button></a>
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(isset($restructuring) && $action=="edit")
    {{ Form::open(['method' => 'delete', 'url' => "/admin/restructuring/{$restructuring->id}", 'id' => 'delete_restructuring']) }}
    {{ Form::close() }}
    @endif 
    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/enrollorg_modal')

@endsection

@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script>
    var restructuring_n = 0;
    var select_type = null;
    var isnew = '';

    function addEnrollorg(type){
        let enrollorg = '<div class="form-row">' + 
                            '<div class="form-group">' +
                                '<div class="input-group">' +
                                    '<label class="col-form-label">機關代碼：</label>' +
                                    '<input type="text" name="new_' + type + '_enrollorg[' + restructuring_n + ']" class="form-control" readonly>' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group" style="margin-left:10px;">' + 
                                '<div class="input-group">' +
                                    '<label class="col-form-label">機關名稱：</label>' +
                                    '<input type="text" name="new_' + type  + '_enrollname[' + restructuring_n + ']" class="form-control" disabled>' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group" style="margin-left:10px;">' +
                                '<button type="button" class="btn btn-primary" onclick="showM17tbModolForRestructuring(' + restructuring_n + ', \'' + type +'\', true)">···</button>' +
                            '</div>' +
                            '<div class="form-group" style="margin-left:10px;">' +
                                '<button type="button" class="btn btn-danger" onclick="deleteDetail(this)">刪除</button>' +
                            '</div>' + 
                        '</div>';
        restructuring_n++;
        $("#" + type).append(enrollorg);
    }

    function showM17tbModolForRestructuring(id = null, type, is_new = false)
    {
        select_m17tb = id;
        $("#m17tb").modal('show');
        select_type = type;
        isnew = (is_new) ? 'new_' : '';
    }

    function chooseM17tb(enrollorg, enrollname){
        console.log("input[name='" + isnew + select_type + "_enrollorg[" + select_m17tb + "]']");
        $("input[name='" + isnew + select_type + "_enrollorg[" + select_m17tb + "]']").val(enrollorg);
        $("input[name='" + isnew + select_type + "_enrollname[" + select_m17tb + "]']").val(enrollname);
    }

    function deleteDetail(btn){
        $(btn).parent().parent().remove();
    }

    function deleteRestructuring(){
        if (confirm("確定要刪除此改制嗎")){
            $("#delete_restructuring").submit();
        }
    }
</script>
@endsection