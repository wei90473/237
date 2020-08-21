@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

<style>
        /* .item_con {
            display: flex;
            align-items: center;
        } */
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
    <?php $_menu = 'webbook';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路預約場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes" class="text-info">網路預約場地審核處理</a></li>
                        <li class="active">網路預約場地審核處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'post', 'route'=>array("webbook.change.email.get"), 'id'=>'form']) !!}
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">發送異動通知</h3></div>
                        <div class="card-body pt-4">
                            <table class="table table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center"><input type="checkbox" onclick="checkAll(this);"></th>
                                        <th class="text-center">收件人姓名</th>
                                        <th class="text-center">收件人單位</th>
                                        <th class="text-center">EMAIL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data as $row){ ?>
                                        <tr class="text-center">
                                            <td><input type="checkbox" name="checkboxs[]" value="{{$row['id']}}"></td>
                                            <td><a href="{{route('webbook.parameter.add',$row['id'])}}">{{$row['name']}}</a></td>
                                            <td>{{$row['organize']}}</td>
                                            <td>{{$row['email']}}</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-info">寄送</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="backFun()">返回</button></a>
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
<script>
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
    
    var sdate3 = "<?= isset($data[0]['startdate'])? $data[0]['startdate'] :''?>";
    if(sdate3 != ''){
        var sdate3 = sdate3.substr(0,3)+'/'+sdate3.substr(3,2)+'/'+sdate3.substr(5,2);
    }
    var edate3 = "<?= isset($data[0]['enddate'])? $data[0]['enddate'] :''?>";
    if(edate3 != ''){
        var edate3 = edate3.substr(0,3)+'/'+edate3.substr(3,2)+'/'+edate3.substr(5,2);
    }
    $("#sdate3").val(sdate3);
    $("#edate3").val(edate3);
    
});
function deleteClass()
{
    var txt=confirm("是否要刪除?");
    if(txt==true){
        $("#delete").val(1);
        $("#form").submit();
    }
    
}

function checkAll(source) {
    checkboxes = document.getElementsByName('checkboxs[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}

function backFun()
{
    history.go(-1);
}
</script>
@endsection