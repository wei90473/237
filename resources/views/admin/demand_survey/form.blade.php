@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')


<style>
        /* .item_con {
            display: flex;
            align-items: center;
        } */
        /*.display_inline {
            display: inline-block;
            margin-right: 5px;
        }*/
        .halfArea {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0px 25px;
        }
        .item_con label {
            cursor: pointer;
        }
        .item_check.active, .item_uncheck.active {
            background-color: #d2f1ff;
        }
        /*.arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }*/
    </style>
    <?php $_menu = 'demand_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求調查處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_survey" class="text-info">需求調查處理</a></li>
                        <li class="active">新增或編輯</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_survey/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_survey/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">新增或編輯</h3></div>
                    <div class="card-body pt-4">


                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                @if(isset($data))
                                <input class="date form-control" value="{{$data->yerly}}" type="text"  disabled>
                                @else
                                <select class="browser-default custom-select" name="yerly" onchange="gettimes()">
                                @foreach($queryData['choices'] as $key => $va)
                                        <option value="{{ $key }}" {{ old('yerly', (isset($data->yerly))? $data->yerly : date('Y')-1911) == $va? 'selected' : '' }}>{{ $va }}</option>
                                @endforeach
                                </select>
                                @endif
                            </div>
                            
                            <!-- 辦班院區 -->
                            <label class="control-label text-md pt-2">院區<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                @if(isset($data))
                                <input class="date form-control" value="{{ config('app.branch.'.$data->branch)}}" type="text"  disabled>
                                @else
                                <select id="branch" name="branch" class="browser-default custom-select" onchange="getlist();gettimes();">>
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}"  {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        @if(isset($data))
                        <div class="form-group row">
                            <!-- 第幾次調查 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">第幾次<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" id="times" name="times" class="form-control" autocomplete="off" value="{{ $data->times }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" disabled>
                            </div>
                        </div>
                        @else
                        <div class="form-group row">
                            <!-- 第幾次調查 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">第幾次<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" id="times" name="times" class="form-control" autocomplete="off" value="" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" disabled>
                            </div>
                        </div>
                        @endif
                        <!-- 需求調查名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">需求調查名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-max" id="purpose" name="purpose" placeholder="請輸入需求調查名稱" value="{{ old('purpose', (isset($data->purpose))? $data->purpose : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>
                        <?php
                            $sdate = isset($data->sdate)? $data->sdate : '';
                            $edate = isset($data->edate)? $data->edate : '';
                        ?>
                        <!-- 填報開始日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">填報開始日期<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input class="date form-control" value="{{$sdate}}" type="text" id="sdate" name="sdate">
                            </div>
                            <!-- 填報結束日期 -->
                            <label class="control-label text-md pt-2">填報結束日期<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input class="date form-control" value="{{$edate}}" type="text" id="edate" name="edate">
                            </div>
                        </div>
                        
                        <hr size="8px" align="center" width="100%">        
                        <!-- 班別 -->
                        <!-- old STYLE -->
                        <?php
                            $yerly = isset($data->yerly)? $data->yerly : '';
                            $times = isset($data->times)? $data->times : '';
                        ?>
                        <?php $list = $base->getDemandSurveyClasses($yerly, $times);
                   
                        ?>
                        <!-- <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select class="select2 form-control select2-single input-max" multiple="multiple" required>
                                    @foreach($list as $va)
                                        <option value="{{ $va->class }}" {{  ($yerly == $va->yerly && $times == $va->times && $va->yerly)? 'selected' : '' }} >{{ $va->class }} {{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->
                        <!-- 新STYLE -->
                        <input type="text" class="form-control" name="class" hidden>
                        <div class="modal-body">
                            <div class="col-md-12" id="course_div" style='display: flex;'>
                                <!-- 未選取的課程  class="checkbox"-->
                                <div style="flex:1;">
                                    <span>可選取欄位</span>
                                    <div class="halfArea" style="flex:1;height:300px;max-width:400px;overflow:auto;">
                                        <table style="width:100%;">
                                            <tbody id="orgchk_uncheckList">
                                                <tr class="item_con orgchk_item item_uncheck">
                                                    <th>代碼  </th>
                                                    <th>班別</th>
                                                </tr>
                                                @foreach($queryData['classlist'][0] as $va)
                                                    @if($va['branch']=='1')
                                                        <tr class="item_con orgchk_item item_uncheck" onclick="selectItem(this, 'orgchk')" value="{{$va['class']}}" name="Tbranch">
                                                    @else
                                                        <tr class="item_con orgchk_item item_uncheck" onclick="selectItem(this, 'orgchk')" value="{{$va['class']}}" name="Nbranch">
                                                    @endif        
                                                        <td>{{$va['class']}}</td>
                                                        <td>{{$va['name']}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>  
                                        </table>
                                    </div>
                                </div>
                                

                                <div class="arrow_con">
                                    <button class="btn btn-primary" onclick="changeItem(true, 'orgchk')" style="margin-bottom:10px;"  type="button"><i class="fas fa-arrow-right" style="margin-right:3px;"></i>新增</button>
                                    <button class="btn btn-danger" onclick="changeItem(false, 'orgchk')"  type="button"><i class="fas fa-arrow-left" style="margin-right:3px;"></i>移除</button>

                                </div>

                                <!-- 已選取的課程 class="checkbox checkbox-primary"-->
                                <div style="flex:1;">
                                    <span>已選取欄位</span>
                                    <div class="halfArea" style="flex:1;height:300px;max-width:400px;overflow:auto;">
                                        <table style="width:100%;">
                                            <tbody id="class" name="class[]">
                                                <tr class="item_con orgchk_item item_check">
                                                    <th>代碼  </th>
                                                    <th>班別</th>
                                                </tr>
                                                @if(isset($data))
                                                <?php for($i=0; $i<sizeof($queryData['classlist'][1]); $i++) { ?>
                                                    <tr class="item_con orgchk_item item_check" onclick="selectItem(this, 'orgchk')" value="{{$va['class']}}">
                                                        <td>{{$queryData['classlist'][1][$i]['class']}}</td>
                                                        <td>{{$queryData['classlist'][1][$i]['name']}}</td>
                                                    </tr>
                                                <?php } ?>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END NEW -->
                    </div>
                    <div class="card-footer">
                        <button type="button"  onclick="confirmOrgchk();" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))
                            <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>                         
                        @endif
                        <a href="/admin/demand_survey">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
            @if(isset($data))
                {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/demand_survey/'.$data->id, 'id'=>'deleteform']) !!}
                
                {!! Form::close() !!}                            
            @endif
        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection
@section('js')
<script>
$( function() {
    $('#sdate').datepicker({   
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#edate').datepicker({   
        format: "twymmdd",
        language: 'zh-TW'
    });
  } );

    // 點選項目
    let leftCon;
    let rightCon;
    function selectItem(e, name) {
        leftCon = -1;
        rightCon = -1;
        let classname = name+"_item";

        if($(e).hasClass("active")) {
            $(e).removeClass("active");
            rightCon = -1;
            leftCon = -1;
            return;
        }
        else {
            $("."+classname).removeClass("active");

            $(e).addClass('active');
        }

        if($(e).hasClass("item_uncheck")) {
            leftCon = $(e).index();
            rightCon = -1;
        }
        else {
            rightCon = $(e).index();
            leftCon = -1;
        }
    }

    // 左右換項目
    function changeItem(type, name) {
        let classname = name+"_item";
        let countIndex = 0;
        if(!type) {      // 移除項目
            if(rightCon == -1) {
                return;
            }
            countIndex = $("."+classname+".item_uncheck").length;
            let a = $("."+classname+".item_check").eq(rightCon);
            a.addClass("item_uncheck");
            a.removeClass("item_check");
            a.find('input').prop("checked", false);
            $("."+classname+".item_uncheck").eq(countIndex-1).after(a);
            rightCon = -1;
            leftCon = countIndex;
        }
        else {      // 新增項目
            if(leftCon == -1) {
                return;
            }
            countIndex = $("."+classname+".item_check").length;
            let b = $("."+classname+".item_uncheck").eq(leftCon);
            b.addClass("item_check");
            b.removeClass("item_uncheck");
            b.find('input').prop("checked", true);
            $("."+classname+".item_check").eq(countIndex).after(b);
            leftCon = -1;
            rightCon = countIndex;      
        }
    }
    // 院區聯動班別
    function getlist(){
        var branch = $("#branch").val();
        console.log(branch);
        if(branch=='2'){
            $('tr[name="Tbranch"]').css('display','none');
            $('tr[name="Nbranch"]').css('display','table-row');
        }else{
            $('tr[name="Tbranch"]').css('display','table-row');
            $('tr[name="Nbranch"]').css('display','none');
        }
    }
    // pop選擇參訓機關
     function confirmOrgchk() {
            let classarray = '';
            for(let i=1; i<$(".orgchk_item.item_check").length; i++) {
                classarray += $(".orgchk_item.item_check").eq(i).find('td').html() + ',';
            }

            if($("input[name=sdate]").val() > $("input[name=edate]").val() ){
                alert('日期錯誤');
                return false;
            }
           
            $("input[name=class]").val(classarray);
            if(classarray ==''){
                alert('您尚未設定任一班別');
            }else{
                submitForm('#form');
            }
        }
    // 刪除
    function deleteClass(){
        if(confirm('確定要刪除嗎?')){
            $("#deleteform").submit();
        }
    }    
    // 取得
    function gettimes(){
        var yerly = $("select[name=yerly]").val();
        var branch = 'T';
        var listHTML ='';
        if(!yerly) return false;

        $("#orgchk_uncheckList").html('<a>Loading...</a>');
        $("#class").html('<tr class="item_con orgchk_item item_check">\
                                        <th>代碼  </th>\
                                        <th>班別</th>\
                                    </tr>');
        console.log(yerly);
        $.ajax({
            url:"/admin/demand_survey/gettimes",  
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "json",
            type: 'get',
            data: {
                yerly: yerly,
                branch:$("select[name=branch]").val()             
            },
            success: function(data){
                console.log(data.msg.list);
                if(data.status=='0'){
                   $("input[name=times]").val(data.msg.times);
                   $.each(data.msg.list, function (key, value) {
                        if(value.branch==1){
                            branch = 'T';
                        }else{
                            branch = 'N';
                        }
                        listHTML += '<tr class="item_con orgchk_item item_uncheck" onclick="selectItem(this, \'orgchk\')" value="'+value.class+'" name="'+branch+'branch"><td>'+value.class+'</td><td>'+value.name+'</td></tr>';
                    });
                   $("#orgchk_uncheckList").html('<tr class="item_con orgchk_item item_uncheck">\
                                        <th>代碼  </th>\
                                        <th>班別</th>\
                                    </tr>'+listHTML);
                }else{
                    return false
                }
            }
        });
    } 
    $(document).ready(function(){ 
        gettimes();

    })   
</script>
@endsection