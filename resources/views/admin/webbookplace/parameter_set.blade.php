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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>參數設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="float:left; margin-bottom:5px;">
                                    {{ Form::open(["id" => "search_form", "method" => "put", "url" => "admin/webbookplace/updateApplyLimit"]) }}
                                        <div class="input-group-prepend">
                                            <label class="input-group-text">場地申請日期限制</label>
                                            <input type="text" class="form-control" name="spaceappday" value="{{ $spaceappday }}" style="width: 100px;height: 43px"></input>
                                            <button class="btn btn-info" type="submit" style="margin-left: 5px;font-size: 16px">儲存</button>
                                        </div>
                                    {{ Form::close() }} 
                                    </div>
                                    <div style="float:right; margin-bottom:5px;">
                                        <a href="{{route('webbook.parameter.add')}}"><button class="btn btn-info" type="button" id="set_seat_button">新增</button></a>
                                        <!--<a href="/admin/webbookplace"><button class="btn btn-danger" type="button" id="set_seat_button">返回</button></a>-->
                                        <button class="btn btn-danger" type="button" onclick="window.close();" id="set_seat_button">離開</button>
                                    </div>
                                    </div>
                                    <table class="table table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">收件人姓名</th>
                                                <th class="text-center">收件人單位</th>
                                                <th class="text-center">EMAIL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($data as $row){ ?>
                                                <tr class="text-center">
                                                    <td><a href="{{route('webbook.parameter.add',$row['id'])}}">{{$row['name']}}</a></td>
                                                    <td>{{$row['organize']}}</td>
                                                    <td>{{$row['email']}}</td>
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
    
 

</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

