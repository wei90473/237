@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'session';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">會議資料處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/session" class="text-info">會議資料處理列表</a></li>
                        <li class="active">會議資料處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/session/'.$data['meet'].$data['serno'].'/edit', 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/session/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">會議資料處理</h3></div>
                    <div class="card-body pt-4">

                        
                        @if(!isset($data))

                            <!-- 會議代號 -->
                        <div class="form-group row">    
                            <label class="col-md-2 col-form-label text-md-right">會議類型<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="meet" name="meet" class="browser-default custom-select"  onchange="getserno()">
                                    <option value="" >請選擇</option>
                                    @foreach(config('app.meet') as $key => $va)
                                        <option value="{{ $key }}" >{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <!-- 會議代號 -->
                            <label class="col-md-2 col-form-label text-md-right">會議代號<span class="text-danger">*</span></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="number" name="number" placeholder="請輸入會議代號" value="{{ old('number', (isset($data['meet']))? $data['meet'] : (date('Y') - 1911).date('m').date('d')) }}" autocomplete="off" maxlength="255" {{ (isset($data))? 'readonly' : '' }} onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onchange="getserno()" required>
                            </div>
                            <!-- 編號 -->
                            <label class="control-label text-md pt-2">編號</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="serno" name="serno" placeholder="" value="{{ old('serno', (isset($data['serno']))? $data['serno'] : 1) }}" autocomplete="off" maxlength="255" readonly>
                            </div>
                        </div>
                        <!-- 會議名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">會議名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入會議名稱" value="{{ old('name', (isset($data['name']))? $data['name'] : '') }}" autocomplete="off" maxlength="255" required>
                            </div>
                        </div>
                        <!-- 開始日期 -->
                        
                        <div class="form-group row">
                            <div class="input-group col-6">
                                <span class="col-sm-4 col-form-label text-md-right">開始日期<span class="text-danger">*</span></span>
                                <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off" value="{{ old('sdate', (isset($data['sdate']))? $data['sdate'] : (date('Y',strtotime('now'))-1911).date('md',strtotime('now')) ) }}" required>
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group col-6">
                                <label class="control-label text-md pt-2 mr-2">結束日期<span class="text-danger">*</span></label>
                                <input type="text" id="edate" name="edate" class="form-control" autocomplete="off" value="{{  old('edate', (isset($data['edate']))? $data['edate'] : (date('Y',strtotime('now'))-1911).date('md',strtotime('now')) ) }}" required>
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div> 
                        <!-- 人數 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">人數</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control number-input-max" id="cnt" name="cnt" min="1" placeholder="請輸入人數" value="{{ old('cnt', (isset($data['cnt']))? $data['cnt'] : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                            </div>
                            <!-- 聯絡人 -->
                            <?php $list = $base->getSponsor(); ?>
                            <label class="col-form-label text-md">聯絡人</label>
                            <div class="col-sm-4">
                                <select id="sponsor" name="sponsor" class="form-control select2">
                                   <option value="">請選擇</option>
                                    @foreach($list as $key => $va)
                                        <option value="{{ $key }}" {{ old('sponsor', (isset($data['sponsor']))? $data['sponsor'] : '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 主持人 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">主持人</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="chairman" name="chairman" placeholder="請輸入主持人" value="{{ old('chairman', (isset($data['chairman']))? $data['chairman'] : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                        <!-- 預約場地1 -->
                        <div class="form-group row">
                        <!-- 院區 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">預約場地一</label>
                            <div class="col-sm-4">
                                <select class="browser-default custom-select" id="branch1" name="branch1"  onchange="getbranch(1)">
                                   <option value="">請選擇</option>
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ old('branch1', (isset($data['branch1']))? $data['branch1'] : '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- 教室 -->
                            <div class="col-md-6" style="display: inline-flex;"id ="Taipeilist1">
                                <select id="siteT1" name="siteT1" class="browser-default custom-select" onchange="no_reservation_area(1)">
                                    <option value="">請選擇</option>
                                    @foreach($Taipeilist as $key => $va)
                                        <option value="{{ $va->site }}" {{ old('site1', (isset($data['site1']))? $data['site1'] : '') == $va->site? 'selected' : '' }}>{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" style="display: none;" id ="Nantoulist1" >
                                <select id="siteN1" name="siteN1" class="browser-default custom-select">
                                    <option value="">請選擇</option>
                                    @foreach($Nantoulist as $key => $va)
                                        <option value="{{ $va->roomno }}" {{ old('site1', (isset($data['site1']))? $data['site1'] : '') == $va->roomno? 'selected' : '' }}>{{ $va->roomname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 預約場地2 -->
                        <div class="form-group row">
                        <!-- 院區 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">預約場地二</label>
                            <div class="col-sm-4">
                                <select class="browser-default custom-select" id="branch2" name="branch2"  onchange="getbranch(2)">
                                   <option value="">請選擇</option>
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ old('branch2', (isset($data['branch2']))? $data['branch2'] : '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- 教室 -->
                            <div class="col-md-6" style="display: inline-flex;"id ="Taipeilist2">
                                <select id="siteT2" name="siteT2" class="browser-default custom-select" onchange="no_reservation_area(2)">
                                    <option value="">請選擇</option>
                                    @foreach($Taipeilist as $key => $va)
                                        <option value="{{ $va->site }}" {{ old('site2', (isset($data['site2']))? $data['site2'] : '') == $va->site? 'selected' : '' }}>{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" style="display: none;" id ="Nantoulist2" >
                                <select id="siteN2" name="siteN2" class="browser-default custom-select">
                                    <option value="">請選擇</option>
                                    @foreach($Nantoulist as $key => $va)
                                        <option value="{{ $va->roomno }}" {{ old('site2', (isset($data['site2']))? $data['site2'] : '') == $va->roomno? 'selected' : '' }}>{{ $va->roomname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 預約場地3 -->
                        <div class="form-group row">
                        <!-- 院區 -->
                            <label class="col-sm-2 control-label text-md-right pt-2">預約場地三</label>
                            <div class="col-sm-4">
                                <select class="browser-default custom-select" id="branch3" name="branch3"  onchange="getbranch(3)">
                                   <option value="">請選擇</option>
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ old('branch3', (isset($data['branch3']))? $data['branch3'] : '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        <!-- 教室 -->
                            <div class="col-md-6" style="display: inline-flex;"id ="Taipeilist3">
                                <select id="siteT3" name="siteT3" class="browser-default custom-select" onchange="no_reservation_area(3)">
                                    <option value="">請選擇</option>
                                    @foreach($Taipeilist as $key => $va)
                                        <option value="{{ $va->site }}" {{ old('site3', (isset($data['site3']))? $data['site3'] : '') == $va->site? 'selected' : '' }}>{{ $va->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" style="display: none;" id ="Nantoulist3" >
                                <select id="siteN3" name="siteN3" class="browser-default custom-select">
                                    <option value="">請選擇</option>
                                    @foreach($Nantoulist as $key => $va)
                                        <option value="{{ $va->roomno }}" {{ old('site3', (isset($data['site3']))? $data['site3'] : '') == $va->roomno? 'selected' : '' }}>{{ $va->roomname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!--時段-->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">時段</label>
                            <?php   $time = (isset($data['time']))?$data['time'] : 'A';
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
                                        default:
                                            break;
                                      }
                            ?>
                            <div class="col-md-10 mt-1">
                                <input type="radio" name="time" value="A" id="period1"  {{$select_A}}>上午
                                <input type="radio" name="time" value="B" id="period2"  {{$select_B}}>下午
                                <input type="radio" name="time" value="C" id="period3"  {{$select_C}}>晚上
                                <input type="radio" name="time" value="D" id="period4"  {{$select_D}}>白天(上午、下午)
                                <input type="radio" name="time" value="E" id="period5"  {{$select_E}}>全天(上午、下午、晚上)
                            </div>
                        </div>
                        <!-- 備註 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="remark" id="remark" maxlength="255">{{ old('remark', (isset($data['remark']))? $data['remark'] : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 所需設備 -->
                        <!--div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">所需設備</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="equip" id="equip" maxlength="255">{{ old('equip', (isset($data['equip']))? $data['equip'] : '') }}</textarea>
                            </div>
                        </div-->



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="actionCreate()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))
                        <span onclick="$('#del_form').attr('action', '/admin/session/{{ $data['meet'].$data['serno'] }}');" data-toggle="modal" data-target="#del_modol" >
                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash text-white"></i>刪除</button>
                            </span>
                        </span>
                        @endif
                        <!-- <a href="/admin/session">
                            <button type="button" class="btn btn-sm btn-danger" ><i class="fa fa-reply"></i> 回上一頁</button>
                        </a> -->
                        <button type="button" class="btn btn-sm btn-danger" onclick="window.history.go(-1); return false;"><i class="fa fa-reply"></i> 回上一頁</button>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript">

    $(document).ready(function() {
        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });
        $("#edate").datepicker( {   
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });

        getbranch(1);
        getbranch(2);
        getbranch(3);
    });
    function no_reservation_area(number){
        var site = $("select[name=siteT"+number+"]").val();
        var no_reservation_area = ['101','103','201','202','203','204','205','C01','C02'];
        if(no_reservation_area.indexOf(site)!='-1'){
            alert('您選擇的是台北院區一二樓的場地，請自行與福華會館確認是否可預約該場地，謝謝。');
        }
    }

    function getbranch(number){
        var branch = $("select[name=branch"+number+"]").val();
        if(branch =='2'){   //南投
            $('#Nantoulist'+number).css('display','inline-flex'); 
            $('#Taipeilist'+number).css('display','none'); 
        }else if(branch =='1') { //台北
            $('#Nantoulist'+number).css('display','none'); 
            $('#Taipeilist'+number).css('display','inline-flex');
        }else{
            $('#Nantoulist'+number).css('display','none'); 
            $('#Taipeilist'+number).css('display','none'); 
        }
    }
    // 送出
    function actionCreate(){
        if($("input[name=sdate]").val()=='' || $("input[name=edate]").val()=='') {
            alert('日期不可為空!');
            return false;
        }else if($("input[name=name]").val()=='' ) {
            alert('會議名稱不可為空!');
            return false;
        }else if($("select[name=meet]").val()=='' ||$("input[name=number]").val()=='' ) {
            alert('會議代碼不可為空!');
            return false;
        }else{
            $("#form").submit();
        }
    }
    // 取得編號
    function getserno()
    {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                url: '/admin/session/getserno',
                data: { meet: $('#meet').val(),
                        number: $('#number').val()
                    },
                success: function(data){
                    $('#serno').val(data.msg);
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
    }

    </script>
    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection