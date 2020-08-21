@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
  

    <?php $_menu = 'webbookplace';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路預約場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">網路預約場地審核處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>網路預約場地審核處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <!--認證時數資料匯出-->
                                    <form id="form_two"  action="{{route('webbook.Nantou.post')}}" method="post">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    
                                        <div class="input-group col-6">
                                            <label class="mt-1 mr-2">日期:</label>
                                            <input type="text" id="sdate3" name="date1" class="form-control" autocomplete="off" >
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                            <span class="mt-1 ml-2 mr-2">到</span>
                                            
                                            <input type="text" id="edate3" name="date2" class="form-control" autocomplete="off">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <div class="col-md-12 form-inline pt-4">
                                            <label>申請單位:</label>
                                            <div class="col-md-2">
                                                <input class="form-control"type="text" name="orgname" id="orgname" value="{{isset($condition['orgname'])? $condition['orgname'] :''}}">
                                            </div>
                                            <label>申請人姓名:</label>
                                            <div class="col-md-4">
                                                <input class="form-control"type="text" id="applyuser" name="applyuser"value="{{isset($condition['applyuser'])? $condition['applyuser'] :''}}" >
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-inline pt-4">
                                            <label>申請單處理狀態:</label>
                                            <?php
                                                $ck1="checked";$ck2='';$ck3='';$ck4='';$ck5='';$ck6='';
                                                if(isset($condition["status"])){
                                                    switch($condition["status"]){
                                                        case 1:
                                                            $ck1="checked";
                                                            break;
                                                        case 2:
                                                            $ck2="checked";
                                                            break;
                                                        case "F":
                                                            $ck3="checked";
                                                            break;
                                                        case "N":
                                                            $ck4="checked";
                                                            break;
                                                        case "T":
                                                            $ck5="checked";
                                                            break;
                                                        case "0":
                                                            $ck6="checked";
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                }
                                            ?>
                                            <div class="col-md-10">
                                                <input class="form-control"type="radio" name="status" value="1" {{$ck1}}>可以使用
                                                <input class="form-control"type="radio" name="status" value="2" {{$ck2}}>審核中
                                                <input class="form-control"type="radio" name="status" value="F" {{$ck3}}>可以借用未收費
                                                <input class="form-control"type="radio" name="status" value="N" {{$ck4}}>尚未處理
                                                <input class="form-control"type="radio" name="status" value="T" {{$ck5}}>可以借用已收費
                                                <input class="form-control"type="radio" name="status" value="0" {{$ck6}}>無法借用
                                            </div>
                                        </div>
                                        
                                       
                                        <div class="col-xs-12 ml-2 mt-3 mb-3">
                                            <button class="btn" style="background-color:#317eeb;color:white;" type="submit">查詢</button>
                                            <button class="btn" style="background-color:#317eeb;color:white;" type="button" onclick="resetFun()">重設條件</button>
                                            <a href="{{route('webbook.arg.get')}}" target="_blank"><button class="btn" style="background-color:#317eeb;color:white;" type="button">批次折扣設定</button></a>
                                            <a href="{{route('webbook.parameter.get')}}" target="_blank"><button class="btn" style="background-color:#317eeb;color:white;" type="button">參數設定</button></a>
                                        </div>

                                    </form>
                                </div>
                                <div class="col-md-12">
                                    <table class="table table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">功能</th>
                                                <th class="text-center">申請日期</th>
                                                <th class="text-center">申請單位</th>
                                                <th class="text-center">處理狀態</th>
                                                <th class="text-center">收費情形</th>
                                                <th class="text-center">折扣</th>
                                                <th class="text-center">實付費用</th>
                                                <th class="text-center">借用場地</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <?php if(!empty($result)){?>
                                        <tbody>
                                            <?php foreach($result as $row){?>
                                            <?php if($row['id']!=null){?>
                                            <tr class="text-center">
                                                <td><a href="{{route('webbook.edit.get',$row['applyno'])}}?applydate={{$row['applydate']}}"><button class="btn btn-info">編輯</button></a>
                                                <button class="btn btn-info" onclick='deleteFun("{{route('webbook.delete.delete',$row['applyno'])}}")'>刪除</button></td>
                                                <td>{{$row["applydate"]}}</td>
                                                <td>{{$row["orgname"]}}</td>
                                                <?php
                                                    switch($row["status"]){
                                                        case 1:
                                                            $status="可以借用";
                                                            break;
                                                        case 2:
                                                            $status="審核中";
                                                            break;
                                                        case "F":
                                                            $status="可以借用未收費";
                                                            break;
                                                        case "N":
                                                            $status="尚未處理";
                                                            break;
                                                        case "T":
                                                            $status="可以借用已收費";
                                                            break;
                                                        case "0":
                                                            $status="無法借用";
                                                            break;
                                                        default:
                                                            $status="";
                                                            break;
                                                    }
                                                ?>
                                                <td>{{$status}}</td>
                                                <?php 
                                                    if($row["applykind"]==1){
                                                        if($row["paydate"]!=""){
                                                            $applykind="已收費";
                                                        }else{
                                                            $applykind="未收費";
                                                        }
                                                    }else{
                                                        $applykind="免收費";
                                                    }
                                                ?>
                                                <td>{{$applykind}}</td>
                                                <td>{{$row["discount"]}}</td>
                                                <td>{{$row["fee"]}}</td>
                                                <td>{{$row["detail"]}}筆明細</td>
                                                <td><a href="{{route('webbook.change.get')}}"><button class="btn btn-light">發送異動通知</a></td>
                                            </tr>
                                            <?php }?>
                                            <?php }?>
                                        </tbody>
                                        <?php } ?>
                                    </table>
                                </div>
                                @include('admin/layouts/list/pagination', ['paginator' => $result, 'queryData' => $condition])

                            </div>
                        </div>
                        <!-- 列表頁尾 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    //匯出記錄查詢 搜尋ajax 
    $(document).ready(function() {
        $("#sdate3").datepicker({
            format: "twy/mm/dd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#sdate3").focus();
        });
        $("#edate3").datepicker({
            format: "twy/mm/dd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#edate3").focus();
        });

        var sdate3 = "<?= isset($condition['date1'])? $condition['date1'] :''?>";
        if(sdate3 != ''){
            var sdate3 = sdate3.substr(0,3)+'/'+sdate3.substr(3,2)+'/'+sdate3.substr(5,2);
        }
        var edate3 = "<?= isset($condition['date2'])? $condition['date2'] :''?>";
        if(edate3 != ''){
            var edate3 = edate3.substr(0,3)+'/'+edate3.substr(3,2)+'/'+edate3.substr(5,2);
        }
        $("#sdate3").val(sdate3);
        $("#edate3").val(edate3);
        
    });
    
    //檢查輸入時間
    function checkdate()
    {
        var sdate=$("#sdate3").val();
        var edate=$("#edate3").val();

        if(sdate>edate || sdate=='' ||edate==''){
            return false;
        }else{
            return true;
        }
    }

    function deleteFun(url)
    {
        if(confirm('是否確認刪除？')){
            location.href = url;
        }

        return false;
    }

    function resetFun()
    {
        document.all.sdate3.value = "";
        document.all.edate3.value = "";
        document.all.orgname.value = "";
        document.all.applyuser.value = "";
    }
    

    //認證時數清除
    function clear_condition()
    {   
        document.all.sdate3.value = " ";
        document.all.edate3.value = " ";
        document.all.final_sdate2.value = " ";
        document.all.final_edate2.value = " ";
        //alert('why');
    }


    function print()
    {
        var sdate3=$("#sdate3").val();
        var edate3=$("#edate3").val();
        var find = '/';
        var re = new RegExp(find, 'g'); 
        sdate3=sdate3.replace(re,"");
        edate3=edate3.replace(re,"");

        message=checkdate();
        if(message){
            $("#final_sdate2").val(sdate3);
            $("#final_edate2").val(edate3);
        
            $("#form_two").attr("action","/admin/libraryexport/export/1");
            $("#form_two").submit();
        
            $("#form_two").attr("action","/admin/libraryexport/export/2");
            $("#form_two").submit();
        
            $("#form_two").attr("action","/admin/libraryexport/export/3");
            $("#form_two").submit();

            $("#form_two").attr("action","/admin/libraryexport/export/4");
            $("#form_two").submit();
        }else{
            alert("時間輸入有誤");
        }
        
    }

</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

