@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'hourlyfee_notice';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">鐘點費入帳通知書</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">鐘點費入帳通知書</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>鐘點費入帳通知書</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <form id="exportFile" method="post" id="search_form" action="/admin/hourlyfee_notice/export" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label text-md-right pt-2">輸入列印條件</label>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                                        <div class="col-6">
                                                            <div class="input-group bootstrap-touchspin number_box">
                                                                <select id="classes" name="classes" class="select2 form-control select2-single input-max" onchange="getTerms();">
                                                                    <option value="">請選擇</option>
                                                                <?php foreach ($classArr as $key => $va) { ?>
                                                                    <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-2 control-label text-md-right pt-2">期別</label>
                                                        <div class="col-3">
                                                            <div class="input-group bootstrap-touchspin number_box">
                                                                <select id="term" name="term" class="select2 form-control select2-single input-max" required>
                                                                <?php foreach ($termArr as $key => $va) { ?>
                                                                    <option value='<?=$va->term?>'><?=$va->term?></option>
                                                                <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                                        <label class="col-2 text-right">請選檔案格式：</label>
                                                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                                    </div>
                                                    <?php $weekpicker =''; ?>
                                                    <div class="form-group row align-items-center">
                                                        <label class="col-2  text-md-right pt-2" style="float: left ;margin-left: -5px">日期<span class="text-danger"></span></label>
                                                        <input type="text" class="form-control col-3" value="{{$weekpicker}}" id="weekpicker" min="1" autocomplete="off" placeholder="請選擇要查詢的星期" name="weekpicker" readonly require>
                                                       
                                                            <button type="submit" class="btn mobile-100" style="margin-left: 20px"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                          
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">入帳通知</label>
                                            </div>
                                            <!-- <div class="form-group row ">
                                                <label class="col-2  text-md-right pt-2">寄件者電子信箱：</label>
                                                <input type="text" class="form-control col-2" id="name" name="name" min="1" value="" input-max autocomplete="off" >
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2  text-md-right pt-2">信件發送主機：</label>
                                                <input type="text" class="form-control col-2" id="name" name="name" min="1" value="" input-max autocomplete="off" >
                     
                                            </div> 
                                            <div class="form-group row ">
                                                <label class="col-2  text-md-right pt-2">SNS IP：</label>
                                                <input type="text" class="form-control col-2" id="name" name="name" min="1" value="" input-max autocomplete="off" >
                                                <label class="ml-2">SNS Port：</label>
                                                <input type="text" class="form-control col-2" id="name" name="name" min="1" value="" input-max autocomplete="off" >
                                            
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-2  text-md-right pt-2">SNS帳號：</label>
                                                <input type="text" class="form-control col-2" id="name" name="name" min="1" value="" input-max autocomplete="off" >
                                                <label class="ml-2">SNS密碼：</label>
                                                <input type="password" class="form-control col-2" id="name" name="name" min="1" value="" input-max autocomplete="off" >
                                            </div> -->
                                            <?php $datetw ='';$tdate=$tdateArr[0]->date;?>
                                            <div class="form-group row">
                                                <label class="col-2  text-md-right pt-2">轉存日期:</label>
                                                <div class="col-3">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="tdate" name="tdate" class="select2 form-control select2-single input-max" onchange="tdatechange();" required>
                                                        <?php foreach ($tdateArr as $key => $va) { ?>
                                                            <option value='<?=$va->date?>'><?=$va->sdate?></option>
                                                        <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <input type="text" class="form-control col-3 ml-2" value="{{$datetw}}" id="datetw" name="datetw"  id="datetw" min="1" autocomplete="off">
                                                 -->
                                                <a id="mlink" href="/admin/hourlyfee_notice/{{$tdate}}/send">
                                                <button type="button" class="btn btn-primary btn-sm ml-3"><i class="fas fa-file-export fa-lg pr-1"></i>發送</button>
                                                </a>   
                                                
                                                <!-- <label class="col-1 ml-2" >
                                                    <input type="checkbox" id="checkemail" value="1"> Email
                                                </label>
                                                <label class="col-1" >
                                                    <input type="checkbox" id="checksns" value="1"> 簡訊
                                                </label>         -->
                                            </div>
                                            <!-- <div class="form-group row  justify-content-center">
                                                <label >訊息：</label>
                                                <textarea  id="textarea" class="form-control col-9" rows="5"></textarea>
                                            </div> -->
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script src="/backend/assets/js/moment.min.js"></script>
<script>

    $(document).ready(function(){
        moment.locale('zh-tw', {
        week: { dow: 1 } // Monday is the first day of the week
        });

    //Initialize the datePicker(I have taken format as mm-dd-yyyy, you can     //have your owh)
    $( function() {
            $('#weekpicker').datepicker({
                format: "yyyy/mm/dd",
            });
        } );
    //Get the value of Start and End of Week
    $('#weekpicker').change(function () {
        var value = $("#weekpicker").val();
        //var firstDate = moment(value, "YYYY/MM/DD").day(1).format("YYYY/MM/DD");
        var firstDate = moment(value, "YYYY/MM/DD").day(0).format("YYYY/MM/DD");
        //var lastDate =  moment(value, "YYYY/MM/DD").day(7).format("YYYY/MM/DD");
        var lastDate =  moment(value, "YYYY/MM/DD").day(6).format("YYYY/MM/DD");
        var ftemp = firstDate.split("/");
        var ltemp = lastDate.split("/");

        ftemp[0]=pad(ftemp[0]-1911,3);
        ltemp[0]=pad(ltemp[0]-1911,3);

        $("#weekpicker").val(ftemp[0] + "/" + ftemp[1]+ "/" + ftemp[2]+ " ~ " + ltemp[0] + "/" + ltemp[1]+ "/" + ltemp[2]); 
    });
    }); 

    function pad(num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }   

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/lecture_signature/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#term").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};

    function tdatechange() {
            $("#mlink").attr("href","/admin/hourlyfee_notice/"+$("#tdate").val()+"/send");
    }
</script>
@endsection