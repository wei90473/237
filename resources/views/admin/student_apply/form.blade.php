@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_apply';?>
<style>
input{
    /* min-width:1px; */
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
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員報名處理</h3>
                    </div>
                    @if($action == "create")
                    {!! Form::model($t13tb, array('url' => "/admin/student_apply/{$t04tb->class}/{$t04tb->term}/{$identity}", 'method'=>'post')) !!}    
                    @elseif ($action == "change_student")
                    {!! Form::model($t13tb, array('url' => "/admin/student_apply/changeStudent/{$t04tb->class}/{$t04tb->term}/{$des_idno}",'method'=>'put')) !!}    
                    @elseif ($action == "edit")
                    {!! Form::model($t13tb, array('url' => "/admin/student_apply/{$t13tb->class}/{$t13tb->term}/{$t13tb->des_idno}",'method'=>'put')) !!} 
                    @endif
                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        @if(!empty($t13tb->class))
                        <div class="row col-12" style="margin-top:10px;">
                            <div>
                                <button type="button" class="btn btn-primary" onclick="showChangeStudentModol()">換人</button>
                                <button type="button" class="btn btn-primary" onclick="showChangetTermModol()">調期</button>
                            </div>                         
                        </div>
                        @endif
                        <div>
                            @if ($t13tb->m02tb->identity == 1)
                                @include('admin.student_apply.form_government')
                            @elseif($t13tb->m02tb->identity == 2)
                                @include('admin.student_apply.form_general')
                            @endif 
                        </div>                                                                                   
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save"></i> 儲存</button>
                        @if($action == "edit")
                            <button type="button" class="btn btn-sm btn-danger" onclick="$('#deleteForm').submit()"><i class="fa fa-trash"></i>刪除</button> 
                        @endif 
                        <a href="/admin/student_apply/{{ $t04tb->class }}/{{ $t04tb->term }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>  
                    {!! Form::close() !!} 
                    {!! Form::open(['id' => 'deleteForm', 'method' => 'delete', 'url' => "/admin/student_apply/{$t13tb->class}/{$t13tb->term}/{$t13tb->des_idno}", 'onsubmit' => 'return confirm("確定要刪除此報名資料嗎？")' ]) !!}
                               
                    {!! Form::close() !!}                                        
                </div>
            </div>
        </div>
    </div>
</div>
@if(isset($t13tb))
<!-- 換人彈跳視窗 -->
<div id="change_student" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">換員</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'post' , 'url' => "/admin/student_apply/redirectChangeStudent/{$t13tb->class}/{$t13tb->term}"]) !!}
                <div class="search-float">
                    <div>
                        <div class="input-group col-12" style="padding-bottom: 10px;">
                            <div class="input-group-prepend">
                                <label class="input-group-text">新學員身分證</label>
                            </div>
                            <input type="text" name="new_student" class="form-control" autocomplete="off">
                            <input type="hidden" name="old_student" value="{{$t13tb->idno}}">
                        </div> 
                        <div class="input-group col-12" style="padding-bottom:0px;">     
                            <button class="btn mobile-100 mb-3 mb-md-0">送出</button>
                            <button type="button" data-dismiss="modal" class="btn btn-danger mobile-100 mb-3 mb-md-0">取消</button>                                                                                                                                                                                                                   
                        </div>
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>
            <div id="wrapper">

            </div>            
        </div>
    </div>
</div>
<!-- 換人彈跳視窗 -->
<!-- 換期彈跳視窗 -->
<div id="change_term" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">換期</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'put' , 'url' => "/admin/student_apply/changeTerm/{$t13tb->class}/{$t13tb->term}/{$t13tb->des_idno}"]) !!}
                <div class="search-float">
                    <div>
                        <div class="input-group col-12" style="padding-bottom: 10px;">
                            <div class="input-group-prepend">
                                <label class="input-group-text">期別</label>
                            </div>
                            <select name="new_term" class="form-control select2">
                                @if(!empty($t04tb->terms))
                                    @foreach($t04tb->terms as $change_t04tb)
                                        <option value="{{ $change_t04tb->term }}">{{ $change_t04tb->term }}</option>
                                    @endforeach 
                                @endif
                            </select>
                        </div> 
                        <div class="input-group col-12" style="padding-bottom:0px;">     
                            <button class="btn mobile-100 mb-3 mb-md-0">送出</button>
                            <button type="button" data-dismiss="modal" class="btn btn-danger mobile-100 mb-3 mb-md-0">取消</button>                                                                                                                                                                                                                   
                        </div>
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>
            <div id="wrapper">

            </div>            
        </div>
    </div>
</div>
<!-- 換期彈跳視窗 -->
@endif

@endsection

@section('js')
<script>
    $(document).ready(function() {
        $("input[name=dropdate]").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
    })
    computeAge();
    function computeAge(){
        let class_year = {{ (int)substr($t04tb->class, 0, 3) }};
        let birth = $("input[name='m02tb[birth]']").val();
        if (birth.length == 7){
            let year = parseInt(birth.substr(0,3));
            let age = class_year - year;
            $("input[name=age").val(age);
        }
    }

    function showChangeStudentModol()
    {
        $("#change_student").modal('show');
    }

    function showChangetTermModol()
    {
        $("#change_term").modal('show');
    }

    function status_change(status)
    {
        console.log(status);

        if (status == 2){
            $("input[name=not_present_notification]").attr('disabled', false);
        }else{
            $("input[name=not_present_notification]").attr('disabled', true);
        }

        if (status == 3){
            $("input[name=dropdate]").attr('disabled', false);
            $("input[name=droptime]").attr('disabled', false);            
        }else{
            $("input[name=dropdate]").attr('disabled', true);
            $("input[name=droptime]").attr('disabled', true);             
        }
        
        if (status == 1){
            $("input[name=authorize][value=Y]").attr('checked', true);
        }else{
            $("input[name=authorize][value=N]").attr('checked', true);            
        }
           
        
    }
</script>
@endsection