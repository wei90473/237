@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <style>
        .halfArea {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .arrow {
            font-size: 30px !important;
            color: #969696;
            padding: 10px;
            cursor: pointer;
        }
        .arrow:hover {
            color: #696969;
        }
        /*.item_con {
            display: flex;
            align-items: center;
        }
        .item_con label {
            cursor: pointer;
        }*/
        .item_con.active {
            background-color: #d2f1ff;
        }
        .arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>

    <?php $_menu = 'reportmg';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">重要訊息維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/reportmg" class="text-info">重要訊息維護</a></li>
                        <li class="active">重要訊息維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data))
                {!! Form::open([ 'method'=>'post', 'url'=>"/admin/reportmg/edit/{$data['id']}", 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>"/admin/reportmg/create", 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">重要訊息維護</h3></div>
                    <div class="card-body pt-4">

                        <input type="hidden" id="_method"name="_method" value="">

                        <!-- 標題 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">標題</label>
                            <div class="col-sm-4 pl-4">
                                <input type="text" id="title" name="title" value="{{ isset($data['title'])? $data['title'] : '' }}" required>
                            </div>
                        </div>
                        

                        <!-- 顯示位置 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">顯示位置</label>
                            <div class="col-sm-4 pl-4 pt-2">
                                <?php 
                                    $control_1=''; $control_2='';
                                    if(isset($data['position'])){
                                  
                                        if($data['position']=='loginbefore'){
                                            $control_1='checked';
                                            
                                        }else{
                                            $control_2='checked';
                                        }
                                    }else{
                                        $control_1='checked';
                                    }
                                ?>
                                <input type="radio" id="position" name="position" value="loginbefore" {{$control_1}}>登入前顯示
                                <input type="radio" id="position" name="position" value="loginafter" {{$control_2}}>登入後顯示
                            </div>
                        </div>

                        <!-- 顯示對象 -->
                        <div class="form-group row"  id ='position_div'>
                            <label class="col-sm-2 control-label text-md-right pt-2">顯示對象</label>
                            <div class="col-sm-4 pl-4 pt-2">
                                <?php $control_3=''; $control_4=''; $control_5=''; $control_6='';
                                if(isset($data['for'])){
                                    $temp_for=explode(",",$data['for']);
                                    for($i=0;$i<count($temp_for);$i++){
                                        if($temp_for[$i]=='student'){
                                            $control_3='checked';
                                        }
                                        if($temp_for[$i]=='teacher'){
                                            $control_4='checked';
                                        }
                                        if($temp_for[$i]=='practice'){
                                            $control_5='checked';
                                        }
                                        if($temp_for[$i]=='contactor'){
                                            $control_6='checked';
                                        }
                                    }
                                }
                                    
                                ?>
                                <input type="checkbox" id="for" name="for[]" value="student" {{$control_3}}>學員
                                <input type="checkbox" id="for" name="for[]" value="teacher" {{$control_4}}>講座
                                <input type="checkbox" id="for" name="for[]" value="practice" {{$control_5}}>訓練承辦人
                                <input type="checkbox" id="for" name="for[]" value="contactor" {{$control_6}}>委訓單位承辦人
                            </div>
                        </div>

                        <!-- 上架日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上架日期</label>
                            <div class="col-sm-5">
                                <div class="input-group roc-date input-max">
                                    <div class="input-group col-sm-10">
                                        <input type="text" id="sdate" name="launch" class="form-control number-input-max" autocomplete="off" value="{{ isset($data['launch'])? $data['launch'] : '' }}" required>
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 下架日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">下架日期</label>
                            <div class="col-sm-5">
                                <div class="input-group roc-date input-max">
                                    <div class="input-group col-sm-10">                                        
                                        <input type="text" id="edate" name="discontinue" class="form-control number-input-max" autocomplete="off" value="{{ isset($data['discontinue'])? $data['discontinue'] : '' }}" required>
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 使用彈跳視窗 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right  pl-4 pt-2">使用彈跳視窗</label>
                            <div class="col-sm-5 pt-2 pl-4">
                                <?php $control=''; if(isset($data['opener'])){if($data['opener']=='on'){$control='checked';}}?>
                                <input type="checkbox" id="opener" name="opener" {{$control}}>
                            </div>
                        </div>

                        <!-- 內容 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right  pl-4 pt-2">內容</label>
                            <div class="col-sm-8 pt-2 pl-4">
                                <textarea rows="5" cols="40" maxlength="1000" id="content" name="content" required>{{isset($data['content'])? $data['content'] : '' }}</textarea>
                            </div>
                        </div>
                        
                    </div>

                    <div class="card-footer">
                        <button type="button"  class="btn btn-sm btn-info" onclick="return checkform();"><i class="fa fa-save pr-2"></i>儲存</button>
                        <?php if(isset($data)){?>
                            <button type="button"  class="btn btn-sm btn-danger" onclick="return deleteform();">刪除</button>
                        <?php }?>
                        <a href="/admin/reportmg">
                            <button type="button" class="btn btn-sm btn-danger">返回</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    
   
    

@endsection

@section('js')

<script type= text/javascript>
$(document).ready(function() {

        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });
        $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });
        $("#sdate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#sdate2").focus();
        });
        $("#edate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#edate2").focus();
        });
        $("#sdate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#sdate3").focus();
        });
        $("#edate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#edate3").focus();
        });
        
        $('input[name="position"]').change(function(){
        if ($('input[name="position"][value="loginbefore"]').prop("checked")) {
                $("#position_div").hide();
                $("#opener").attr("disabled",false);
            } else {
                $("#opener").prop("checked", true);
                $("#position_div").show();
                $("#opener").attr("disabled",true);
            }
        })
         if ($('input[name="position"][value="loginbefore"]').prop("checked")) {
            $("#position_div").hide();
            $("#opener").attr("disabled",false);
        } else {
            $("#opener").prop("checked", true);
            $("#position_div").show();
            $("#opener").attr("disabled",true);
        }
        

    });
</script>

<script>
    var id=<?php 
            if(isset($id)){
                echo $id;
            }else{
                $id="'add'";
                echo $id;
            } ?>;
    function checkform()
    {
        var title_info = $("#title").val();
        console.log(title_info);
        if(title_info ==''){
            alert("請填寫標題");
            return false;
        }

        var for_info = $("input:checkbox:checked[name='for[]']").val();
        if(for_info == null || for_info == undefined){           
            if ($('input[name="position"][value="loginafter"]').prop("checked")) {
                alert("請勾選顯示對象");
                return false;
            }          
           
        }

        var launch_info = $("#sdate").val();
        console.log(launch_info);
        if(launch_info ==''){
            alert("請填寫上架日期");
            return false;
        }

        var discontinue = $("#edate").val();
        if(discontinue ==''){
            alert("請填寫下架日期");
            return false;
        }

        if(launch_info>discontinue){
            alert("上架日期不可超過下架日期");
            return false;
        }
        
        var content = $("#content").val();
        if(content ==''){
            alert("請填寫內容");
            return false;
        }

        
        
        
        $("#_method").val("post");
        $("form").submit();
    }

    function deleteform()
    {
        //$("#form").attr("method",'delete');
        $("#form").attr("action","/admin/reportmg/edit/"+id);
        $("#_method").val("delete");
        $("#form").submit();
    }


</script>

@endsection