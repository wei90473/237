@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <style>
        label {
            margin: 0px;
        }
        .item_con {
            display: flex;
            align-items: center;
        }
        .selectStyle > .select2-container {
            width: 150px !important;
            margin-right: 5px;
        }
        .select2 .select2-container .select2-container--default {
            width: 150px !important;
        }
        .display_inline {
            display: inline-block;
            margin-right: 5px;
        }
        .col-sm-10 {
            padding: 0px;
        }
    </style>

    <?php $_menu = 'site_manage';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期資料處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/site_manage" class="text-info">洽借場地班期資料處理列表</a></li>
                        <li class="active">洽借場地班期資料處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/site_manage/'.$data->class, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/site_manage/create', 'id'=>'form']) !!}
            @endif
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">洽借場地班期資料處理</h3></div>
                    <div class="card-body pt-4">


                        <!-- 班號 -->
                        <div class="form-group row">
                            @if(!isset($data))
                            <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <input type="text" class="form-control input-max" id="yerly" name="yerly" placeholder="請輸入年度" value="{{ date('Y')-1911 }}" autocomplete="off" required maxlength="3" >
                            </div >
                            <div class="col-md-1">
                                <input type="text" class="form-control input-max" id="classtype" name="classtype" value="G" autocomplete="off"  maxlength="255" readonly>
                            </div >
                            <div class="col-md-1">
                               <input type="text" class="form-control input-max" id="class_b" name="class_b" value="{{ $base->getMaxSitClass() }}" autocomplete="off" required maxlength="2" > 
                            </div>
                            <!-- 院區代號 -->
                            <div class="col-md-1">
                                <input type="text" class="form-control input-max" id="branchcode" name="branchcode" value="{{ old('branch', (isset($data->branch))? (($data->branch ==1)?'A':'B') : 'A')  }}" autocomplete="off"  maxlength="1" readonly>
                            </div>
                            <!-- 辦班院區 -->
                            <label class="col-form-label text-md" >辦班院區<span class="text-danger">*</span></label>
                            <div class="col-md-2">
                                <select id="branch" name="branch" class="select2 form-control select2-single input-max" onchange="getbranchcode()">
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}"  {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @else                           
                            <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                            <div class="col-md-2">
                                <input type="text" class="form-control input-max" id="class" name="class" value="{{ $data->class }}" readonly>
                                <!-- <input type="text" class="form-control input-max" value="{{ $data->class.$data->branchcode }}" autocomplete="off" required maxlength="255" readonly> -->
                            </div>
                            <!-- 辦班院區 -->
                            <label class="col-form-label text-md" >辦班院區<span class="text-danger">*</span></label>
                            <div class="col-md-2">
                                <input type="text" class="form-control input-max" id="branch" name="branch" value="{{ config('app.branch.'.$data->branch) }}" autocomplete="off"  readonly>
                            </div>
                            @endif
                            
                            <!-- 課程分類 -->
                            <label class="control-label text-md pt-2">課程分類</label>
                            <div class="col-sm-2 item_con">
                                @if(isset($data))
                                <input type="text" class="form-control input-max" id="classtypename" name="classtypename" value="{{ $data->classtype.config('app.classtype.'.$data->classtype) }}" autocomplete="off"  maxlength="255" readonly>
                                @else
                                <select id="classtypename" name="classtypename" class="select2 form-control select2-single input-max" onchange="getclasstype()">
                                    @foreach(config('app.classtype') as $key => $va)
                                        <option value="{{ $key }}"  {{ 'G' == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
                        <!-- 班別 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入班別名稱(中文)" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 上課方式 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">上課方式</label>
                            <div class="col-md-2">
                                <select id="style" name="style" class="select2 form-control select2-single input-max" >
                                        <option value="4" >4 其他</option>
                                </select>
                            </div>
                            <!-- 新增上課方式-->
                            <div>
                                <button id="newtype_class" class="btn btn-number" onclick="chooseClassDay()" type="button">+</button>
                            </div>
                            <div class="col-md-5"  onclick="chooseClassDay()">
                                    <input type="text" class="form-control input-max" id="newstyle" name="newstyle" placeholder="新增上課方式"  value="" autocomplete="off"  maxlength="255" readonly>
                            </div>
                        </div>
                        <!-- 班別類型 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別類型</label>
                            <div class="col-md-3">
                                <select id="process" name="process" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.process') as $key => $va)
                                        <option  value="{{ $key }}" {{ old('process', (isset($data->process))? $data->process : 3) == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- 訓練性質 -->
                            <label class="col-form-label text-md">訓練性質</label>
                            <div class="col-md-3">
                                <select id="traintype" name="traintype" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.traintype') as $key => $va)
                                        <option value="{{ $key }}" {{ old('traintype', (isset($data->traintype))? $data->traintype : 3) == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 訓期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2"><span class="text-danger">*</span>訓期</label>
                            <div class="col-sm-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="period" name="period" min="1" placeholder="請輸入訓期" value="{{ old('period', (isset($data->period))? $data->period : '') }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" maxlength="2" onchange="kindChange();countTotalDays();" required>
                                </div>
                            </div>
                            <!-- 訓期單位 -->
                            <div class="col-md-2 pt-2" onchange="countTotalDays();">
                                @foreach(config('app.kind') as $key => $va)
                                    @if($key=="1")
                                      <!--  <span style="display:inline" id="week"><input type="radio" id="kind" name="kind" value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'checked' : '' }}>{{ $va }}</span>  -->
                                    @else
                                       <input type="radio" id="kind" name="kind" value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 2) == $key? 'checked' : '' }}>{{ $va }}
                                    @endif 
                                  
                                @endforeach
                            </div>
                            <!-- 訓練總天數 -->
                            <label class="col-md-2 col-form-label text-md-right">訓練總天數</label>
                            <div class="col-sm-1">
                                <input type="text" class="form-control number-input-max" id="trainday" name="trainday" value="{{ old('trainday', (isset($data->trainday))? $data->trainday : '0') }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" >
                            </div>
                            <!-- 訓練總時數 -->
                            <label class="control-label text-md pt-2">訓練總時數</label>
                            <div class="col-sm-1">
                                <input type="text" class="form-control number-input-max" id="trainhour" name="trainhour" value="{{ old('trainhour', (isset($data->trainhour))? $data->trainhour : '0') }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="5" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 正取人數 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">每期人數</label>
                            <div class="col-sm-2 md-left" >
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="quota" name="quota" min="1" placeholder="請輸入正取名額" value="{{ old('quota', (isset($data->quota))? $data->quota : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" >
                                </div>
                            </div>
                            <!-- 備取人數 -->
                            <label class="control-label text-md pt-2">備取人數</label>
                            <div class="col-sm-2 md-left">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="extraquota" name="extraquota" min="1" placeholder="請輸入後補名額" value="{{ old('extraquota', (isset($data->extraquota))? $data->extraquota : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" >
                                </div>
                            </div>
                        </div>
                        <!-- 參加對象 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">參加對象</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="target" id="target">{{ old('target', (isset($data->target))? $data->target : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 備註 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="remark" id="remark">{{ old('remark', (isset($data->remark))? $data->remark : '') }}</textarea>
                            </div>
                        </div>
                        <!-- 訓練績效計算方式 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">訓練績效計算方式</label>
                            <div class="col-md-6 pt-2">
                                @foreach(config('app.cntflag') as $key => $va)
                                    <input type="radio" id="cntflag" name="cntflag" value="{{ $key }}" {{ old('cntflag', (isset($data->cntflag))? $data->cntflag : 2) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                    <!-- onclick="submitForm('#form');"  -->
                        <button type="button" class="btn btn-sm btn-info" onclick="submitForm('#form');"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))
                        <!-- <span onclick="$('#del_form').attr('action', '/admin/site_manage/{{$data->class}}');" data-toggle="modal" data-target="#del_modol">
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>刪除 -->
                                                                
                            <button type="button" onclick="$('#del_form').attr('action', '/admin/site_manage/{{$data->class}}');" data-toggle="modal" data-target="#del_modol" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>                         
                        @endif
                            <button type="button" class="btn btn-sm btn-danger"  onclick="history.go(-1)"><i class="fa fa-reply"></i>回上一頁</button>
                        
                    </div>
                </div>
            </div>

           

        </div>
    </div>


    <!-- 上課方式 日期選擇 modal -->
	<div class="modal fade bd-example-modal-lg classDay" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-dialog_80" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">上課方式</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    @if(!isset($data))
                    <div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time1" value="Y" ><label>週一</label> 
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time2" value="Y" ><label>週二</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time3" value="Y" ><label>週三</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time4" value="Y" ><label>週四</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time5" value="Y" ><label>週五</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time6" value="Y" ><label>週六</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time7" value="Y" ><label>週日</label>
                        </div>
                    </div>
                    <div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="holiday" value="Y" ><label>含國定假日</label>
                        </div>
                    </div>
                    @else
		        	<div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time1" value="Y" {{ $data->time1=='Y'?'Checked':'' }}><label>週一</label> 
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time2" value="Y" {{ $data->time2=='Y'?'Checked':'' }}><label>週二</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time3" value="Y" {{ $data->time3=='Y'?'Checked':'' }}><label>週三</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time4" value="Y" {{ $data->time4=='Y'?'Checked':'' }}><label>週四</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time5" value="Y" {{ $data->time5=='Y'?'Checked':'' }}><label>週五</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time6" value="Y" {{ $data->time6=='Y'?'Checked':'' }}><label>週六</label>
                        </div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="time7" value="Y" {{ $data->time7=='Y'?'Checked':'' }}><label>週日</label>
                        </div>
                    </div>
                    <div>
                        <div class="item_con display_inline">
                            <input type="checkbox" name="holiday" value="{{$data->holiday}}" {{ $data->holiday=='Y'?'Checked':'' }}><label>含國定假日</label>
                        </div>
                    </div>
                    @endif
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="gettime()">確定</button>
			        <!-- <button type="button" class="btn btn-info" data-dismiss="modal">取消</button> -->
		        </div>
		    </div>
		</div>
	</div>
    {!! Form::close() !!}
    
    <!-- 班別類別 modal -->
	<div class="modal fade bd-example-modal-lg classType" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-dialog_80" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">班別類別</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    <label class="control-label text-md-right">主</label>
                    <select class="form-control select2" name="status">
                        <option>領導能力發展(具管理訓練性質)</option>
                        <option>機關業務知能訓練(具專業訓練性質)</option>
                        <option>停用</option>
                    </select>
                    <label class="control-label text-md-right pt-2">次</label>
                    <select class="form-control select2" name="status">
                        <option>管理訓練 102</option>
                        <option>領導統御 103</option>
                        <option>危機管理 104</option>
                    </select>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">確定</button>
			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
		        </div>
		    </div>
		</div>
	</div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script>
        // 選擇上課方式 日期
        function chooseClassDay() {
            $(".classDay").modal('show');
        }

        // 選擇班別類別
        function chooseClassType() {
            $(".classType").modal('show');
        }
    //課程分類賦予
    function getclasstype(){
        var classtype = $('#classtypename').val();
        $('#classtype').val(classtype);
        // if(classtype =='A'){
        //     $('#classtypename').val('A生活知能');
        // }else if(title == 'B'){
        //     $('#classtypename').val('B藝術文化');
        // }else if(title == 'C'){
        //     $('#classtypename').val('C健康休閒');
        // }else if(title == 'D'){
        //     $('#classtypename').val('D領導管理');
        // }else if(title == 'E'){
        //     $('#classtypename').val('E人文知能');
        // }else if(title == 'F'){
        //     $('#classtypename').val('F知識科技');
        // }else{
        //     $('#classtypename').val('G專題演講');
        // }
    }    
    //院區代號賦予
    function getbranchcode(){
        var title = $('#select2-branch-container')[0].innerText;
        if(title =='臺北院區'){
            $('#branchcode').val('A');
        }else if(title = '南投院區'){
            $('#branchcode').val('B');
        }else{
            $('#branchcode').val('');
        }
    }
    //取得上課方式
    function gettime(){
        var timestype = '';
        if ($(':checkbox[name="time1"]:checked').prop("checked")) timestype += '週一';
        if ($(':checkbox[name="time2"]:checked').prop("checked")) timestype += '週二';
        if ($(':checkbox[name="time3"]:checked').prop("checked")) timestype += '週三';
        if ($(':checkbox[name="time4"]:checked').prop("checked")) timestype += '週四';
        if ($(':checkbox[name="time5"]:checked').prop("checked")) timestype += '週五';
        if ($(':checkbox[name="time6"]:checked').prop("checked")) timestype += '週六';
        if ($(':checkbox[name="time7"]:checked').prop("checked")) timestype += '週日';
        if ($(':checkbox[name="holiday"]:checked').prop("checked")) timestype += '含例假日';
        
        console.log(timestype);
        $('#newstyle').val(timestype);
    }


    function gettype(){  //**
         //上課:非其他不＋顯示
        if($("#style").val() == 4) {
            $("#btn-classStyle").css("display", "block");
            $("#newstyle").css("display", "block");
            $("#newtype_class").css("display", "block");
            
        }else {
            $("#btn-classStyle").css("display", "none");
            $("#newstyle").css("display", "none");
            $("#newtype_class").css("display", "none");
        }
         //上課:密集:單位(周)不顯示
        var title = $('#select2-style-container')[0].title;
        if (title !== '密集式') {
            $('#week').css('display','none');
            $("input[name='kind']:radio[value='2']").attr('checked','true');
        }else{
            $('#week').css('display','inline');
        }
    }
    // 訓期類別為天跟周要為整數,天為小數第一位
    function kindChange() {
        var kindval = $(':radio[name="kind"]:checked').val();
        // 確保訓期有值
        if ( ! $('#period').val()) {
            $('#period').val(1)
        }

        if (kindval == '3') {
            // 時數為小數點第一位
            var period = parseFloat($('#period').val());
            $('#period').val(period.toFixed(1));
        } else {
            // 時數為整數
            $('#period').val(parseInt($('#period').val()));
        }
    }
    // 計算總天數、時數
    function countTotalDays() {
        var kindval = $(':radio[name="kind"]:checked').val();
        // 確保訓期有值
        if ( ! $('#period').val()) {
            $('#period').val(1)
        }
        // 確保每日上課時數有值
        // if ( ! $('#dayhour').val()) {
        //     $('#dayhour').val(8)
        // }
        // 取得訓期
        var period = parseFloat($('#period').val());
        // 取得每天時數
        // var dayhoues = parseInt($('#dayhour').val());
        var dayhoues = 6;
        // 計算總天數
        if (kindval == '1') {
            // 以週為單位
            var total_days = period * 5;
        } else if (kindval == '2') {
            // 以天為單位
            if(dayhoues < 3 ){ //若每日上課時數<3小時，訓練天數以0.5天計。
                var total_days = period.toFixed(0)/2 ;
            }else{
                var total_days = period.toFixed(0) ;
            }
        } else {
            // 以時為單位
            if(period<3){
                var total_days = 0.5;
            }else{
                var total_days = 1;
            }
            // var total_days = period / dayhoues;
        }
        // 寫入訓練傯天數
        $('#trainday').val(total_days);

        // 計算總時數
        if (kindval == '1') {
            // 以週為單位
            var totalhours = period * 5 * dayhoues;

        } else if (kindval == '2') {
            // 以天為單位
            var totalhours = parseInt(period * dayhoues);
        } else {
            // 以時為單位
            var totalhours = period;
        }
        // 寫入訓練總時數
        $('#trainhour').val(totalhours);
    }
    function deleteClass(){
        if(confirm('確定要刪除嗎?')){
            $("#del_form").submit();
        }
    }
    // $(document).ready(function (e) {
    //     gettype();
        
    // });
gettime();
    </script>
<!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
@endsection