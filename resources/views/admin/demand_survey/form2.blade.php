@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_survey';?>

    <style>
        .backgroundWhite {
            background-color: #ffffff !important;
            resize: none;
            border-top: 0px solid; 
            border-right: 0px solid; 
            border-bottom: 0px solid; 
            border-left: 0px solid;
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">填報狀況查詢</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_survey" class="text-info">需求調查列表</a></li>
                        <li class="active">填報狀況查詢</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">填報資料</h3></div>
                    <div class="card-body pt-4">
                        <!-- 本次需求基本資料 -->
                        <fieldset style="border:groove">
                        <div class="card-body pt-4 text-center">
                            <label class="col-sm-12 control-label text-md-left">年度：<span>{{$iddata[0]->yerly}}</span></label>
                            <label class="col-sm-12 control-label text-md-left">第幾次調查：<span>{{$iddata[0]->times}}</span></label>
                            <label class="col-sm-12 control-label text-md-left">院區：<span>{{ config('app.branch.'.$iddata[0]->branch) }}</span></label>
                            <label class="col-sm-12 control-label text-md-left">需求調查名稱：<span>{{$iddata[0]->purpose}}</span></label>
                        </div>
                        </fieldset>
                        <!-- 列表 -->
                        <div class="card-body mx-auto">
                            <!-- <input type="hidden" id="enrollorg" name="enrollorg" value=""> -->
                            <table>
                                <tr>
                                    <td class="text-center"><Button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModall">匯入填報資料</Button></td>
                                    <td class="text-center"><Button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal2">列印機關建議班別</Button></td>
                                    <td class="text-center"><Button type="button" class="btn btn-primary" onClick="canceldata()">取消凍結</Button></td>
                                    <td class="text-center"><Button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal4">需求名冊</Button></td>
                                    <td class="text-center"><Button type="button" class="btn btn-primary" onClick="resetdata()">重設填報方式</Button></td>
                                    <td class="text-center"><Button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal6" >班別需求整併</Button></td>
                                </tr>
                            </table>
                        </div>
                        <!-- 內容 -->
                        
                        <div style="width:100%">
                            <!--左側-->
                            <div style="width:30%;float:left;">
                                <div class="card-body pt-4 text-left" style="height:400px;overflow:auto">
                                @if(isset($data)) 
                                    <ul id="treeview" class="filetree">
                                        <li><span class="folder classType_item">行政機關</span>
                                            <ul>
                                            @foreach($data['Administration'] as $va)
                                                @if ( !isset($va->downorgan))
                                                <!-- if(count($va->downorgan)==0) -->
                                                <li><span class="folder classType_item" onclick="chooseType('1','{{$va->enrollorg}}','{{ $va->enrollname }}')">{{ $va->enrollname }}{{ $va->report }}</span></li>
                                                @else
                                                <li><span class="folder classType_item" onclick="chooseType('1','{{$va->enrollorg}}','{{ $va->enrollname }}')">{{ $va->enrollname }}{{ $va->report }}</span>
                                                    <ul>
                                                    @foreach($va->downorgan as $value)
                                                        <li><span class="folder classType_item" onclick="chooseType('1','{{$va->enrollorg}}','{{ $value->enrollname }}','{{ $va->enrollname }}')">{{ $value->enrollname }}{{ $value->report }}</span></li>
                                                    @endforeach
                                                    </ul>
                                                </li>
                                                @endif
                                            @endforeach
                                            </ul>
                                        </li>
                                        <li><span class="folder classType_item">訓練機構</span>
                                            <ul>
                                            @foreach($data['TrainingInstitution'] as $va)
                                                <li><span class="folder classType_item" onclick="chooseType('2','{{$va->enrollorg}}','{{ $va->enrollname }}')" >{{ $va->enrollname }}{{ $va->report }}</span></li>
                                            @endforeach
                                            </ul>
                                        </li>
                                    </ul>
                                 @endif    
                                </div>
                                <!-- 左下2 -->
                                <div class="card-body pt-4 text-left" style="height:300px;overflow:auto">
                                    <fieldset>
                                        <div class="card-body pt-4"  id="DemandSurveyTitle">
                                            <!-- <label class="col-sm-12 control-label text-md-left">年度：<span>{{$iddata[0]->yerly}}</span></label>
                                            <label class="col-sm-12 control-label text-md-left">第幾次調查：<span>{{$iddata[0]->times}}</span></label>
                                            <label class="col-sm-12 control-label text-md-left">院區：<span>{{ config('app.branch.'.$iddata[0]->branch) }}</span></label>
                                            <label class="col-sm-12 control-label text-md-left">需求調查名稱：<span>{{$iddata[0]->purpose}}</span></label> -->
                                        </div>
                                    </fieldset>    
                                </div>
                            </div>
                            <!--右側-->
                            <div style="width:70%;float:left;">
                                <div class="card-body pt-4 text-left" style="height:700px;overflow:auto">
                                    <table class="table table-bordered mb-0" style="float:left;">
                                        <thead>
                                            <tr>
                                                <th class="text-left">班號</th>
                                                <th class="text-left">班別名稱</th>
                                                <th class="text-left">核定</th>
                                                <th class="text-left">彙總</th>
                                            </tr>
                                        </thead>
                                        <tbody id="DemandSurveyData">
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Need foreach decide how many rows and columns -->
                       <!--  <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="float:left;">

                                <thead>
                                    <tr>
                                        <th class="text-center">班號</th>
                                        <th class="text-center">班別名稱</th>
                                        <th class="text-center">核定</th>
                                        <th class="text-center">彙總</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td class="text-center">108515</td>
                                        <td class="text-center">基層主管班</td>
                                        <td class="text-center">6</td>
                                        <td class="text-center">6</td>
                                </tbody>

                            </table>
                        </div> -->
                        
                    </div>
                    
                    

                    <!-- Modal1 匯入填報資料 -->
                    <form method="POST" action="/admin/demand_survey/importdata" enctype="multipart/form-data" id="form1" name="form1">
                        {{ csrf_field() }}
                        <div class="modal fade" id="exampleModall" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">匯入填報資料</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label>年度</label>
                                        <select class="browser-default custom-select" name="importyerly" id="importyerly">
                                        @foreach($iddata['choices'] as $key => $va)
                                                <option value="{{ $key }}" {{ $iddata[0]->yerly == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                        </select>
                                        <label for="importtimes">第幾次調查</label>
                                        <select class="browser-default custom-select" name="importtimes" id="importtimes">
                                        @for($i=1;$i<9;$i++)
                                                <option value="{{ '0'.$i }}" {{ $iddata[0]->times == '0'.$i? 'selected' : '' }}>{{ '0'.$i }}</option>
                                        @endfor
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form1');">確定</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Modal2 列印機關建議班別 -->
                    <form method="POST" action="/admin/demand_survey/printdata" enctype="multipart/form-data" id="form2" name="form2">
                        {{ csrf_field() }}
                        <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                   <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">列印機關建議班別</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label for="printyerly">年度</label>
                                        <select class="browser-default custom-select" name="printyerly" id="printyerly">
                                        @foreach($iddata['choices'] as $key => $va)
                                                <option value="{{ $key }}" {{ $iddata[0]->yerly == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                        </select>

                                        <label for="printtimes">第幾次調查</label>
                                        <select class="browser-default custom-select" name="printtimes" id="printtimes">
                                        @for($i=1;$i<9;$i++)
                                                <option value="{{ '0'.$i }}" {{ $iddata[0]->times == '0'.$i? 'selected' : '' }}>{{ '0'.$i }}</option>
                                        @endfor
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form2');">確定</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Modal3 取消凍結 -->
                    <form method="POST" action="/admin/demand_survey/canceldata" enctype="multipart/form-data" id="form3" name="form3">
                        {{ csrf_field() }}
                        <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">取消凍結</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <input type=hidden id="organyerly" name="organyerly" value="{{$iddata[0]->yerly}}" >
                                        <input type=hidden id="organtimes" name="organtimes" value="{{$iddata[0]->times}}" >
                                        <label for="organizationCode">機關代碼：</label>
                                        <input type="text" id="organizationCode" name="organizationCode" value="" readonly>
                                        <br/>
                                        <label for="organizationName">機關名稱：</label>
                                        <input type="text" id="organizationName" name="organizationName" value="" readonly>
                                        <br/>
                                        <p>是否【取消凍結】</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form3');">是</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">否</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Modal4 需求名冊 -->
                    <div class="modal fade" id="exampleModal4" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">需求名冊</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="card-header"><h3 class="card-title">輸入查詢條件</h3></div>
                                    <div class="card-body pt-4 text-center">
                                        <label for="class">班別：</label>
                                        <select class="form-control select2" name="class" id="class">
                                            @foreach($classlist as $va)
                                                <option value="{{ $va->class }}">{{ $va->class.' '.$va->name }}</option>
                                            @endforeach
                                        </select>
                                        <br/>
                                        <label for="organizationCode2">機關代碼：</label>
                                        <select class="browser-default custom-select" name="organizationCode2" id="organizationCode2">
                                            @foreach($organizationlist as $va)
                                                <option value="{{ $va->enrollorg }}">{{ $va->enrollname }}</option>
                                            @endforeach
                                        </select>
                                        <br/>
                                        <button type="button" class="btn btn-primary mt-2 mb-0" onClick="demanddata()">查詢</button>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" >機關名稱</th>    
                                                    <th class="text-center" >姓名</th>    
                                                    <th class="text-center" width="70">性別</th>    
                                                    <th class="text-center" >職稱</th>    
                                                    <th class="text-center" >官職等</th>    
                                                </tr>
                                            </thead>
                                            <tbody id="demandlist">               
                                            </tbody>            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal5 重設填報方式 -->
                    <form method="POST" action="/admin/demand_survey/resetdata" enctype="multipart/form-data" id="form5" name="form5">
                        {{ csrf_field() }}
                        <div class="modal fade" id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">重設填報方式</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <input type=hidden id="from2id" name="from2id" value="{{$iddata[0]->id}}" >
                                    <input type=hidden id="resetdatayerly" name="resetdatayerly" value="{{$iddata[0]->yerly}}" >
                                    <input type=hidden id="resetdatatimes" name="resetdatatimes" value="{{$iddata[0]->times}}" >
                                    <input type=hidden id="resetdataorgan" name="resetdataorgan" value="" >
                                    <div class="modal-body">
                                        <p>若重設填報方式，將刪除該機關及其所屬機關的填報資料?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="submitForm('#form5');">是</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">否</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Modal6 班別需求整併 -->
                    <form method="POST" action="/admin/demand_survey/marge" enctype="multipart/form-data" id="form6" name="form6">
                        {{ csrf_field() }}
                        <div class="modal fade" id="exampleModal6" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <input type="hidden" name="yerly" value="{{$iddata[0]->yerly}}">
                                <input type="hidden" name="times" value="{{$iddata[0]->times}}" >
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">班別需求整併</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="card-header"><h3 class="card-title">班別需求整併</h3></div>
                                        <div class="card-body pt-4 text-center">
                                            <label for="class">班別：</label>
                                            <select class="form-control select2" name="class1" id="class1">
                                            @foreach ($classlist as $va)
                                            <option value="{{ $va->class }}">{{ $va->class.' '.$va->name }}</option>
                                            @endforeach    
                                            </select>
                                            <br/>
                                            <label for="class">合併至 班別：</label>
                                            <select class="form-control select2" name="class2" id="class2">
                                            @foreach ($classlist as $va)
                                            <option value="{{ $va->class }}">{{ $va->class.' '.$va->name }}</option>
                                            @endforeach     
                                            </select>
                                            
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="merage();">確定合併</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="card-footer">
                        <a href="/admin/demand_survey">
                            <button type="button" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        </a>
                        <a href="/admin/demand_survey">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection
@section('js')
<script>

$('#sdate').datepicker();
$('#edate').datepicker();
var sdate = <?=$iddata[0]['sdate']?>;
var edate = <?=$iddata[0]['edate']?>;
function merage(){
    if (confirm("您確定執行合併？")){
        $("#form6").submit();
    }else{
        return false;
    }
}
function canceldata(){
    if($('#organizationCode').val()==''){
        alert('未點選機關!');
        return false;
    }
    $('#exampleModal3').modal('show');
}

function demanddata(){

    $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/demand_survey/demanddata",
            data: {
                class: $('#class').val(),
                organizationCode2: $('#organizationCode2').val()
            },
            success: function(data){
                console.log(data);
                let dataArr = JSON.parse(data);
                let tempHTML = "";
                for(let i=0; i<dataArr.length; i++) 
                {
                    tempHTML += "<tr>\
                        <td class='text-center'>"+dataArr[i].enrollname+"</td>\
                        <td class='text-center'>"+dataArr[i].NAME+"</td>\
                        <td class='text-center'>"+dataArr[i].sex+"</td>\
                        <td class='text-center'>"+dataArr[i].POSITION+"</td>\
                        <td class='text-center'>"+dataArr[i].rank+"</td>\
                    </tr>";
                    
                };
                $("#demandlist").html(tempHTML);					
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
}

function resetdata(){
    if($('#resetdataorgan').val()==''){
        alert('未點選機關!');
        return false;
    }
    $('#exampleModal5').modal('show');
}

function chooseType(base,index,name,name2=null) { //選擇機關
        
        var Detype = '';
        var enrollorg = index;
        var enrollorgname = name;
        var enrollorgname2 = name2;
        
        $('#organizationCode').val(enrollorg);  //賦予取消凍結
        $('#organizationName').val(enrollorgname); 
        $('#resetdataorgan').val(enrollorg);  //賦予重設填表
        $('#DemandSurveyTitle').html('');
        year = <?=$iddata[0]->yerly?>;
        times = <?=$iddata[0]->times?>;
        $('#DemandSurveyData').html('<tr><td></td><td class="text-center">LODAING...</td><td></td><td></td></tr>');
        console.log(index);
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            url:"/admin/demand_survey/form2/getDemandSurveyData",
            data: {
                year: year,
                times: times,
                enrollorg:enrollorg
            },
            success: function(data){
                console.log(data.msg);
                let tempHTML = "";
                let classlistHTML = "";
                let url = "";
                $.each(data.msg.list, function (key, value) { 
                    tempHTML+='<tr><td class="text-left">'+value.class+'</td>'
                                +'<td class="text-left">'+value.name+'</td>'
                                +'<td class="text-left">'+value.checkcnt+'</td>'
                                +'<td class="text-left">'+value.applycnt+'</td></tr>';
                });
                $('#DemandSurveyData').html(tempHTML);
                
                console.log(data.msg.title);
                if(base=='1'){
                    url += '行政機關';
                }else if(base =='2'){
                    url += '訓練機構';
                }
                if(enrollorgname2==null){
                    url += '/'+enrollorgname;
                }else{
                    url += '/'+enrollorgname2+'/'+enrollorgname;
                }
                if(data.msg.title.report!=null ){
                    url += data.msg.title.report;
                }
                tempHTML = '<label class="control-label text-left">代碼：<span>'+enrollorg+'</span></label>\
                            <label class="control-label text-left">名稱：<span>'+enrollorgname+'</span></label>\
                            <label class="control-label text-left">關係：<span>'+url+'</span></label>';
                if(data.msg.title.type==null ){
                    if(base=='1'){
                        tempHTML+='<label class="control-label text-left">填表方式：<span>人數</span></label>';
                    }
                }else{
                    if(data.msg.title.type == '2'){
                        tempHTML+='<label class="control-label text-left">填表方式：<span>名冊</span></label>';
                    }else{
                        tempHTML+='<label class="control-label text-left">填表方式：<span>人數</span></label>';
                    }
                }            
                tempHTML+= '<label class="control-label text-left">開始填表日期：<span>'+data.msg.title.sdate+'</span></label>\
                            <label class="control-label text-left">結束填表日期：<span>'+data.msg.title.edate+'</span></label>';
                
                $('#DemandSurveyTitle').html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
                $('#DemandSurveyData').html('');
            }
        });
        
        
    }
// 初始化階層樹
setTimeout(() => {
    $("#treeview").treeview({
        persist: "location",
        collapsed: true,
        unique: false,
        toggle: function() {
            // console.log("%s was toggled.", $(this).find(">span").text());
        }
    });
}, 1000);
</script>
@endsection 