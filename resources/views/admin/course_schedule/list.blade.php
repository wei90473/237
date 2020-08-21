@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'course_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">課程表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">課程表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form id="exportFile" method="post" id="search_form" action="/admin/course_schedule/export" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>課程表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"></i>兩區條件請選其一 (每次僅會套用一區的條件)</h4>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            
                                            <div class="form-group row align-items-center">
                                                <input class="col-sm-1 pt-2 float-right" type="radio" id="card1" name="cardselect" value="1" checked>
                                                <label class="col-sm-2">依班期<span class="text-danger"></span></label>    
                                            </div>

                                                <!-- 班別 -->
                                                <div class="form-group row">
                                                    <label class="col-sm-1">班別</label>
                                                    <div class="col-sm-10">
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
                                                <!-- 期別 -->                        
                                                <div class="form-group row">
                                                    <label class="col-sm-1">期別</label>
                                                    <div class="col-sm-2">
                                                        <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="terms" name="terms" class="select2 form-control select2-single input-max" >
                                                            <?php foreach ($termArr as $key => $va) { ?>
                                                                <option value='<?=$va->term?>'><?=$va->term?></option>
                                                            <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row align-items-center">
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="single" name="weektype"" value="1" checked>
                                                    <label class="col-sm-1">單週<span class="text-danger"></span></label>    
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="duo" name="weektype" value="2" >
                                                    <label class="col-sm-1">雙週<span class="text-danger"></span></label>       
                                                </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                    <div class="card-header">
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group row">
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="card2" name="cardselect" value="2"">
                                                    <label class="col-sm-2">依整週<span class="text-danger"></span></label>    
                                                </div>
                                                <div class="form-group row align-items-center">
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="teipei"" name="area"" value="1" checked>
                                                    <label class="col-sm-1">台北院區<span class="text-danger"></span></label>    
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="nantou" name="area" value="2" >
                                                    <label class="col-sm-1">南投院區<span class="text-danger"></span></label>
                                                    <input class="col-sm-1 pt-2 float-right" type="radio" id="allarea" name="area" value="3" >
                                                    <label class="col-sm-1">全部<span class="text-danger"></span></label>           
                                                </div>
                                                
                                      
                                                <?php $weekpicker =''; ?>
                                                <div class="form-group row align-items-center" id="datepicker">
                                                    <label class="col-md-1">日期<span class="text-danger"></span></label>
                                                    <input type="text" class="form-control  col-md-6 input-max width" value="{{$weekpicker}}" id="weekpicker" placeholder="請選擇要查詢的星期" name="weekpicker"  min="1" autocomplete="off" readonly >
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            </div>

                        </div>
                    </div>
                    <div id="dvdoctype" class="form-group row  align-items-center">
                        <label class="col-2 text-right">請選檔案格式：</label>
                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                    </div>
                    <div align="center">
                                <div>
                                    <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                    <label id="download" visible="false"></label>
                                </div>
                            </div>
                    </form>
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
        console.log(ftemp[2]);
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
//get terms by class
function getTerms()	{
    $.ajax({
        type: 'post',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        dataType: "html",
        url:"/admin/course_schedule/getTerms",
        data: { classes: $('#classes').val()},
        success: function(data){
        let dataArr = JSON.parse(data);
        let tempHTML = "";
        for(let i=0; i<dataArr.length; i++) 
        {
            tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
        }
        $("#terms").html(tempHTML);
        },
        error: function() {
            console.log('Ajax Error');
        }
    });
};


</script>
@endsection