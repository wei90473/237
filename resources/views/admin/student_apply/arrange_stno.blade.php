@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_apply';?>
<style>
.search-float input{
    min-width:1px;
}
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員報名處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員報名處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>序學號</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::open(['method' => 'put']) !!}
                                <button class="btn btn-primary">自動編排</button>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        {!! Form::open(['method' => 'put', 'url' => "/admin/student_apply/stno_edit/{$t04tb_info['class']}/{$t04tb_info['term']}", "id" => 'stno']) !!}
                        <div class="row" style="margin-top:10px;">
                            <div class="table-responsive margin_top_bottom10" style="height:700px;" >
                                <table class="table table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th width="100">學號</th>
                                        <th>姓名</th>
                                        <th>性別</th>
                                        <th>機關</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($t13tbs as $t13tb)
                                            <tr>
                                                <td><input type="text" class="form-control stno" name="stno[{{ $t13tb->idno }}]" value="{{ old("stno.{$t13tb->idno}" ,$t13tb->no) }}"></td>
                                                <td>{{ $t13tb->m02tb->cname }}</td>
                                                <td>{{ config('database_fields.m02tb')['sex'][$t13tb->m02tb->sex] }}</td>
                                                <td>{{ $t13tb->m02tb->dept }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>                        
                            </div> 
                        </div>
                        {!! Form::close() !!}
                    </div>

                    <div class="card-footer">
                        <button type="button" class="btn btn-sm btn-primary" onclick="$('#stno').submit()"><i class="fa fa-save"></i> 儲存</button>
                        <a href="/admin/student_apply/{{$t04tb_info['class']}}/{{$t04tb_info['term']}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function up()
    {
        let selected = $("#condition").find("option:selected");
        let last = $("#condition option:nth-child(" + (selected.index()) + ")");
        if (selected.index() > 0 && selected.length == 1){
            $("#condition").find("option");
            let tmp = last.val();
            let tmp_text = last.text();
            last.val(selected.val());
            last.text(selected.text());
            last.prop("selected", true);

            selected.val(tmp);
            selected.text(tmp_text);
            selected.prop("selected", false);
        }
    }

    function down()
    {
        let selected = $("#condition").find("option:selected");
        let next = $("#condition option:nth-child(" + (selected.index() + 2) + ")");
        console.log(selected.index());
        if (selected.index() + 1 < $("#condition option").length && selected.length == 1){
            $("#condition").find("option");
            let tmp = next.val();
            let tmp_text = next.text();
            next.val(selected.val());
            next.text(selected.text());
            next.prop("selected", true);

            selected.val(tmp);
            selected.text(tmp_text);
            selected.prop("selected", false);            
        }
    }    

    function add()
    {
        let selected = $("#unselect").find("option:selected");
        $("#condition").append(selected);
        selected.prop("selected", false); 
    }

    function sub()
    {
        let selected = $("#condition").find("option:selected");
        $("#unselect").append(selected);
        selected.prop("selected", false); 
    }

    function assign_submit()
    {
        $("#condition").find("option").prop("selected", true);
    }

</script>
@endsection