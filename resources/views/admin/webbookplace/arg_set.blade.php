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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>常用折扣設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="float:right; margin-bottom:5px;">
                                        <a href="{{route('webbook.arg.add.get')}}"><button class="btn btn-info" type="button" id="set_seat_button">新增</button></a>
                                        <button class="btn btn-danger" onclick="window.close();" type="button" id="set_seat_button">離開</button>
                                    </div>
                                    <table class="table table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">折扣說明</th>
                                                <th class="text-center">折扣數</th>
                                                <th class="text-center">折扣類別</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ ?>
                                                <tr class="text-center">
                                                    <td><a href="{{route('webbook.arg.add.getid',$row['id'])}}">{{$row['name']}}</a></td>
                                                    <td>{{$row['param1']}}</td>
                                                    <?php 
                                                        if($row['param2']==0){
                                                            $param2='比例';
                                                        }else{
                                                            $param2='數值';
                                                        }
                                                    ?>
                                                    <td>{{$param2}}</td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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

        var curr = new Date(); // get current date
        var first = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
        var last = first + 6; // last day is the first day + 6
        var year = curr.getFullYear()-1911;
        var month = curr.getMonth()+1;
        if(month<10){
            month='0'+month;
        }
        if(first<10){
            first='0'+first;
        }
        if(last<10){
            last='0'+last;
        }
        var firstday = year+'/'+month+'/'+first;
        var lastday = year+'/'+month+'/'+last;

        $("#sdate3").val(firstday);
        $("#edate3").val(lastday);
        
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

