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

<?php $_menu = 'bookplace';?>

<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">批次場地修改</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin" class="text-info">首頁</a></li>
                    <li><a href="/admin/effectiveness_survey" class="text-info">批次場地修改</a></li>
                    <li class="active">批次場地修改</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <!-- form start -->
        @if ( isset($data) && !isset($create))
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/bookplace/batchVerify', 'id'=>'form']) !!}
        @else
        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/effectiveness_survey/', 'id'=>'form']) !!}
        @endif

        <div class="col-md-12 ">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">批次場地修改</h3>
                </div>
                <div class="card-body pt-4">
                    <!-- 院區 -->
                    <div class="form-group row">
                        <label class="col-sm-2 control-label text-md-right pt-2">院區</label>
                        <div class="col-sm-6">
                            <select class="select2 form-control select2-single input-max" name="branch" id="branch" onchange="getsitelist()">
                            @foreach(config('app.branch') as $key => $va)
                                <option value="{{ $key }}">{{ $va }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <!--上左搜尋欄-->
                        <div class="col-md-6 pt-4" style="border:groove;">
                            <!-- 日期 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                                <div class="col-sm-4">
                                    <div class="input-group roc-date input-max">
                                        <div class="input-group col-sm-10">
                                            <input type="text" id="left_sdate" name="left_sdate"
                                                class="form-control number-input-max" autocomplete="off"
                                                value="{{ old('fillsdate', (isset($data['fillsdate']))? $data['fillsdate'] : '') }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i
                                                    class="fa fa-calendar"></i></span>
                                            <!--<input type="text" id="left_sdate" name="left_sdate" class="form-control number-input-max" autocomplete="off"
                                                    value="108/05/17">-->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="input-group roc-date input-max">
                                        <div class="input-group col-sm-10">
                                            <input type="text" id="left_edate" name="left_edate"
                                                class="form-control number-input-max" autocomplete="off"
                                                value="{{ old('filledate', (isset($data['filledate']))? $data['filledate'] : '') }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i
                                                    class="fa fa-calendar"></i></span>
                                            <!--<input type="text" id="left_edate" name="left_edate" class="form-control number-input-max" autocomplete="off"
                                                    value="108/05/24">-->
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!--場地-->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                                <div class="col-sm-6" id="left_class_T">
                                    <select class="select2 form-control select2-single input-max" name="left_room_T" id="left_room_T">
                                        <option value="">請選擇</option>
                                        <?php foreach($placeT as $row){?>
                                        <option value="{{$row['site']}}">{{$row['site']}} {{$row['name']}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6" id="left_class_N" style="display: none">
                                    <select class="select2 form-control select2-single input-max" name="left_room_N" id="left_room_N">
                                        <option value="">請選擇</option>
                                        <?php foreach($placeN as $row){?>
                                        <option value="{{$row['site']}}">{{$row['site']}} {{$row['name']}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!--班別-->
                            <div class="form-group row">
                                <input type="hidden" name="type" value="">
                                <div class="col-sm-2" >
                                    <button type="button" class="btn btn-info" id="classbtn" style="display: inline" onclick="classmeet(2)">班別</button>
                                    <button type="button" class="btn btn-info" id="meetbtn" style="display: none" onclick="classmeet(1)">會議</button>
                                </div>
                                <div class="col-sm-6" id="classList" style="display: inline">
                                    <select id="class" name="class" class="select2 form-control select2-single input-max"  onchange="classChange()">
                                        <option value="">請選擇</option>
                                        @foreach($classList as $key => $va)
                                            <option value="{{ $va->class }}" {{ old('class', (isset($arr["class"]))? $arr["class"] : 1) == $va->class? 'selected' : '' }}>{{ $va->class }} {{ $va->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6" id="meetList"  style="display: none">    
                                    <select id="meet" name="meet" class="select2 form-control select2-single input-max"  onchange="classChange()">
                                        <option value="">請選擇</option>
                                        @foreach($meetList as $key => $va)
                                            <option value="{{ $va->class }}" >{{ $va->class }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- <div class="col-sm-4">
                                    <select id="class" name="class"
                                        class="select2 form-control select2-single input-max"
                                        onchange="classChange()">
                                        <option value="">請選擇</option>
                                        @foreach($classList as $key => $va)
                                        <option value="{{ $va->class }}"
                                            {{ old('class', (isset($arr["class"]))? $arr["class"] : 1) == $va->class? 'selected' : '' }}>
                                            {{ $va->class }} {{ $va->name }}</option>
                                        @endforeach
                                    </select>
                                </div> -->
                            </div>

                            <!--期別-->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                                <div class="col-sm-4">
                                    <!-- <select id="term" name="term" class="select2 form-control select2-single input-max"
                                        onchange="getCourse();"> -->
                                    <select id="term" name="term" class="select2 form-control select2-single input-max"
                                        >    
                                    </select>
                                </div>
                                <button type="button" class="btn btn-info" onclick="_ajax('left');">查詢</button>
                                <button type="button" class="btn btn-danger" onclick="clear_all();">清除條件</button>
                            </div>
                        </div>

                        <!--上右搜尋欄-->
                        <div class="col-md-6 pt-4" style="border:groove;">
                            <!-- 日期 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                                <div class="col-sm-4">
                                    <div class="input-group roc-date input-max">
                                        <div class="input-group col-sm-10">
                                            <input type="text" id="right_sdate" name="right_sdate"
                                                class="form-control number-input-max" autocomplete="off"
                                                value="{{ old('fillsdate', (isset($data['fillsdate']))? $data['fillsdate'] : '') }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i
                                                    class="fa fa-calendar"></i></span>
                                            
                                        </div>
                                    </div>
                                    <!--<input type="text" id="right_sdate" name="right_sdate" class="form-control number-input-max" autocomplete="off"
                                                    value="108/12/28">-->
                                </div>

                                <div class="col-sm-4">
                                    <div class="input-group roc-date input-max">
                                        <div class="input-group col-sm-10">
                                            <input type="text" id="right_edate" name="right_edate"
                                                class="form-control number-input-max" autocomplete="off"
                                                value="{{ old('filledate', (isset($data['filledate']))? $data['filledate'] : '') }}">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i
                                                    class="fa fa-calendar"></i></span>
                                            
                                        </div>
                                    </div>
                                    <!--<input type="text" id="right_edate" name="right_edate" class="form-control number-input-max" autocomplete="off"
                                                    value="108/12/28">-->
                                </div>
                            </div>
                            <!-- 院區 -->
                            <!-- <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">院區</label>
                                <div class="col-sm-6">
                                    <select class="select2 form-control select2-single input-max" name="right_branch" id="right_branch" onchange="getsitelist('R')">
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}">{{ $va }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div> -->
                            <!--場地-->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                                <div class="col-sm-6" id="right_class_T">
                                    <select class="select2 form-control select2-single input-max" name="right_room_T" id="right_room_T">
                                        <option value="">請選擇</option>
                                        <?php foreach($placeT as $row){?>
                                        <option value="{{$row['site']}}">{{$row['site']}} {{$row['name']}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-sm-6" id="right_class_N" style="display: none">
                                    <select class="select2 form-control select2-single input-max" name="right_room_N" id="right_room_N">
                                        <option value="">請選擇</option>
                                        <?php foreach($placeN as $row){?>
                                        <option value="{{$row['site']}}">{{$row['site']}} {{$row['name']}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!--查詢-->
                            <div class="form-group row">
                                <div class="col-sm-2">
                                </div>
                                <button type="button" class="btn btn-info" onclick="_ajax('right');">查詢</button>
                            </div>

                        </div>
                    </div>

                    <hr>
                    <input type="hidden" id="modal_send" name="modal_send">

                    <!-- 下方選項欄 -->
                    <div class="form-group row">
                        <div class="col-md-12" id="course_div123" style='display: flex;'>
                            @if(isset($data))
                            <!-- 未選取的課程  class="checkbox"-->
                            <div class="col-md-5">
                                <div class="halfArea" style="flex:1;height:300px;max-width:100%;overflow:auto;">
                                    <table style="width:100%;" id="it_uncheck">
                                        <tbody id="orgchk_uncheckList">
                                            <tr class="item_con orgchk_item item_uncheck">
                                                <th>日期</th>
                                                <th>場地</th>
                                                <th>時段</th>
                                                <th>班別</th>
                                                <th>期別</th>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!--箭頭-->
                            <div class="arrow_con col-md-2">
                                <button type="button" class="btn btn-light" id="arrow" ><=></button>
                            </div>
                            
                            <!-- 已選取的課程 class="checkbox checkbox-primary"-->
                            <div class="col-md-5">
                                <div class="halfArea" style="flex:1;height:300px;max-width:100%;overflow:auto;">
                                    <table style="width:100%;" id="it_check">
                                        <tbody id="test_course">
                                            <tr class="item_con  item_check">
                                                <th>日期</th>
                                                <th>場地</th>
                                                <th>時段</th>
                                                <th>班別</th>
                                                <th>期別</th>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit"class="btn btn-sm btn-info"><i
                            class="fa fa-save pr-2"></i>儲存</button>
                    <a href="/admin/bookplace/index">
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                    </a>

                </div>
            </div>
            <input type="hidden" id="final_info" name="final_info">
        </div>
        {!! Form::close() !!}
        <div id="set_time" class="modal inmodal fade" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
            data-keyboard="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <form method="post" id="send_modal">
                            <div class="row">
                                <!--上左搜尋欄-->
                                <div class="col-md-6 pt-4" style="border:groove;">
                                    <!-- 場地 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="left_site" id="left_site"disabled>
                                        </div>
                                    </div>

                                    <!--日期-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="left_date" id="left_date"disabled>
                                        </div>
                                    </div>

                                    <!--時段-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時段</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="left_time" id="left_time"disabled>
                                        </div>
                                    </div>

                                    <!--時間-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時間</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="col-xs-2" size="2" name="left_stime" id="left_stime" >-
                                            <input type="text" class="col-xs-2" size="2" name="left_etime" id="left_etime">
                                            <input type="hidden" id="left_stime_old" name="left_stime_old">
                                            <input type="hidden" id="left_etime_old" name="left_etime_old">
                                        </div>
                                    </div>
                                </div>

                                <!--上右搜尋欄-->
                                <div class="col-md-6 pt-4" style="border:groove;">
                                    <!-- 日期 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="right_site" id="right_site" disabled>
                                        </div>
                                    </div>
                                    <!--場地-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="right_date" id="right_date" disabled>
                                        </div>
                                    </div>

                                    <!--班別-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時段</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="right_time" id="right_time" disabled>
                                        </div>
                                    </div>

                                    <!--期別-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時間</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="col-xs-2" size="2" name="right_stime" id="right_stime">-
                                            <input type="text" class="col-xs-2" size="2" name="right_etime" id="right_etime">
                                            <input type="hidden" id="right_stime_old" name="right_stime_old">
                                            <input type="hidden" id="right_etime_old" name="right_etime_old">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" type="button" form="send_modal" onclick="save_modal();">儲存</button>
                        <button class="btn" type="button" class="close" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 圖片 -->
@include('admin/layouts/form/image')
@include('admin/layouts/list/del_modol')



@endsection

@section('js')
<script type=text/javascript>
    $(document).ready(function() { 
        $("#left_sdate").datepicker( {
            format: "twy/mm/dd" ,
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#left_sdate").focus();
        });
        $("#left_edate").datepicker({
            format: "twy/mm/dd" ,
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#left_edate").focus();
        });
        $("#right_sdate").datepicker({
            format: "twy/mm/dd" ,
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#right_sdate").focus();
        });
        $("#right_edate").datepicker({
            format: "twy/mm/dd" ,
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#right_edate").focus();
        });
        
        $("#arrow").click(function (){
            var left_time=document.getElementById("it_uncheck").rows[leftCon].cells[2].innerHTML;
            var right_time=document.getElementById("it_check").rows[rightCon].cells[2].innerHTML;
            var result=check_error();

            if(result){
                if(left_time=='D' || right_time=='D'){
                    var left_site=document.getElementById("it_uncheck").rows[leftCon].cells[1].innerHTML;
                    var right_site=document.getElementById("it_check").rows[rightCon].cells[1].innerHTML;
                    var left_date=document.getElementById("it_uncheck").rows[leftCon].cells[0].innerHTML;
                    var right_date=document.getElementById("it_check").rows[rightCon].cells[0].innerHTML;
                    var left_stime=$("#left_stime_hidden").val();
                    var right_stime=$("#right_stime_hidden").val();
                    var left_etime=$("#left_etime_hidden").val();
                    var right_etime=$("#right_etime_hidden").val();
                    if(left_time!='D'){
                        $("#left_stime").attr("disabled",true);
                        $("#left_etime").attr("disabled",true);
                    }
                    $("#left_stime_old").val(left_stime);
                    $("#left_etime_old").val(left_etime);
                

                    if(right_time!='D'){
                        $("#right_stime").attr("disabled",true);
                        $("#right_etime").attr("disabled",true);
                    }

                    $("#right_stime_old").val(right_stime);
                    $("#right_etime_old").val(right_etime);
                    

                    $("#left_date").val(left_date);
                    $("#left_site").val(left_site);
                    $("#right_site").val(right_site);
                    $("#right_date").val(right_date);
                    $("#left_time").val(left_time);
                    $("#right_time").val(right_time);
                    $("#right_stime").val(right_stime);
                    $("#right_etime").val(right_etime);
                    $("#left_etime").val(left_etime);
                    $("#left_stime").val(left_stime);
                    $('#set_time').modal('show');
                }else{
                    change_class();
                }
            }
        });

        classmeet(1);
    }); 
</script>
    
<script>
    // 點選項目
    let leftCon;
    let rightCon;
    var arr=[];
    var error_arr=['P05','P01','P04','P11','P12','P13','P14','P15','P16','P02','V01','V02'];
    var fuhua_arr=['101','103','201','202','203','204','205','C01','C02','C14'];
    function selectItem(e)
    {
        //leftCon = -1;
        //rightCon = -1;
        let classname = "orgchk_item";
        if($(e).hasClass("active")) {
            $(e).removeClass("active");

            if($(e).hasClass("item_uncheck")){
                leftCon = -1;
            }
            if($(e).hasClass("item_check")){
                rightCon = -1;
            }
            //rightCon = -1;
            //leftCon = -1;
            return;
        }
        else {
            
            if($(e).hasClass("item_uncheck")){
                $(".item_uncheck").removeClass("active");
                $(e).addClass('active');
                
            }
            if($(e).hasClass("item_check")){
                $(".item_check").removeClass("active");
                $(e).addClass('active');
                
            }  
        }

        if($(e).hasClass("item_uncheck")) {
            leftCon = $(e).index();
            //rightCon = -1;
            //console.log(leftCon);
        }
        else {
            rightCon = $(e).index();
            //leftCon = -1;
            //console.log(rightCon);
        }
    }
    function check_error()
    {
        let left_site=document.getElementById("it_uncheck").rows[leftCon].cells[0].innerHTML;
        let left_date=document.getElementById("it_uncheck").rows[leftCon].cells[1].innerHTML;
        let left_time=document.getElementById("it_uncheck").rows[leftCon].cells[2].innerHTML;
        let left_class=document.getElementById("it_uncheck").rows[leftCon].cells[3].innerHTML;
        let left_term=document.getElementById("it_uncheck").rows[leftCon].cells[4].innerHTML;
        let right_site=document.getElementById("it_check").rows[rightCon].cells[0].innerHTML;
        let right_date=document.getElementById("it_check").rows[rightCon].cells[1].innerHTML;
        let right_time=document.getElementById("it_check").rows[rightCon].cells[2].innerHTML;
        let right_class=document.getElementById("it_check").rows[rightCon].cells[3].innerHTML;
        let right_term=document.getElementById("it_check").rows[rightCon].cells[4].innerHTML;
        
        if(error_arr.indexOf(left_site)!=-1 || error_arr.indexOf(right_site)!=-1){
            alert("場地資料錯誤!");
            return false;
        }
        if(fuhua_arr.indexOf(left_site)!=-1 || fuhua_arr.indexOf(right_site)!=-1){
            alert("無法對福華場地做修改!");
            return false;
        }
        if(left_site==right_site && left_date==right_date && left_time==right_time && left_class==right_class && left_term==right_term){
            alert("為同一筆資料");
            return false;
        }

        if(left_class.indexOf("I")==0 || right_class.indexOf("I")==0){
            alert("無法對網路預約場地做編輯");
            return false;
        }

        
        return true;
    }
    function getsitelist(){
        var branch = $('#branch').val();
        if(branch=='1'){
            $('#left_class_T').show();
            $('#left_class_N').hide();
            $('#right_class_T').show();
            $('#right_class_N').hide();
        }else{
            $('#left_class_T').hide();
            $('#left_class_N').show();
            $('#right_class_T').hide();
            $('#right_class_N').show();
        }
    }
    // 切換類型
    function classmeet(type){
        if(type=='1'){
            $("#classbtn,#classList,#termlist,#termtitle").css('display','inline');
            $("#meetbtn,#meetList").css('display','none');

        }else{
            $("#meetbtn,#meetList").css('display','inline');
            $("#classbtn,#classList,#termlist,#termtitle").css('display','none');
        }
        $('input[name=type]').val(type);
    }
    function clear_all()
    {
        $("#left_sdate").val('');
        $("#left_room_N").val('');
        $("#left_room_T").val('');
        $("#left_edate").val('');
        $("#class").val('');
        $("#meet").val('');
        $("#left_term").val('');
    }
      
    
    function change_class(type)
    {
        //setTime(left_date,right_date,left_site,right_site,left_setime,right_setime,left_time,right_time);
        let left = $(".item_uncheck").eq(leftCon);
        let right = $(".item_check").eq(rightCon);
        
        var left_class_id=document.getElementById("it_uncheck").rows[leftCon].cells[3].innerHTML;
        var left_class_term=document.getElementById("it_uncheck").rows[leftCon].cells[4].innerHTML;
        var right_class_id=document.getElementById("it_check").rows[rightCon].cells[3].innerHTML;
        var right_class_term=document.getElementById("it_check").rows[rightCon].cells[4].innerHTML;

        document.getElementById("it_check").rows[rightCon].cells[3].innerHTML=left_class_id;
        document.getElementById("it_check").rows[rightCon].cells[4].innerHTML=left_class_term;
        document.getElementById("it_uncheck").rows[leftCon].cells[3].innerHTML=right_class_id;
        document.getElementById("it_uncheck").rows[leftCon].cells[4].innerHTML=right_class_term;

                
        var left_info=left.find('#test1').val();
        var right_info=right.find('#test2').val();
        if(type=='modal'){ 
            var info=$("#left_date").val()+'_'+$("#left_site").val()+'_'+$("#left_stime_old").val()+'_'+$("#left_etime_old").val()+'_'+$("#left_stime").val()+'_'+$("#left_etime").val()+'&'+$("#right_date").val()+'_'+$("#right_site").val()+'_'+$("#right_stime_old").val()+'_'+$("#right_etime_old").val()+'_'+$("#right_stime").val()+'_'+$("#right_etime").val();
        }else{
            //var info='left='+leftCon+' '+left_info+'&right='+rightCon+' '+right_info;
            var info=left_info+'&'+right_info;
        }
        
        
        arr.push(info);
        $("#final_info").val(arr);
        console.log(arr);
    }

    /*function setTime(left_date,right_date,left_site,right_site,left_setime,right_setime,left_time,right_time)
    {
        var iHeight=(window.screen.availHeight)*0.4;
        var iWidth=(window.screen.availWidth)*0.5;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2; 
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2; 
        window.open("/admin/bookplace/setTime?left_date="+left_date+"&right_date="+right_date+"&left_time="+left_time+"&right_time="+right_time+"&left_site="+left_site+
                    "&right_site="+right_site+"&left_setime="+left_setime+"&right_setime="+right_setime
                    ,"set_time", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
    }*/
    
    function _ajax(type){
        var branch = $('#branch').val();
        // if(type=='left'){
            var classmeet = $('input[name=type]').val();
            var left_sdate= $("#left_sdate").val();
            var left_edate= $("#left_edate").val();
            if(branch=='1'){
                var left_room=$("#left_room_T").val();
            }else{
                var left_room=$("#left_room_N").val();
            }
            if(classmeet=='1'){
                var class_id=$("#class").val();
            }else{
                var class_id=$("#meet").val();
            }
            var term= $("#term").val();
            if(class_id!='' && term=='') {
                alert('期別不可為空!');
                return false;
            }else if(class_id=='' && term) {
                alert('班別不可為空!');
                return false;
            }else if(class_id=='' && !term){
                if(left_sdate !='' && left_edate =='' )  {
                    alert('日期錯誤');
                    return false;
                }else if(left_sdate =='' && left_edate !=''){
                    alert('日期錯誤');
                    return false;
                } else if(left_sdate =='' && left_edate =='' && left_room ==''  )  {
                    alert('查詢資料不可為空!');
                    return false;
                } 
            }else if(left_sdate !='' && left_edate =='' )  {
                alert('日期錯誤!');
                return false;
            }else if(left_sdate =='' && left_edate !=''){
                alert('日期錯誤!');
                return false;
            }
        // }else{
            var right_sdate=$("#right_sdate").val();
            var right_edate=$("#right_edate").val();
            if(branch=='1'){
                var room=$("#right_room_T").val();
            }else{
                var room=$("#right_room_N").val();
            }
            if(right_sdate =='' && right_edate =='' && room ==''  )  {
                alert('查詢資料不可為空!');
                return false;
            } else if(right_sdate !='' && right_edate =='' )  {
                alert('日期錯誤');
                return false;
            }else if(right_sdate =='' && right_edate !=''){
                alert('日期錯誤');
                return false;
            }
        // }
        
        arr.length=0;
        $("#final_info").val(arr);
        var tablehead ="<tr class='item_con orgchk_item item_uncheck'><th>日期</th><th>場地</th><th>時段</th><th>班別</th><th>期別</th></tr>";
        var tableBody ="<tr><td colspan=5>Loadind...</td</tr>";
        // if(type=='left'){
            $("#orgchk_uncheckList").html(tablehead+tableBody);
            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: "POST", //傳送方式
                url: "/admin/bookplace/getT22tbAjax", //傳送目的地
                data:{ //傳送資料
                    branch: branch,
                    final_sdate: left_sdate, 
                    final_edate: left_edate,
                    room: left_room,
                    class_id: class_id,
                    term: term
                },
                dataType: "json", //資料格式
                success: function(data) {
                    console.log(data);
                    var tableBody='';
                    tableBody+="<tr class='item_con orgchk_item item_uncheck'><th>日期</th><th>場地</th><th>時段</th><th>班別</th><th>期別</th></tr>";
                    for(var i=0;i<data.length;i++){
                        tableBody+='<tr class="item_con orgchk_item item_uncheck" onclick="selectItem(this)" value="test">';
                        tableBody+="<td>"+data[i].date+"</td>";
                        tableBody+="<td>"+data[i].site+"</td>";
                        tableBody+="<td>"+data[i].time+"</td>";
                        tableBody+="<td>"+data[i].class+"</td>";
                        tableBody+="<td>"+data[i].term+"</td>";
                        tableBody+="<td><input hidden id='test1' type='checkbox' value='"+data[i].date+'_'+data[i].site+'_'+data[i].stime+'_'+data[i].etime+"'checked></td>";
                        tableBody+="<td><input hidden id='left_stime_hidden' value='"+data[i].stime+"'></td>";
                        tableBody+="<td><input hidden id='left_etime_hidden' value='"+data[i].etime+"'></td>";
                        tableBody+='</tr>';
                        
                    }
                    if(data.length=='0'){
                        tableBody+='<tr><td colspan=5>查無資料</td</tr>';
                    }
                    $("#orgchk_uncheckList").html(tableBody);
                },
                error: function(data) {
                    var tableBody ="<tr><td colspan=5>系統錯誤</td</tr>";
                    $("#orgchk_uncheckList").html(tablehead+tableBody);
                    console.log('error');
                }
                    
            });
        // }

        // if(type=='right'){
            $("#test_course").html(tablehead+tableBody);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST", //傳送方式
                url: "/admin/bookplace/getT22tbAjax", //傳送目的地
                data:{ //傳送資料
                    branch: branch,
                    final_sdate: right_sdate, 
                    final_edate: right_edate,
                    room: room,
                },
                dataType: "json", //資料格式
                success: function(data) {
                    console.log(data);
                    var tableBody='';
                    tableBody+="<tr class='item_con item_check'><th>日期</th><th>場地</th><th>時段</th><th>班別</th><th>期別</th></tr>";
                    for(var i=0;i<data.length;i++){
                        tableBody+='<tr class="item_con orgchk_item item_check" onclick="selectItem(this)" value="test">';
                        tableBody+="<td>"+data[i].date+"</td>";
                        tableBody+="<td>"+data[i].site+"</td>";
                        tableBody+="<td>"+data[i].time+"</td>";
                        tableBody+="<td>"+data[i].class+"</td>";
                        tableBody+="<td>"+data[i].term+"</td>";
                        tableBody+="<td><input hidden id='test2' type='checkbox' value='"+data[i].date+'_'+data[i].site+'_'+data[i].stime+'_'+data[i].etime+"'checked></td>";
                        tableBody+="<td><input hidden id='right_stime_hidden' value='"+data[i].stime+"'></td>";
                        tableBody+="<td><input hidden id='right_etime_hidden' value='"+data[i].etime+"'></td>";
                        tableBody+='</tr>';
                        document.getElementById("test_course").innerHTML=tableBody;
                    }
                    if(data.length=='0'){
                        tableBody+='<tr><td colspan=5>查無資料</td</tr>';
                    }
                },
                error: function(data) {
                    var tableBody ="<tr><td colspan=5>系統錯誤</td</tr>";
                    $("#test_course").html(tablehead+tableBody);
                    console.log('error');
                }
                    
            });
        // }
        
    }

    function save_modal()
    {
        $("#set_time").modal("hide");
        $("#modal_send").val();
        var type="modal";
        change_class(type);
    }

    function confirmOrgchk()
    {
        let classarray = '';
        let test='';
        for(let i=1; i<$(".orgchk_item.item_check").length; i++) {
            classarray += $(".orgchk_item.item_check").eq(i).find('td').html()+',';
            test +=$("input:checkbox[name=course]:checked").val();
        }
        //alert(test);
        //alert(classarray);
        $("input[name=test_course]").val(classarray);
    }
    // 取得期別
    function classChange()
    {   
        if($('#classmeet').val()=='1'){
            var classes = $('#class').val();
        }else{
            var classes = $('#meet').val();
        }
        
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/training_survey/getterm',
            data: { classes: classes, selected: ''},
            success: function(data){
                $('#term').html(data);
                $("#term").trigger("change");
            },
            error: function() {
                alert('Ajax Error');
            }
        });
    }
    // 取得課程
    function getCourse()
    {
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/training_process/getcourse',
            data: { classes: $('#class').val(), term: $('#term').val()},
            success: function(data){
                $('#course_div').html(data);
            },
            error: function() {
                alert('Ajax Error');
            }
        });
    }

</script>


@endsection