@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期排程維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/site_schedule" class="text-info">洽借場地班期排程處理</a></li>
                        <li class="active">洽借場地班期排程維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/site_schedule/'.$data->class, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/site_schedule/', 'id'=>'form']) !!}
            @endif
            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期排程維護</h3>
                        </div>
                        <!-- 搜尋 -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 班號 -->
                                    @if ( isset($data) )
                                    <input type="hidden" id="class" name="class" value="{{$data->class}}">
                                    <input type="hidden" id="term" name="term" value="{{$data->term}}">
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right"><span class="text-danger">*</span>班號</label>
                                            <div class="col-md-2">
                                                <select class="browser-default custom-select" name="class" disabled>
                                                    <option value="{{ $data->class }}">{{ $data->class }}</option>
                                                </select>
                                                <!-- <input type="text" class="form-control input"  placeholder="請輸入班號" value="{{ $data->class }}" disabled> -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 班別 -->
                                    <div class="form-group row">
                                        <label class="col-md-3 col-form-label text-md-right"><span class="text-danger">*</span>班別名稱</label>
                                        <div class="col-md-7">
                                            <select class="browser-default custom-select" name="name" disabled>
                                                <option value="{{ $data->class }}" selected >{{  $data->name  }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    @else
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right"><span class="text-danger">*</span>班別</label>
                                            <div class="col-md-7">
                                                <select class="select2" name="class" onchange="changeclass()">
                                                    @foreach($classlist as $key => $va)
                                                        <option value="{{ $va['class'] }}">{{ $va['class'].'_'. $va['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <!-- 期別 -->
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right"><span class="text-danger">*</span>期別</label>
                                            <div class="col-md-2">
                                                <input type="text" id="term" name="term" class="form-control input"  placeholder="請輸入期別" value="{{ old('term', (isset($data->term))? $data->term : '') }}" {{ isset($data)? 'disabled' : 'required' }}>
                                            </div>
                                            <label class="col-form-label text-md"><span class="text-danger">*</span>人數</label>
                                            <div class="col-md-2">
                                                <input type="text" id="quota" name="quota" class="form-control input"  placeholder="請輸入人數" value="{{ old('quota', (isset($data->quota))? $data->quota : '0') }}"  {{ isset($data)? 'disabled' : 'required' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 開課日期 - 結束日期 -->
                                    <!-- 開訓日期範圍 -->
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right"><span class="text-danger">*</span>開課日期</label>
                                            <div class="col-md-2">
                                                <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off"  placeholder="請輸入開課日期" value="{{ old('sdate', (isset($data->sdate))? $data->sdate : '') }}" onchange="getedate()" required>
                                            </div>
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                            <label class="col-form-label text-md">結束日期</label>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" autocomplete="off" id="edate" value="{{ isset($data->edate)? $data->edate : '系統自動運算' }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 教室 -->
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right">主教室</label>
                                            <!-- 院區 -->
                                            <div class="col-md-2">
                                                <select class="browser-default custom-select" name="site_branch" onchange="getbranch()">
                                                    <option value="">未定</option>
                                                    @foreach(config('app.branch') as $key => $va)
                                                        <option value="{{ $key }}" {{ old('site_branch', (isset($data->site_branch))?  $data->site_branch:'') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                        <option value="3" {{ old('site_branch', (isset($data->site_branch))?  $data->site_branch:'') == '3'? 'selected' : '' }}>外地上課</option>
                                                </select>
                                            </select>
                                            </div>
                                            <!-- 教室 -->
                                            <div class="col-md-5" style="display: inline-flex;"id ="Taipeilist">
                                                <select id="site" name="siteT" class="form-control select2" onchange="no_reservation_area()">
                                                        <option value="" >請選擇</option>
                                                    @foreach($Taipeilist as $va)
                                                        <option value="{{ $va->site }}" {{ old('stime', (isset($data->site))? $data->site : 1) == $va->site? 'selected' : '' }}>{{ $va->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5" style="display: none;" id ="Nantoulist" >
                                                <select id="site" name="siteN" class="form-control select2">
                                                        <option value="" >請選擇</option>
                                                    @foreach($Nantoulist as $va)
                                                        <option value="{{ $va->roomno }}" {{ old('stime', (isset($data->site))? $data->site : 0) == $va->roomno? 'selected' : '' }}>{{ $va->roomname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5" style="display: none;" id ="otherlocation" >
                                                <input type="text" class="form-control input-max" id="location" name="location" placeholder="外地上課地點" value="{{ old('location', (isset($data->location))? $data->location : '') }}" autocomplete="off" maxlength="255">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 辦班人員 -->
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right">辦班人員</label>
                                            <div class="col-md-2">
                                                <?php $list = $base->getSponsor(); ?>
                                                <select id="sponsor" name="sponsor" class="form-control select2" onchange="getsection()">
                                                    <option value="">請選擇</option>
                                                    @foreach($list as $key => $va)
                                                        <option value="{{ $key }}" {{ old('sponsor', (isset($data->sponsor))? $data->sponsor : '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 部門 -->
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right">部門</label>
                                            <div class="col-md-2">
                                                <select id="section" name="section" class="form-control select2">
                                                    <option value="">請選擇</option>
                                                    @foreach($section as $key => $va)
                                                        <option value="{{ $va['section'] }}" {{ old('section', (isset($data->section))? $data->section: '') == $va['section']? 'selected' : '' }}>{{ $va['section'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div id='loading' style="display: none">正在載入資料...</div>
                                        </div>
                                    </div>
                                    <!-- 費用 -->
                                    <div class="form-group row">
                                        <div class="input-group">
                                            <label class="col-md-3 col-form-label text-md-right">費用</label>
                                            <div class="col-md-7">
                                                <input type="text" id="fee" name="fee" class="form-control input"  value="{{ isset($data->fee)? $data->fee : '0' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 預約教室時段 -->
                                    <fieldset style="border:groove;margin:50;padding: 20">
                                        <legend>預約教室時段</legend>
                                        <div class="form-group row">
                                            <div class="col-md-10">
                                                @foreach(config('app.time') as $key => $va)
                                                    <input type="radio" name="time" value="{{ $key }}" {{ old('time', (isset($data->time))? $data->time: 'D') == $key? 'Checked' : '' }}>{{ $va }}
                                                @endforeach
                                            </div>
                                        </div>
                                    </fieldset>
                                    <!-- 師資陣容 -->
                                    <!-- <fieldset style="border:groove;margin:50;padding: 20">
                                        <legend>師資陣容</legend>
                                        <div class="form-group row">
                                            <div class="col-md-7">
                                                @foreach(config('app.lineup') as $key => $va)
                                                    <input type="radio" name="lineup" value="{{ $key }}" {{ old('lineup', (isset($data->lineup))? $data->lineup: '') == $key? 'Checked' : '' }}>{{ $va }}
                                                @endforeach
                                            </div>
                                        </div>
                                    </fieldset> -->


                                    <div align="center">
                                        <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                                        @if(isset($data))
                                            <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>                         
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal" onclick="history.go(-1)"><i class="fa fa-reply pr-2"></i>回上一頁</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @if(isset($data))
        {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/site_schedule/'.$data->class.$data->term, 'id'=>'deleteform']) !!}
            <!-- <button onclick="return confirm('確定要刪除嗎?')" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button> -->
        {!! Form::close() !!}                            
    @endif
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
    function deleteClass(){
        if(confirm('確定要刪除嗎?')){
            $("#deleteform").submit();
        }
    }
    function changeclass(){
        $("#term").val(''); 
        $("#sdate").val(''); 
        $("#edate").val(''); 
    }
    function getsection(){
        var Sponsor = $('#sponsor').val();
        if(Sponsor==''){
            return false;
        }
        $('#loading').show();
           $.ajax({
            url:'/admin/site_schedule/getsection',
            type:'get',
            data:{Sponsor:Sponsor},
            dataType:'json',
            success:function(data){
                console.log(data);
                $('#loading').hide();
                if(data.status == '0'){
                   // $("#section").val(data.msg);
                   $("select[name=section]").val(data.msg).trigger("change"); 
                   
                   return false; 
                   
                }else{
                    alert(data.msg);
                    $("#section").val(''); 
                    return false; 
                }
            }
        });
    }
    function getbranch(){
        var branch = $("select[name=site_branch]").val();
        if(branch =='2'){
            $('#Nantoulist').css('display','inline-flex'); 
            $('#Taipeilist').css('display','none'); 
            $('#otherlocation').css('display','none'); 
            $('.location').val(''); 
        }else if(branch =='1') {
            $('#Nantoulist').css('display','none'); 
            $('#Taipeilist').css('display','inline-flex');
            $('#otherlocation').css('display','none');  
            $('#location').removeAttr('value'); 
        }else if(branch =='3') {
            $('#Nantoulist').css('display','none'); 
            $('#Taipeilist').css('display','none'); 
            $('#otherlocation').css('display','inline-flex'); 
        }else{
            $('#Nantoulist').css('display','none'); 
            $('#Taipeilist').css('display','none'); 
            $('#otherlocation').css('display','none'); 
        }
    }
$(document).ready(function(){
    getbranch();
});

function getedate(){
    var sdate = $("#sdate").val();
    var _class = $("select[name=class]").val();
    console.log(_class);
    $.ajax({
        url:'/admin/site_schedule/getedate',
        type:'get',
        data:{_class:_class,sdate:sdate},
        dataType:'json',
        success:function(data){
            console.log(data);
            if(data.status == '0'){
               $("#edate").val(data.msg); 
               return false; 
               
            }else{
                alert(data.msg);
                $("#sdate").val(''); 
                $("#edate").val(''); 
                return false; 
            }
        }
    });
}

function no_reservation_area(){
    var site = $("select[name=siteT]").val();
    var no_reservation_area = ['101','103','201','202','203','204','205','C01','C02'];
    if(no_reservation_area.indexOf(site)!='-1'){
        alert('您選擇的是台北院區一二樓的場地，請自行與福華會館確認是否可預約該場地，謝謝。');
    }
}

</script>

<script>
$(document).ready(function() {
            $("#sdate").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker1').click(function(){
                $("#sdate").focus();
            });
            $("#edate").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });

        
           
     });

</script>
