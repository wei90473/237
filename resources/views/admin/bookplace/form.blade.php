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
        .custom-select {
            display: inline-block;
            height: calc(2.25rem + 2px);
            padding: .375rem 1.75rem .375rem .75rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            vertical-align: middle;
            background: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e) no-repeat right .75rem center/8px 10px;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
    </style>
    <?php $_menu = 'bookplace';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地預約</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes" class="text-info">場地預約</a></li>
                        <li class="active">場地預約</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ($mode=='edit')
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/bookplace/update', 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'route'=>"bookplace_post", 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">場地預約</h3></div>
                    <div class="card-body pt-4">
                        <input type="hidden" name="branch" id="branch" value="{{ $arr['branch'] }}">
                        <input type="hidden" name="time"  value="{{ $arr['time'] }}">
                        <input type="hidden" name="type" value="">
                        <!-- 班號、期別 -->
                        <?php if($mode!='edit') {?>
                        <div class="form-group row" >
                            <div class="col-sm-1" ></div>
                            <button type="button" class="col-sm-1 btn btn-info" id="classbtn" style="display: inline" onclick="classmeet(2)">班別</button>
                            <button type="button" class="col-sm-1 btn btn-info" id="meetbtn" style="display: none" onclick="classmeet(1)">會議</button>
                            <div class="col-sm-6" id="classList" style="display: inline">
                                <select id="class" name="class" class="select2 form-control select2-single input-max"  onchange="classChange()">
                                    @foreach($classList as $key => $va)
                                        <option value="{{ $va->class }}" {{ old('class', (isset($arr["class"]))? $arr["class"] : 1) == $va->class? 'selected' : '' }}>{{ $va->class }} {{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6" id="meetList"  style="display: none">    
                                <select id="meet" name="meet" class="select2 form-control select2-single input-max" >
                                    @foreach($meetList as $key => $va)
                                        <option value="{{ $va->class.$va->term }}" >{{ $va->class.' '.$va->name.'('.$va->term.')' }}</option>
                                    @endforeach
                                </select>
                            </div>
                                <label id="termtitle" class="col-sm-1 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                                <div class="col-sm-2" id="termlist" >
                                    <select id="term" name="term" class="select2 form-control select2-single input-max" required>

                                    </select>
                                </div>
                        </div>
                        <?php }else{?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別/會議</label>
                            <input type="hidden" name="class" value="{{$arr['class']}}">
                            <input type="hidden" name="term" value="{{$arr['term']}}">
                            <div class="col-sm-3">
                                <input type="text" value="{{$arr['class']}}" class="form-control" disabled>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" value="{{$show_class_name}}" class="form-control" disabled>
                            </div>
                            <label class="col-sm-1 control-label text-md-right pt-2">期別/編號<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" value="{{$arr['term']}}" class="form-control" disabled>
                            </div>
                        </div>
                        <?php }?>

                        <!-- 場地 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">場地</label>
                            <div class="col-sm-3">
                                <select class="custom-select" id="classroom" name="site"></select>
                            </div>
                        </div>

                        <!--日期-->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">日期</label>
                            <div class="input-group col-3">
                                    <input type="text" id="sdate3" name="date" class="form-control" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                            </div>
                            <?php if($mode!='edit'){?>
                            <div class="col-3">
                                <button type="button" class="btn btn-info"  value="<?=$arr['site']?>" onclick="set_week(this);">週期性預約</button>
                                <input type="hidden" name="setweek" id="setweek">
                            </div>
                            <?php }else{?>
                                <input type="hidden" name="origin_time" id="origin_time" value="<?=$arr["time"]?>">
                                <input type="hidden" name="origin_date" id="origin_date" value="<?=$arr["date"]?>">
                            <?php }?>
                        </div>
                        
                        <!--時段-->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">時段</label>
                            <?php   $time = (isset($reserve_info->time))?$reserve_info->time : $arr["time"];
                                    $select_A = $select_B = $select_C = $select_D = $select_E = $select_F ='';
                                      switch($time){
                                        case 'A':
                                            $select_A='checked';
                                            break;
                                        case 'B':
                                            $select_B='checked';
                                            break;
                                        case 'C':
                                            $select_C='checked';
                                            break;
                                        case 'D':
                                            $select_D='checked';
                                            break;
                                        case 'E':
                                            $select_E='checked';
                                            break;
                                        case 'F':
                                            $select_F='checked';
                                            break;
                                        default:
                                            break;
                                      }
                            ?>
                            <div class="col-md-10 mt-1">
                                <input type="radio" name="time" value="A" id="period1"  {{$select_A}}>上午
                                <input type="radio" name="time" value="B" id="period2"  {{$select_B}}>下午
                                <input type="radio" name="time" value="C" id="period3"  {{$select_C}}>晚上
                                <?php if($mode!='edit'){?>
                                <input type="radio" name="time" value="G" id="period4" >白天(上午、下午)
                                <input type="radio" name="time" value="H" id="period5" >全天(上午、下午、晚上)
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right"></label>
                            <div class="col-md-10 mt-1">
                                <label class="form-inline">
                                    <input type="radio" name="time" value="D" >其他
                                    <input type="text"  id="other2" class="form-control mr-2" maxlength="4" name="stime" >
                                    <input type="text"  id="other3" class="form-control mr-3" maxlength="4" name="etime" >
                                    <input type="radio" name="time" value="E"  >第一場
                                    <input type="radio" name="time" value="F" class="ml-3" >第二場 
                                </label>
                            </div>
                        </div>
                        @if($arr['branch']=='1')
                        <!-- 備註 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="purpose" id="purpose">{{ old('purpose', (isset($reserve_info->purpose))? $reserve_info->purpose : '') }}</textarea>
                            </div>
                        </div>
                        <!--聯絡人-->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">聯絡人</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="liaison" name="liaison" autocomplete="off" maxlength="50" value="{{ old('purpose', (isset($reserve_info->liaison))? $reserve_info->liaison : '') }}">
                            </div>
                            <label class="col-sm-2 control-label text-md-right">人數</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-max" id="cnt" name="cnt" autocomplete="off" maxlength="4" value="{{ old('purpose', (isset($reserve_info->cnt))? $reserve_info->cnt : '') }}">
                            </div>
                        </div>
                      
                        <br>
                        <!--座位方式-->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">座位方式</label>
                            <div class="col-md-2 pt-2">
                                <?php isset($reserve_info->seattype)? $seattype=$reserve_info->seattype : $seattype='C';
                                      $select_A='';$select_B='';$select_C='';$select_D='';$select_E='';
                                      switch($seattype){
                                        case 'A':
                                            $select_A='selected';
                                            break;
                                        case 'B':
                                            $select_B='selected';
                                            break;
                                        case 'C':
                                            $select_C='selected';
                                            break;
                                        case 'D':
                                            $select_D='selected';
                                            break;
                                        case 'E':
                                            $select_E='selected';
                                            break;
                                        default:
                                            break;
                                      }
                                ?>
                                <select class="custom-select" name="seattype" id="seattype">
                                    <option value="A" {{$select_A}}>A 標準型</option>
                                    <option value="B" {{$select_B}}>B 馬蹄型</option>
                                    <option value="C" {{$select_C}}>C T型</option>
                                    <option value="D" {{$select_D}}>D 菱型</option>
                                    <option value="E" {{$select_E}}>E 其他</option>
                                </select>
                            </div>
                        </div>
                        <!--場地使用者-->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">場地使用者</label>
                            <?php isset($reserve_info->usertype)? $usertype=$reserve_info->usertype : $usertype='1';
                                      $select_A='';$select_B='';$select_C='';$select_D='';$select_E='';$select_F='';
                                      switch($usertype){
                                        case '1':
                                            $select_A='checked';
                                            break;
                                        case '2':
                                            $select_B='checked';
                                            break;
                                        case '3':
                                            $select_C='checked';
                                            break;
                                        case '4':
                                            $select_D='checked';
                                            break;
                                        case '5':
                                            $select_E='checked';
                                            break;
                                        case '6':
                                            $select_F='checked';
                                            break;
                                        default:
                                            break;
                                      }
                            ?>
                            <div class="col-md-3">
                                <div class="col-md-12" id="usertype">
                                    <input type="radio" name="usertype"  value="1" {{$select_A}}>本學院自辦訓練及活動-學院
                                </div>
                                <div class="col-md-12">
                                    <input type="radio" name="usertype"  value="2" {{$select_B}}>本學院借洽租借給民間機關
                                </div>
                                <div class="col-md-12">
                                    <input type="radio" name="usertype"  value="3" {{$select_C}}>會館接洽租借給民間機構
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="col-md-12">
                                    <input type="radio" name="usertype"  value="4" {{$select_D}}>本學院接洽租借給公務機關
                                </div>
                                <div class="col-md-12">
                                    <input type="radio" name="usertype"  value="5" {{$select_E}}>會館接洽租借給公務機關
                                </div>
                                <div class="col-md-12">
                                    <input type="radio" name="usertype"  value="6" {{$select_F}}>本學院自辦訓練及活動-人事總處
                                </div>
                            </div>
                        </div>

                        <!--訂席代號 宴會名稱-->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">訂席代號</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="bqno" name="bqno" value="{{ old('purpose', (isset($reserve_info->bqno))? $reserve_info->bqno : '') }}" disabled>
                            </div>
                            <label class="col-md-2 col-form-label text-md-right">宴會名稱</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="bqname" name="bqname" value="{{ old('purpose', (isset($reserve_info->bqname))? $reserve_info->bqname : '') }}"disabled>
                            </div>
                        </div>
                        @endif


                      
                    </div>
                    <div class="card-footer">
                        <button type="submit"  class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                            <?php if($mode=='edit'){?>
                                <button type="button" onclick="actionDelete()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>                         
                            <?php } ?>
                        <a href="/admin/bookplace/index">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>
          
            {!! Form::close() !!}

        </div>
    </div>
    <?php if($mode=='edit'){?>
        {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/bookplace/delete', 'id'=>'deleteform']) !!}
           <input type="hidden" name="branch" value="{{ $arr['branch'] }}">
           <input type="hidden" name="site"  value="{{ $arr['site'] }}">
           <input type="hidden" name="date"  value="{{ $arr['date'] }}">
           <input type="hidden" name="time"  value="{{ $arr['time'] }}">
        {!! Form::close() !!}                            
    <?php } ?>



@endsection

@section('js')
<script>
    var site="<?=$arr["site"]?>";
    var classroom_info='';
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
        
        var y=<?=substr($arr['date'],0,3);?>;
        var m=<?=substr($arr['date'],3,2);?>;
        var d=<?=substr($arr['date'],5,2);?>;
        if(m<10){
            m='0'+m;
        }
        if(d<10){
            d='0'+d;
        }
        var t=y+'/'+m+'/'+d;
        $("#sdate3").val(t);
        var branch="<?=$arr["branch"]?>";
        _ajax(branch);

        $("select[id='classroom']").change(function(){    
            // timeControl($("#classroom").val());
        });
        classChange();
        classmeet(1);
    });

    // 切換類型
    function classmeet(type){
        if(type=='1'){
            $("#classbtn,#classList,#termlist,#termtitle").css('display','inline');
            $("#meetbtn,#meetList").css('display','none');

        }else{
            $("#meetbtn,#meetList").css('display','inline');
            $("#classbtn,#classList,#termlist,#termtitle").css('display','none');
        }
        $('input[name=type]').val(type);
    }
    // 取得期別
    function classChange()
    {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/training_survey/getterm',
                data: { classes: $('#class').val(), selected: $('input[name=selected]').val()},
                success: function(data){
                    $('#term').html(data);
                    $("#term").trigger("change");
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
    }

    function set_week(obj)
    {
        var iHeight=(window.screen.availHeight)*0.6;
        var iWidth=(window.screen.availWidth)*0.3;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2; 
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2; 
        window.open("/admin/bookplace/setWeek/"+obj.value,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
    }

   

        // 取得課程
    // function getCourse()
    // {
    //         $.ajax({
    //             type: "post",
    //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //             dataType: "html",
    //             url: '/admin/training_process/getcourse',
    //             data: { classes: $('#class').val(), term: $('#term').val()},
    //             success: function(data){
    //                 $('#course_div').html(data);
    //             },
    //             error: function() {
    //                 alert('Ajax Error');
    //             }
    //         });
    // }

    function _ajax(type)
    {
        if(type < 3){
            var url="/admin/bookplace/getPlace/"+type;
        }else{
            alert('執行錯誤，請重新整理');
            return false;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET", //傳送方式
            url: url, //傳送目的地
            dataType: "json", //資料格式
            success: function(data) {
                classroom_info=data;
                $.each(data, function (i, data) {
                    $('#classroom').append($('<option>', { 
                        value: data.site,
                        text : data.name 
                    }));
                });

                $("#classroom").children().each(function(){
                    if ($(this).val()==site){
                        $(this).attr("selected", true); 
                        this.selected = true;
                        // timeControl($(this).val());
                    }
                });
            },
            error: function(data) {
                console.log('error');
            }
                    
        });
    }

    function timeControl(t)
    {
        //將所有的ID設為disabled
        for(var z=1;z<=5;z++){
            $('#period'+z).attr('disabled', true);
            $('#other'+z).attr('disabled', true); 
            $('#field'+z).attr('disabled', true); 
        }

        for(var i=0;i<classroom_info.length;i++){
            if(t==classroom_info[i]['site']){
                if(classroom_info[i]["type"]==1||classroom_info[i]["type"]==2||classroom_info[i]["type"]==4||classroom_info[i]["type"]==5){
                    if(classroom_info[i]["timetype"]==1){
                        for(var j=1;j<=5;j++){
                            $('#period'+j).attr('disabled', false);
                        }
                    }else{
                        for(var j=1;j<=3;j++){
                           $('#other'+j).attr('disabled', false); 
                        }
                    }
                }else{
                    for(var j=1;j<=2;j++){
                        $('#field'+j).attr('disabled', false); 
                    }
                }
            }
        }
    }

    //刪除
    function actionDelete(){
        $("#deleteform").submit();
    }
    


</script>
@endsection