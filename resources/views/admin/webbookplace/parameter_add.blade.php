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
                        <!--webbook.arg.add.putid-->
                        @if ($mode=='put')
                            {!! Form::open([ 'method'=>'put', 'route'=>array('webbook.parameter.put',$data->id), 'id'=>'form']) !!}
                         @else
                            {!! Form::open([ 'method'=>'post', 'route'=>array('webbook.parameter.post'), 'id'=>'form']) !!}
                        @endif

                        <div class="card-body">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">收件人姓名:</label>
                                <div class="col-md-2">
                                <input type="text" class="form-control" id="name" name="name" value="{{isset($data->name)? $data->name:''}}" >
                                </div >
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">收件人單位:</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="organize" value="{{isset($data->organize)? $data->organize:''}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">EMAIL:</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="email" value="{{isset($data->email)? $data->email:''}}">
                                </div>
                            </div>
                            <input type="hidden" id="delete" name="delete" value="0">
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-info" type="submit">儲存</button>
                            <?php if($mode=='put') {?>
                            <button class="btn btn-danger" type="button" onclick="form_delete();">刪除</button>
                            <?php }else{ ?>
                            <a href="{{route('webbook.parameter.get')}}"><button class="btn btn-danger" type="button">返回</button></a>
                            <?php }?>
                        </div>
                        {!! Form::close() !!}
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
    
    function form_delete()
    {
        var txt=confirm("確定要刪除?");
        if(txt){
            $("#delete").val(1);
            $("#form").submit();
        }
        
    }

</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

