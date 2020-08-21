@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

<style>
        /* .item_con {
            display: flex;
            align-items: center;
        } */

        @media print{    
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
        .display_inline {
            display: inline-block;
            margin-right: 5px;
        }
        .halfArea {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0px 5px;
        }
        .item_con label {
            cursor: pointer;
        }
        .item_check.active, .item_uncheck.active {
            background-color: #d2f1ff;
        }
        .arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
    <?php $_menu = 'webbookplace';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show no-print">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路預約場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/webbookplace" class="text-info">網路預約場地審核處理</a></li>
                        <li class="active">網路預約場地審核處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if (!empty($edu_loanplace) )
                {!! Form::open([ 'method'=>'put', 'route'=>array("webbook.edit.put",$applyno), 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">網路預約場地審核處理</h3></div>
                        <div class="card-body pt-4" id="test">
                            <!-- 申請編號 日期-->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">申請單號:</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control input-max"  value="{{$applyno}}" disabled>
                                    <input type="hidden" id="applyno" name="applyno" value="{{$applyno}}">
                                </div >
                                
                                <label class="col-form-label text-md" >申請日期:<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <div class="input-group col-6">
                                        <input type="text" id="sdate3" name="applydate" class="form-control" autocomplete="off" >
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>

                            <!--活動名稱 人數 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2"><span class="text-danger">*</span>活動名稱(事由):</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="reason" id="reason" value="{{isset($edu_loanplace[0]['reason'])?$edu_loanplace[0]['reason']:''}}">
                                </div>
                                <label class="control-label text-md-right pt-2">人數</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="num" id="num" value="{{isset($edu_loanplace[0]['num'])?$edu_loanplace[0]['num']:''}}">
                                </div>
                            </div>

                            <!--單位類型-->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"><span class="text-danger">*</span>單位類型:</label>
                                <?php 
                                    $chec0='';$chec1='';$chec2='';$chec3='';
                                    switch($edu_loanplace[0]["applytype"]){
                                        case 0:
                                            $chec0="checked";
                                            break;
                                        case 1:
                                            $chec1="checked";
                                            break;
                                        case 2:
                                            $chec2="checked";
                                            break;
                                        case 3:
                                            $chec3="checked";
                                            break;
                                        default:
                                            break;
                                    }
                                ?>
                                <div class="col-md-5 mt-2">
                                    <input type="radio" value="0" {{$chec0}} name="applytype" id="applytype">政府機關
                                    <input type="radio" value="1" {{$chec1}} name="applytype" id="applytype">民間單位
                                    <input type="radio" value="2" {{$chec2}} name="applytype" id="applytype">受政府機關委託
                                    <input type="radio" value="3" {{$chec3}} name="applytype" id="applytype" >個人
                                </div>
                            </div>

                            <!--申請單位 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"><span class="text-danger">*</span>申請單位(服務機關):</label>
                                <div class="col-md-3">
                                    <input class="form-control" type="text" name="orgname" id="orgname" value="{{isset($edu_loanplace[0]['orgname'])?$edu_loanplace[0]['orgname']:''}}">
                                </div>
                            </div>
                            <!--收據抬頭 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"><span class="text-danger">*</span>收據抬頭:</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="receiptname" id="receiptname" value="{{isset($edu_loanplace[0]['receiptname'])?$edu_loanplace[0]['receiptname']:''}}">
                                </div>
                                <?php
                                    if($edu_loanplace[0]["is_need_receipt"]==0){
                                        $ck2='checked';
                                    }else{
                                        $ck2='';
                                    }
                                ?>
                                <div class="col-sm-2 pt-2">
                                    <input type="checkbox" name="is_need_receipt" id="is_need_receipt"{{$ck2}}>不需要收據
                                </div>
                                
                            </div>
                            <!-- 單位地址 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">單位地址:</label>
                                <div class="col-md-3">
                                    <input class="form-control" type="text" name="addr" id="addr" value="{{isset($edu_loanplace[0]['addr'])?$edu_loanplace[0]['addr']:''}}">
                                </div>
                            </div>

                            <!-- 聯絡人 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right "><span class="text-danger">*</span>聯絡人(申請人):</label>
                                <div class="col-md-3 pt-2">
                                    <input class="form-control" type="text" name="applyuser" id="applyuser" value="{{isset($edu_loanplace[0]['applyuser'])?$edu_loanplace[0]['applyuser']:''}}">
                                </div>
                            </div>

                            <!-- 職稱 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">職稱:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="title" id="title" value="{{isset($edu_loanplace[0]['title'])?$edu_loanplace[0]['title']:''}}">
                                </div>
                            </div>
                            <!-- 連絡電話 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"><span class="text-danger">*</span>連絡電話:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="tel" id="tel"  value="{{isset($edu_loanplace[0]['tel'])?$edu_loanplace[0]['tel']:''}}">
                                </div>
                            </div>

                            <!-- 傳真 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">傳真:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="fax" id="fax"  value="{{isset($edu_loanplace[0]['fax'])?$edu_loanplace[0]['fax']:''}}">
                                </div>
                            </div>

                            <!-- 行動電話 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">行動電話:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="cellphone" id="cellphone"  value="{{isset($edu_loanplace[0]['cellphone'])?$edu_loanplace[0]['cellphone']:''}}">
                                </div>
                            </div>

                            <!-- 電子信箱 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"><span class="text-danger">*</span>電子信箱:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="email" id="email"  value="{{isset($edu_loanplace[0]['email'])?$edu_loanplace[0]['email']:''}}">
                                </div>
                            </div>

                            <!-- 住宿人數 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">男住宿人數:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="mstay" id="mstay"  value="{{isset($edu_loanplace[0]['mstay'])?$edu_loanplace[0]['mstay']:''}}">
                                </div>
                                <label class="col-md-2 col-form-label text-md-right">女住宿人數:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="fstay" id="fstay"  value="{{isset($edu_loanplace[0]['fstay'])?$edu_loanplace[0]['fstay']:''}}">
                                </div>
                            </div>

                            <!-- 修改密碼 處理狀態 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"><span class="text-danger">*</span>修改密碼:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="passwd" id="passwd"  value="{{isset($edu_loanplace[0]['passwd'])?$edu_loanplace[0]['passwd']:''}}">
                                </div>
                                <label class="col-md-2 col-form-label text-md-right">處理狀態:</label>
                                <div class="col-md-3">
                                    <select class="custom-select" name="status" id="status" >
                                        <?php foreach($status as $s_key=>$s){?>
                                            <option value="{{$s_key}}" {{old("status",isset($edu_loanplace[0]["status"])? $edu_loanplace[0]["status"] : "") === $s_key? 'selected' : '' }}>{{$s}}</option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                         

                            <!-- 鎖定申請表 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">鎖定申請表:</label>
                                <div class="col-md-3">
                                    <select class="custom-select" name="locked" id="locked" >
                                        <?php foreach($lock as $l_key=>$l){?>
                                            <option value="{{$l_key}}" {{old("locked",isset($edu_loanplace[0]["locked"])? $edu_loanplace[0]["locked"] : "") == $l_key? 'selected' : '' }}>{{$l}}</option>
                                        <?php }?>
                                    </select>
                                </div>

                                <div class="col-md-3 mt-2">
                                    <?php 
                                        if($edu_loanplace[0]['inner_check']==1){
                                            $check='checked';
                                        }else{
                                            $check='';   
                                        }
                                    ?>
                                    <input type="checkbox"  name="inner_check" id="inner_check" {{$check}}>內部已完成簽核
                                </div>
                            </div>

                             <!-- 折扣方式 -->
                            <!--<div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">折扣方式:</label>
                                <div class="col-md-3">
                                    <select class="form-control" name="discounttype" id="discounttype" >
                                    <?php foreach($discount as $ds2){?>
                                        <option value="{{$ds2['code']}}" {{old("discounttype",isset($edu_loanplace[0]["discounttype"])? $edu_loanplace[0]["discounttype"] : "") == $ds2["code"]? 'selected' : '' }}>{{$ds2["name"]}}</option>
                                    <?php }?>
                                    </select>
                                </div>
                            </div>-->

                            <!--折扣-->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">平日折扣:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="discount1" id="discount1"  value="{{isset($edu_loanplace[0]['discount1'])?$edu_loanplace[0]['discount2']:''}}">
                                </div>
                                <label class="col-md-2 col-form-label text-md-right">假日折扣:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="discount2" id="discount2"  value="{{isset($edu_loanplace[0]['discount2'])?$edu_loanplace[0]['discount1']:''}}">
                                </div>
                            </div>

                            <!-- 金額確認 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">金額確認:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="confirm_fee" id="confirm_fee"  value="{{isset($edu_loanplace[0]['confirm_fee'])?$edu_loanplace[0]['confirm_fee']:''}}">
                                </div>
                                <div class="col-md-3 no-print">
                                    @if ($edu_loanplace[0]["status"] == 'T' || $edu_loanplace[0]["locked"] == '1')
                                    @else
                                    <button class="btn btn-info" type="button" onclick="confirmFee()">金額確認</button>
                                    @endif
                                </div>
                            </div>

                            <!-- 無法外借原因 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">無法外借原因:</label>
                                <div class="col-md-10">
                                    <textarea class="form-control input-max" rows="5" name="reason2" id="reason2">{{isset($edu_loanplace[0]['reason2'])?$edu_loanplace[0]['reason2']:''}}</textarea>
                                </div>
                            </div>

                            <!-- 備註 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">備註:</label>
                                <div class="col-md-10">
                                    <textarea class="form-control input-max" rows="5" maxlength="1000" name="description" id="description">{{isset($edu_loanplace[0]['description'])?$edu_loanplace[0]['description']:''}}</textarea>
                                </div>
                            </div>

                            <!--預租借之場地-->
                            <fieldset class="printuse" style="border:groove; padding: inherit;">
                                <legend>預租借之場地</legend>
                                <div class="form-group row">
                                    <div class="col-md-10">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">日期</th>
                                                    <th class="text-center">時間</th>
                                                    <th class="text-center">場地</th>
                                                    <th class="text-center">功能</th>
                                                    <th class="text-center">場地安排</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($place)){?>
                                                <?php foreach($place as $row){?>
                                                <tr class="text-center">
                                                    <td>{{substr($row["startdate"],0,3)}}/{{substr($row["startdate"],3,2)}}/{{substr($row["startdate"],5,2)}}~{{substr($row["enddate"],0,3)}}/{{substr($row["enddate"],3,2)}}/{{substr($row["enddate"],5,2)}}</td>
                                                    <td>{{substr($row["timestart"],0,2)}}時~{{substr($row["timeend"],0,2)+1}}時</td>
                                                    
                                                    <td>{{$row['croomclsfullname']}}</td>
                                                    <td><a href="{{route('webbook.place.get',$applyno)}}?mode=modify&id={{$row['id']}}"><button class="btn btn-light" type="button">編輯</button></a></td>
                                                    <td><a href="{{route('webbook.bed.get')}}?applyno={{$applyno}}&croomclsno={{$row['croomclsno']}}&id={{$row['id']}}&applydate={{$applydate}}">
                                                        <button class="btn btn-light" type="button">
                                                            <?php if($row["loansroom_count"]!=0){
                                                                $button_show=$row["loansroom_count"]."床";
                                                            }else if($row["loanroom_count"]!=0){
                                                                $button_show=$row["loanroom_count"]."間";
                                                            }else{
                                                                $button_show='安排';
                                                            }
                                                            ?>
                                                            {{$button_show}}
                                                        </button></a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if ($edu_loanplace[0]["status"] == 'T' || $edu_loanplace[0]["locked"] == '1')
                                @else
                                <a href="{{route('webbook.place.get',$applyno)}}"><button class="btn btn-info no-print" type="button">新增場地</button></a>  
                                @endif  
                            </fieldset>
                            <br>
                            <!--折扣及費用-->
                            <fieldset class="printuse" style="border:groove; padding: inherit;">
                                <legend>折扣及費用</legend>
                                <!-- 折扣方式 -->
                                <div class="form-group row">
                                    <label class="col-md-1">折扣方式:</label>
                                    <div class="col-md-3 mb-1">
                                        <?php if(!empty($discount)){?>
                                            <select class="custom-select tmpselect" name="discounttype">
                                                <?php foreach($discount as $ds){?>
                                                <option value="{{$ds['code']}}"  {{old("discounttype",isset($edu_loanplace[0]["discounttype"])? $edu_loanplace[0]["discounttype"] : "") == $ds["code"]? 'selected' : '' }}>{{$ds["name"]}}</option>
                                                <?php }?>
                                            </select>
                                        <?php }?>
                                    </div>
                                    <input type="hidden" name="batch" id="batch" value="0">
                                    @if ($edu_loanplace[0]["status"] == 'T' || $edu_loanplace[0]["locked"] == '1')
                                    @else 
                                    <button class="btn btn-info no-print" type="button" onclick="batch_submit();">批次折扣</button>
                                    @endif
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-10">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">日期</th>
                                                    <th class="text-center">時間</th>
                                                    <th class="text-center">場地</th>
                                                    <th class="text-center">假日</th>
                                                    <th class="text-center">借用費用</th>
                                                    <th class="text-center">平日折扣</th>
                                                    <th class="text-center">假日折扣</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(!empty($discount_data)){?>
                                                    <?php foreach($discount_data as $drow){?>
                                                    <tr class="text-center">
                                                        <td>{{substr($drow['startdate'],0,3)}}/{{substr($drow['startdate'],3,2)}}/{{substr($drow['startdate'],5,2)}}</td>
                                                        <td>{{substr($drow['timestart'],0,2)}}時</td>
                                                        <td>{{$drow['croomclsfullname']}}</td>
                                                        <td>{{$drow['hday']}}天</td>
                                                        <td>{{$drow['fee']}}元</td>
                                                        <td><input class="form-control" name="ndiscounts[]" type="text" value="{{$drow['ndiscount']}}"></td>
                                                        <td><input class="form-control" name="hdiscounts[]" type="text" value="{{$drow['hdiscount']}}"></td>
                                                        <input type="hidden" name="ids[]" value="{{$drow['id']}}">
                                                        <input type="hidden" name="nfees[]" value="{{$drow['nfee']}}">
                                                        <input type="hidden" name="hfees[]" value="{{$drow['hfee']}}">
                                                    </tr>
                                                    <?php }?>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- <a href="{{route('webbook.email.get',$edu_loanplace[0]['email'])}}" target="_blank"><button class="btn btn-info" type="button" >email回覆</button></a> -->
                                <a href="mailto:{{$edu_loanplace[0]['email']}}"><button class="btn btn-info no-print" type="button" >email回覆</button></a>
                                <button class="btn btn-info no-print" type="button" onclick="printForm(document.getElementById('test'))">列印申請單</button>   
                                <!-- <button class="btn btn-info" type="button">列印回覆單</button>    -->
                            </fieldset>
                        </div>
                    </div>
                    <input type="hidden" name="delete" value="0" id="delete">
                    <div class="card-footer">
                    @if ($edu_loanplace[0]["status"] == 'T' || $edu_loanplace[0]["locked"] == '1')
                    @else 
                        <button type="button" class="btn btn-sm btn-info no-print" onclick="saveFun()"><i class="fa fa-save pr-2"></i>儲存</button>
                    @endif
                        <!-- <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button> -->
                         
                        <a href="{{route('webbook.Nantou.get')}}">
                            <button type="button" class="btn btn-sm btn-danger no-print"><i class="fa fa-reply"></i> 取消</button>
                        </a>
                    </div>
                </div>
            </div>
            
            {!! Form::close() !!}

        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    $("#sdate3").datepicker({
        format: "twy/mm/dd",
        language: 'zh-TW'
    });
    $('#datepicker5').click(function(){
        $("#sdate3").focus();
    });

    /*$( "#is_need_receipt" ).change(function() {
        $("#receiptname").attr("disabled",true);
        //var test=$("#receiptname").attr();
        //console.log(test);
        alert( "Handler for .change() called." );
    });*/
   

    var sdate3 = "<?= isset($edu_loanplace[0]['applydate'])? $edu_loanplace[0]['applydate'] :''?>";
    if(sdate3 != ''){
        var sdate3 = sdate3.substr(0,3)+'/'+sdate3.substr(3,2)+'/'+sdate3.substr(5,2);
    }
    
    $("#sdate3").val(sdate3);    
});
function batch_submit()
{
    $("#batch").val(1);
    $("#form").submit();
}
function deleteClass()
{
    var txt=confirm("是否要刪除?");
    if(txt==true){
        $("#delete").val(1);
        $("#form").submit();
    }
   
}

function saveFun()
{
    var locked = document.getElementById('locked').value;
    var status = document.getElementById('status').value;

    if(locked == '1' || status == 'T'){
        if (confirm("送出後，此申請單即不可再更改，是否確認送出?")){
            $("#form").submit();
        }
    } else {
        $("#form").submit();
    }
}

function confirmFee()
{
    var confirm_fee = document.getElementById('confirm_fee').value;
    var applyno = document.getElementById('applyno').value;

    $.ajax({
        url: "/admin/webbookplace/saveConfirmFee",
        data: {'confirm_fee': confirm_fee, 'applyno':applyno}
    }).done(function(response) {
        if (response > 0){
            alert( "金額已確認" );
        }else{
            alert( "確認失敗" );          
        }
    });   
}

function printForm(obj)
{
    var html = obj.innerHTML;
    var bodyHtml = document.body.innerHTML;
    var contentStyle = document.querySelector('.content');
    contentStyle.style = "margin-top : 0";
    var tmpselect = document.querySelector('.tmpselect');
    tmpselect.style ="width : 250px";

    // $(".printuse").css("margin-top","10%");
    
    // document.body.innerHTML = html;
    window.print();
    document.body.innerHTML = bodyHtml;
    window.location.reload();
}
</script>
@endsection