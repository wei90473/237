@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_money_all';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座費用請領總表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座費用請領總表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座費用請領總表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/lecture_money_all/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div id="divarea" style="visibility:visible" class="form-group row justify-content-center align-items-center">
                                            <label class="radio-inline col-2"><input type="radio" name="area" id="taipei" value="1" checked >台北院區  </label>
                                            <label class="radio-inline col-2"><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                    </div>
                                        <?php $weekpicker =''; ?>
                                        <div class="form-group row justify-content-center align-items-center" id="datepicker">
                                            <label class="col-md-1">日期<span class="text-danger"></span></label>
                                            <input type="text" class="form-control  col-md-6 input-max width" value="{{$weekpicker}}" id="weekpicker" placeholder="請選擇要查詢的星期" readonly name="weekpicker"  min="1" autocomplete="off" required>
                                        </div>

                                        <div class="form-group row justify-content-center align-items-center">
                                            <label class="radio-inline col-2"><input type="radio" name="type" id="normal" value="1" checked >一般班  </label>
                                            <label class="radio-inline col-2"><input type="radio" name="type" id="collect" value="2">代收款班</label>
                                            <label class="radio-inline col-2"><input type="radio" name="type" id="alltype" value="3">全部</label>
                                        </div>
                                        <div id="dvdoctype" class="form-group row  align-items-center">
                                            <label class="col-4 text-right">請選檔案格式：</label>
                                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                        </div>
                                        <div class="form-group row justify-content-center">
                                        <div class="col-4">
                                            <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                            <label id="download"></label>
                                        </div>
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
     if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }
    
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



</script>
@endsection