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
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>編組別</h3>
                    </div>

                    <div class="card-body">
                    {!! Form::open(['method' => 'put', 'onsubmit' => 'assign_submit()']) !!}

                    <div class="row">
                        <div style="width:300px;">
                            <font>分組條件(未選)</font>
                            <select id="unselect" class="custom-select" style="height:130px;" multiple>
                                <option value="sex" selected>性別(男女)</option>
                                <option value="organ">主管機關(中央與地方)</option>
                                <option value="ecode">學歷(學、碩、博)</option>
                                <option value="age">年齡(10年為一個區間)</option>
                            </select>
                        </div>   
                        <div style="width:30px;">
                            <button type="button" class="btn btn-primary" onclick="add()" style="margin-top:25px;"><i class="fa fa-plus" style="font-size: 20px !important;"></i></button>
                            <button type="button" class="btn btn-primary" onclick="sub()" style="margin-top:10px;"><i class="fa fa-minus" style="font-size: 20px !important"></i></button>
                        </div>   

                        <div style="width:300px;margin-left:20px;">
                            <font>分組條件(已選)</font>
                            <select id="condition" name="condition[]" class="custom-select" style="height:130px;" multiple></select>
                        </div>
                        <div style="width:60px;">
                            <button type="button" class="btn btn-primary" onclick="up()" style="margin-top:25px;"><i class="fa fa-arrow-up" style="font-size: 20px !important"></i></button>
                            <button type="button" class="btn btn-primary" onclick="down()" style="margin-top:10px;"><i class="fa fa-arrow-down" style="font-size: 20px !important"></i></button>
                        </div>  
                                               
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div>
                            <button class="btn btn-primary" name="btn" value="diff">自動分組(異質性分組)</button>
                        </div>
                    </div>

                    <div class="row" style="margin-top:10px;">

                        <div class="input-group">
                            <label class="form-label" style="padding:5px;margin-bottom:0px;">組別數量(隨機分配):</label>
                            <div style="width:60px;margin-right:5px;">
                                <input type="text" class="form-control" name="group_num">
                            </div>
                            <button class="btn btn-primary" name="btn" value="random">自動分組(隨機分組)</button>
                        </div>  
                        
                    </div>

                    
                    {!! Form::close() !!}

                    {!! Form::open(['method' => 'put', 'url' => "/admin/student_apply/group_edit/{$t04tb_info['class']}/{$t04tb_info['term']}", 'id' => 'edit_group']) !!}
                    <div class="row" style="margin-top:10px;">
                        <div class="table-responsive margin_top_bottom10" style="height:700px;" >
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th width="100">組別</th>
                                    <th>學號</th>
                                    <th>姓名</th>
                                    <th>性別</th>
                                    <th>年齡</th>
                                    <th>學歷</th>
                                    <th>機關</th>
                                    <th>主管機關</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($t13tbs as $t13tb)
                                        <tr>
                                            <td><input type="text" class="form-control" name="groups[{{ $t13tb->idno }}]" value="{{ $t13tb->groupno }}"></td>
                                            <td>{{ $t13tb->no }}</td>
                                            <td>{{ $t13tb->m02tb->cname }}</td>
                                            <td>
                                            @if (!empty(config('database_fields.m02tb.sex')[$t13tb->m02tb->sex]))
                                            {{ config('database_fields.m02tb.sex')[$t13tb->m02tb->sex] }}
                                            @endif 
                                            </td>
                                            <td>{{ $t13tb->age }}</td>
                                            <td>
                                            @if (!empty(config('database_fields.m02tb.ecode')[$t13tb->ecode]))
                                            {{ config('database_fields.m02tb.ecode')[$t13tb->ecode]}}
                                            @endif 
                                            </td>
                                            <td>
                                            @if (isset($t13tb->m02tb->m17tb))
                                            {{ $t13tb->m02tb->m17tb->enrollname }}
                                            @endif 
                                            </td>
                                            <td>
                                            @if (isset($t13tb->m02tb->m13tb))
                                            {{ $t13tb->m02tb->m13tb->lname }}
                                            @endif 
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>                        
                        </div> 
                    </div>
                    {!! Form::close() !!}
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-sm btn-primary" onclick="$('#edit_group').submit()"><i class="fa fa-save"></i> 儲存</button>
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