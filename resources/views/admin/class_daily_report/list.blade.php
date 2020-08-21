@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'class_daily_report';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班期日報表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">班期日報表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>班期日報表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form id="exportFile" method="post" id="search_form" action="/admin/class_daily_report/export" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <?php $weekpicker =''; ?>
                                        <div class="form-group row align-items-center">
                                            <label class="col-sm-1">日期<span class="text-danger"></span></label>
                                            <input type="text" class="form-control input-max width" value="{{$weekpicker}}" id="weekpicker" min="1" autocomplete="off" placeholder="請選擇要查詢的星期" name="weekpicker" readonly require>
                                        </div>
                                        <div class="form-group row align-items-center">
                                            <input class="col-sm-1 pt-2 float-right" type="radio" id="teipei"" name="area"" value="1" checked>
                                            <label class="col-sm-1">臺北院區<span class="text-danger"></span></label>    
                                            <input class="col-sm-1 pt-2 float-right" type="radio" id="nantou" name="area" value="2" >
                                            <label class="col-sm-1">南投院區<span class="text-danger"></span></label>        
                                        </div>
                                        <div id="dvdoctype" class="form-group row  align-items-center">
                                            <label class="col-2 text-right">請選檔案格式：</label>
                                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                        </div>

                                        <div class="form-group row col-4 justify-content-center align-items-center">
                                            <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                            <label id="download" visible="false"></label>    
                                        </div>
                                    </form>
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

</script>
@endsection