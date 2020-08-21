@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'reportmg';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">重要訊息維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active"></li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>重要訊息維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                    <!-- 搜尋 -->
                                <div class="float-left search-float">
                                    <form method="get" id="search_form" action='/admin/reportmg'>
                                            <div class="col-md-12">
                                                <label>標題:</label>
                                                <input type="text" id="title" name="title" value="{{isset($condition['title'])?$condition['title'] :'' }}">
                                            </div> 

                                            <div class="col-md-12">
                                                <label>顯示位置:</label>
                                                <?php 
                                                    $check2='';$check3='';
                                                    if(isset($condition['position'])){
                                                        if($condition['position']=='loginbefore'){
                                                            $check2='selected';
                                                        }

                                                        if($condition['position']=='loginafter'){
                                                            $check3='selected';
                                                        }
                                                    }
                                                ?>
                                                <select id="position" name="position">
                                                    <option>不限</option value="loginbefore">
                                                    <option value="loginbefore" {{$check2}}>登入前</option>
                                                    <option value="loginafter" {{$check3}}>登入後</option>
                                                </select>
                                            </div>

                                            <div class="float-left ">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-3"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <button type="button" class="btn mobile-100 mb-3 mb-3" onclick="clean();">重設條件</button>
                                                <a href="/admin/reportmg/create">
                                                    <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                                </a>
                                            </div>
                                    </form>
                                </div>

                                    

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="5%">功能</th>
                                                <th class="text-center">顯示位置</th>
                                                <th class="text-center">對象</th>
                                                <th class="text-center">標題</th>
                                                <th class="text-center">使用彈跳視窗</th>
                                                <th class="text-center">上架時間</th>
                                                <th class="text-center">下架時間</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($data as $temp){?>
                                            <?php
                                                $position='登入後顯示';
                                                if($temp['position']=='loginbefore'){
                                                    $position='登入前顯示';
                                                }
                                                $temp2=explode(",",$temp['for']);
                                                $for=[];
                                                for($i=0;$i<count($temp2);$i++){
                                                    if($temp2[$i]=='student'){
                                                        $for[$i]='學員';
                                                    }
                                                    if($temp2[$i]=='teacher'){
                                                        $for[$i]='講座';
                                                    }
                                                    if($temp2[$i]=='practice'){
                                                        $for[$i]='訓練承辦人';
                                                    }
                                                    if($temp2[$i]=='contactor'){
                                                        $for[$i]='委訓單位承辦人';
                                                    }
                                                }
                                                $final_for=implode(",",$for);
                                                $opener='否';
                                                if($temp['opener']=='on'){
                                                    $opener='是';
                                                }
                                            ?>
                                                <tr class="text-center">
                                                    <td>
                                                        <a href="/admin/reportmg/edit/{{$temp['id']}}">
                                                            <button class="btn btn-primary btn-sm ">編輯</button>
                                                        </a>
                                                    </td>
                                                    <td>{{$position}}</td>
                                                    <td>{{$final_for}}</td>
                                                    <td>{{$temp['title']}}</td>
                                                    <td>{{$opener}}</td>
                                                    <td>{{$temp['launch']}}</td>
                                                    <td>{{$temp['discontinue']}}</td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- 分頁 -->
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>

<script>
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
        $("#date1").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker1').click(function(){
            $("#date1").focus();
        });
        $("#date2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker2').click(function(){
            $("#date2").focus();
        });
        $("#date3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker3').click(function(){
            $("#date3").focus();
        });
        $("#date6").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker6').click(function(){
            $("#date6").focus();
        });
        $("#date5").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker5').click(function(){
            $("#date5").focus();
        });
    });
function clean()
{   
    var empty='';
    $("#title").val(empty);
    $("#position").val(empty);
}
</script>