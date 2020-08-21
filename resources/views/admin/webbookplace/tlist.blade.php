@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
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
                                    {!! Form::open([ 'method'=>'post', 'url'=>'/admin/webbookplaceTaipei/', 'id'=>'form_two']) !!}
                                        <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->
                                    
                                        <div class="input-group col-6">
                                            <label class="mt-1 mr-2">日期:</label>
                                            <input type="text" id="sdate3" name="date1" class="form-control" autocomplete="off"  value="{{isset($condition['date1'])? $condition['date1'] : $default['sdate']}}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                            <span class="mt-1 ml-2 mr-2">到</span>
                                            
                                            <input type="text" id="edate3" name="date2" class="form-control" autocomplete="off" value="{{isset($condition['date2'])? $condition['date2'] : $default['edate']}}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <div class="col-md-12 form-inline pt-4">
                                            <label>申請單位:</label>
                                            <div class="col-md-2">
                                                <input class="form-control"type="text" name="name" id="name" value="{{isset($condition['name'])? $condition['name'] :''}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12 form-inline pt-4">
                                            <label>申請單處理狀態:</label>
                                            <?php
                                                $ck1="";$ck2='';$ck3='';$ck4='';$ck5='';$ck6='';
                                                if(isset($condition["result"])){
                                                    switch($condition["result"]){
                                                        case "":
                                                            $ck1="checked";
                                                            break;
                                                        case "Y":
                                                            $ck2="checked";
                                                            break;
                                                        case "ALL":
                                                            $ck3="checked";
                                                            break;
                                                        case "N":
                                                            $ck4="checked";
                                                            break;
                                                        case "C":
                                                            $ck5="checked";
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                }
                                            ?>
                                            <div class="col-md-10">
                                                <input class="form-control"type="radio" name="result" value=""  {{isset($condition["result"])?$ck1 :'checked'}}>未處理
                                                <input class="form-control"type="radio" name="result" value="Y" {{$ck2}}>同意
                                                <input class="form-control"type="radio" name="result" value="N" {{$ck4}}>駁回
                                                <input class="form-control"type="radio" name="result" value="C" {{$ck5}}>逾期取消
                                                <input class="form-control"type="radio" name="result" value="ALL" {{$ck3}}>全部
                                            </div>
                                        </div>
                                        <!-- 每頁幾筆 -->
                                        <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{isset($condition['_paginate_qty'])?$condition['_paginate_qty']:''  }}">
                                       
                                        <div class="col-xs-12 ml-2 mt-3 mb-3">
                                            <button class="btn" style="background-color:#317eeb;color:white;" type="submit">查詢</button>
                                            <button class="btn" style="background-color:#317eeb;color:white;" type="button" onclick="resetFun()">重設條件</button>
                                            <!-- <a href="{{route('webbook.parameter.get')}}" target="_blank"><button class="btn" style="background-color:#317eeb;color:white;" type="button">場地明細</button></a> -->
                                            <a href="/admin/bookplace/index" target="_blank"><button class="btn" style="background-color:#317eeb;color:white;" type="button">場地明細</button></a>
                                        </div>

                                    {!! Form::close() !!}      
                                </div>
                                <div class="col-md-12">
                                    <table class="table table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">功能</th>
                                                <th class="text-center">審核狀態</th>
                                                <th class="text-center">申請編號</th>
                                                <th class="text-center">申請單位</th>
                                                <th class="text-center">聯絡人</th>
                                                <th class="text-center">電話</th>
                                                <th class="text-center">回覆日期</th>
                                                <th class="text-center">費用</th>
                                                <th class="text-center">繳費截止日</th>
                                            </tr>
                                        </thead>
                                        @if(isset($result))
                                        <tbody>
                                            @foreach($result as $row)
                                            <tr class="text-center">
                                                <td><a href="/admin/webbookplaceTaipei/edit/{{$row['meet'].$row['serno']}}"><button class="btn btn-info">編輯</button></a></td>
                                                <?php
                                                    switch($row["result"]){
                                                        case "":
                                                            $status="未處理";
                                                            break;
                                                        case "Y":
                                                            $status="同意";
                                                            break;
                                                        case "N":
                                                            $status="駁回";
                                                            break;
                                                        case "C":
                                                            $status="逾期取消";
                                                            break;
                                                        default:
                                                            break;
                                                    }
                                                ?>
                                                @if($row["result"]=='')
                                                <td><button class="btn btn-info" onclick="Audit('{{$row["meet"].$row["serno"]}}')">{{$status}}</button></td>
                                                @else
                                                <td>{{$status}}</td>
                                                @endif
                                                <td>{{$row["meet"].$row["serno"]}}</td>
                                                <td>{{$row["name"]}}</td>
                                                <td>{{$row["liaison"]}}</td>
                                                <td>{{$row["telno"]}}</td>
                                                <td>{{$row["replydate"]}}</td>
                                                <td>{{$row["totalfee"]}}</td>
                                                <td>{{$row["duedate"]}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        @endif
                                    </table>
                                </div>
                                @if(isset($result))
                                <!-- 分頁 -->
                                @include('admin/layouts/list/pagination', ['paginator' => $result, 'queryData' => $condition])
                                @endif
                            </div>
                        </div>
                        <!-- 列表頁尾 -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 場地核定 modal-->
    <div class="modal fade" id="AuditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog" role="document">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/webbookplaceTaipei/audit',  'id'=>'form1']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" name="key" value="">
                            <!-- 審核狀態 -->
                            <div class="col-md-12">
                                <label class="control-label pt-2">審核狀態</label>
                            </div>
                            <div class="col-md-12">
                                <input type="radio" name="result" value="Y">同意
                                <input type="radio" name="result" value="N">駁回
                                <input type="radio" name="result" value="C">逾期取消
                            </div>
                        </div>
                        <!-- 回覆意見 -->
                        <fieldset style="padding: inherit;page-break-after:always">
                                <!-- <legend>預租借之場地</legend> -->
                                <div class="form-group row">
                                    <div class="col-md-10">
                                        <b>注意！當註記為【駁回】、【逾期取消】時，將清除費用及繳費截止日期。</b>
                                    </div>
                                </div>
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionAudit()">確定</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </div>
                {!! Form::close() !!}       
            </div>
        </div>
    </div>      
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    //匯出記錄查詢 搜尋ajax 
    $(document).ready(function() {
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

        // var sdate3 = "<?= isset($condition['date1'])? $condition['date1'] :''?>";
        // if(sdate3 != ''){
        //     var sdate3 = sdate3.substr(0,3)+'/'+sdate3.substr(3,2)+'/'+sdate3.substr(5,2);
        // }
        // var edate3 = "<?= isset($condition['date2'])? $condition['date2'] :''?>";
        // if(edate3 != ''){
        //     var edate3 = edate3.substr(0,3)+'/'+edate3.substr(3,2)+'/'+edate3.substr(5,2);
        // }
        // $("#sdate3").val(sdate3);
        // $("#edate3").val(edate3);
        
    });
    //場地審核
    function Audit(key){
        $('input[name=key]').val(key);
        $("#AuditModal").modal('show');
    }

    function actionAudit(){
    if( $('input[name=result]').val()==undefined ){
        alert('請選擇!');
        return false;
    }else{
        $("#form1").submit();
    }
}
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

    function resetFun()
    {
        document.all.sdate3.value = "";
        document.all.edate3.value = "";
        document.all.name.value = "";
        document.all.prove.value = "ALL";
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

