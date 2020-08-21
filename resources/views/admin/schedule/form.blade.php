

@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'schedule';?>
    <?php //$institutionList = $base->getDBList('M17tb', ['enrollorg', 'enrollname']);?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需訓練排程處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_distribution" class="text-info">訓練排程處理</a></li>
                        <li class="active">訓練排程處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <div class="col-md-10 offset-md-1 p-0">
        
                <div class="card">
                    <!-- form start -->
                    @if (isset($t04tb))
                        {!! Form::model($t04tb, [ 'method'=>'put', 'url'=>"/admin/schedule/{$t04tb->class}/{$t04tb->term}", 'id'=>'form']) !!}
                    @else
                        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/schedule/create', 'id'=>'form']) !!}
                    @endif                    
                    <div class="card-header"><h3 class="card-title">訓練排程處理表單</h3></div>
                    <div class="card-body pt-4">

                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班號</label>
                            <div class="col-md-8">
                                <select name="class" onchange="syncSection(this)" {{ isset($t04tb) ? 'disabled' : null }}>
                                    <option value="{{ old('class', isset($t04tb) ? $t04tb->class : '') }}">
                                    {{ old('class', isset($t04tb) ? $t04tb->class.' '.$t04tb->t01tb->name : '') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">期別</label>
                            <div class="col-md-1">
                                <input type="text" class="form-control input-max" name="term" autocomplete="off" value="{{ old('term', (isset($t04tb)) ? $t04tb->term : '') }}" onchange="getClassInfo()"
                                @if(isset($t04tb)) disabled @endif >
                            </div>
                            <label class="col-md-4 col-form-label text-md-right">人數</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control input-max" name="quota" value="{{ old('quota', (isset($t04tb)) ? $t04tb->quota : '') }}" 
                                    {{  (!empty($t03tb_sum_quota)) ?  'disabled' : ''  }}
                                >
                            </div>                            
                        </div>
                        <!-- 班號 -->
                        <div class="form-group row"  style="font-size: 16px;">
                            <label class="col-md-2 col-form-label text-md-right">
                                開課日期
                            </label>    
                            <div class="input-group col-md-3">                            
                                <input type="text" id="sdate" name="sdate" class="form-control input-max" autocomplete="off" value="{{ old('sdate', (isset($t04tb)) ? $t04tb->sdate : '') }}" onchange="computeEndDate()">
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                            </div>
                            <label class="col-md-2 col-form-label text-md-right">
                                結束日期
                            </label>
                            <div class="input-group col-md-3"> 
                                <input type="text" id="edate" name="edate" class="form-control input-max" autocomplete="off" value="{{ old('edate', (isset($t04tb)) ? $t04tb->edate : '') }}" readonly>
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>   
                        <div id="reservation_period" style="display:none">
                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"></label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="period" id="morning" value="morning">
                                    <label class="form-check-label" for="morning">早上</label>
                                </div>                     
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="period" id="afternoon" value="afternoon">
                                    <label class="form-check-label" for="afternoon">下午</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="period" id="evening" value="evening">
                                    <label class="form-check-label" for="evening">晚間</label>
                                </div>                                           
                            </div>
                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right" style="padding-bottom: 0px;padding-top: 0px;">預約教室時段</label>     
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="period" id="mor_aft" value="mor_aft">
                                    <label class="form-check-label" for="mor_aft">白天(早上、下午)</label>
                                </div>                                                         
                            </div>
                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right"></label> 
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="period" id="allday" value="allday">
                                    <label class="form-check-label" for="allday">全天(早上、下午、晚間)</label>
                                </div>                                                            
                            </div>                        
                        </div>
                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">主教室</label>
                            <div class="input-group col-2">
                                <select class="form-control select2" name="site_branch" onchange="changeClassRoom(this.value)" value="{{ (isset($t04tb)) ? $t04tb->site_branch : '' }}">
                                    <option value="">未定</option>
                                    @foreach(config('database_fields.m14tb')['branch'] as $key => $va)
                                        <option value="{{ $key }}" 
                                        @if($key == old('site_branch', (isset($t04tb)) ? $t04tb->site_branch : ''))
                                            selected
                                        @endif 
                                        >{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="site" class="input-group col-2" {{ (isset($t04tb) && $t04tb->site_branch != 3 && !empty($t04tb->site_branch)) ? '' : 'style=display:none' }}>
                                <select class="form-control select2" name="site" >
                                    <option value="">未定</option>
                                    @if (isset($t04tb))
                                        @if ($t04tb->site_branch == 1)
                                            @foreach($class_rooms['m14tb'] as $m14tb)
                                                <option value="{{ $m14tb['site'] }}"
                                            
                                                @if(old('site', (isset($t04tb)) ? $t04tb->site : null) === (string)$m14tb['site'])
                                                    selected                           
                                                @endif 

                                                >{{ $m14tb['name'] }}</option>
                                            @endforeach
                                        @else
                                            @foreach($class_rooms['m25tb'] as $m25tb)
                                                <option value="{{ $m25tb['site'] }}"
                                            
                                                @if(old('site', (isset($t04tb)) ? $t04tb->site : null) === (string)$m25tb['site'])
                                                    selected                           
                                                @endif 

                                                >{{ $m25tb['name'] }}</option>
                                            @endforeach
                                        @endif  
                                    @endif 

                                </select>
                            </div>  
                            
                           
                            <div id="location" class="input-group col-2" 
                            
                            @if(old('site_branch', (isset($t04tb)) ? $t04tb->site_branch : '') != 3 )
                                style="display:none"
                            @endif 
                            
                            >
                                <input type="text" class="form-control input-max" name="location" placeholder="請輸入其他上課地點" value="{{ old('location', isset($t04tb) ? $t04tb->location : null) }}">
                            </div>                                                      
                        </div>
                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">辦班人員</label>
                            <div class="input-group col-2">
                                <select class="form-control select2" name="sponsor" onchange="syncSection()">
                                    <option value="">未定</option>
                                    @foreach($sponsors as $sponsor)
                                        <option value="{{ $sponsor->userid }}" data-section="{{ $sponsor->section }}"
                                            @if($sponsor->userid === old('sponsor', (isset($t04tb)) ? $t04tb->sponsor : ''))
                                                selected
                                            @endif 
                                        >{{ $sponsor->username }}</option>
                                    @endforeach 
                                </select>
                            </div>                         
                        </div> 

                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">部門</label>
                            <div class="col-md-2">
                                <select class="form-control select2" name="section">
                                    <option value="">未定</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->section }}"
                                        @if($section->section == old('section', (isset($t04tb)) ? $t04tb->section : ''))
                                            selected
                                        @endif
                                        >{{ $section->section }}</option>
                                    @endforeach 
                                </select>
                            </div>                          
                        </div> 
                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">帶班輔導員(非必填)</label>
                            <div class="col-md-2">
                                {{ Form::select('counselor', collect(['' => '未定'])->merge($sponsors->pluck('username', 'userid')->toArray()), null, ['class' => 'form-control select2']) }}
                            </div>                          
                        </div> 

                    </div>
                    {!! Form::close() !!} 
                    @if(isset($t04tb))
                        {!! Form::open([ 'method'=>'delete', 'url'=>"/admin/schedule/{$t04tb->class}/{$t04tb->term}", "id" => 'deleteForm']) !!} 
                        {!! Form::close() !!}
                    @endif                                            
                    <div class="card-footer">
                        <button type="button"class="btn btn-sm btn-info" onclick="createSchedule()"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($t04tb))                    
                            <button type="submit" onclick="deleteSubmit()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>
                        @endif
                        <a href="/admin/schedule">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script type="text/javascript">
    
    var t01tb; 
    var oneTwoFloor = ['101', '103', '201', '202', '203', '204', '205', 'C01', 'C02'];
    var room = JSON.parse('{!! addslashes(json_encode($class_rooms)) !!}');

    $(document).ready(function() {

        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });

        // $("#edate").datepicker({
        //     format: "twymmdd",
        //     language: 'zh-TW'
        // });
        
        // $('#datepicker2').click(function(){
        //     $("#edate").focus();
        // });

        getClassInfo(false);

        $("select[name=class]").select2({
        language: 'zh-TW',
        width: '100%',
        // 最多字元限制
        maximumInputLength: 10,
        // 最少字元才觸發尋找, 0 不指定
        minimumInputLength: 0,
        // 當找不到可以使用輸入的文字
        // tags: true,
        placeholder: '請輸入名稱...',
        // AJAX 相關操作
        ajax: {
            url: '/admin/field/getData/t01tbs',
            type: 'get',
            // 要送出的資料
            data: function (params){
                console.log(params);
                // 在伺服器會得到一個 POST 'search' 
                return {
                    class_or_name: params.term,
                    page: params.page 
                };
            },
            processResults: function (data, params){

                // 一定要返回 results 物件
                return {
                    results: data,
                    // 可以啟用無線捲軸做分頁
                    pagination: {
                        more: true
                    }
                }
            }
        }
    });

    });
    
    function syncClass(element){
        // var e1,e2;
        // if (element.name == "class_name"){
        //     e1 = "class";
        //     e2 = "class_name";
        // }else if (element.name == "class"){
        //     e1 = "class_name";
        //     e2 = "class";
        // }
        // e2 = $("select[name=" + e2 + "]").val();

        // $("select[name=" + e1 + "]").attr("onchange", "");    
        // $("select[name=" + e1 + "]").val(e2).trigger("change");
        // $("select[name=" + e1 + "]").attr("onchange", "syncClass(this)"); 
        getClassInfo();
    }

    function getClassInfo(change_quota = true){
        var class_no = $("select[name=class]")[0].value;
        var term = $("input[name=term]")[0].value;
        term = padLeft(term, 2);

        if (class_no !== ""){
            if (change_quota == true){
                $("input[name=quota]")[0].value = "讀取中";
            }
            $.ajax({
                url: "/admin/schedule/getClassInfo/" + class_no + "/" + term
            }).done(function(response) {
                if (typeof response.sum_quota !== undefined){
                    t01tb = response.t01tb;
                    if (response.term !== "00"){
                        if (response.sum_quota == 0){
                            $("input[name=quota]").attr("disabled", null);
                        }else{
                            $("input[name=quota]").attr("disabled", "disabled");
                        }
                        if (change_quota == true){
                            $("input[name=quota]")[0].value = response.sum_quota;
                        }
                    }else{
                        if (change_quota == true){
                            $("input[name=quota]")[0].value = "";
                        }
                    }  
                    if (response.t01tb.style == 4){
                        $("#reservation_period").css("display", "block");
                    }else{
                        $("#reservation_period").css("display", "none");
                    }                        
                }
            });        
        }
    }

    function padLeft(str,lenght){
        if(str.length >= lenght)
        return str;
        else
        return padLeft("0" +str,lenght);
    }

    function changeClassRoom(site_branch){ 
        var siteSelectHtml = "";
        if (site_branch == 1){
            for(let i=0; i<room.m14tb.length; i++){
                siteSelectHtml += "<option value=\"" + room.m14tb[i].site + "\">" + room.m14tb[i].name +"</option>";
            }
            $("select[name=site]").html(siteSelectHtml).trigger('change')
            $("#location").css("display", "none");
        }else if (site_branch == 2){
            for(let i=0; i<room.m25tb.length; i++){
                siteSelectHtml += "<option value=\"" + room.m25tb[i].site + "\">" + room.m25tb[i].name +"</option>";
            }           
            $("select[name=site]").html(siteSelectHtml).trigger('change')
            $("#location").css("display", "none");
        }else if (site_branch == 3){
            $("select[name=site]").html("").trigger('change')
            $("#location").css("display", "flex");
        }else{
            $("select[name=site]").html("").trigger('change')
            $("#location").css("display", "none");
        }

        if (site_branch != 3 && site_branch != ''){
            $("#site").css("display", "flex");
        }else{
            $("#site").css("display", "none");
        }
        
        console.log(siteSelectHtml);

        // $("select[name='site[1]']").val("").trigger("change");
        // $("select[name='site[2]']").val("").trigger("change");
        // if (site_branch == 1){
        //     $("#site1").css("display", "flex");
        //     $("#site2").css("display", "none");
        //     $("#location").css("display", "none");
        // }else if (site_branch == 2){
        //     $("#site1").css("display", "none");
        //     $("#site2").css("display", "flex");
        //     $("#location").css("display", "none");
        // }else if (site_branch == 3){
        //     $("#site1").css("display", "none");    
        //     $("#site2").css("display", "none");
        //     $("#location").css("display", "flex");
        // }
    }

    function computeEndDate(){
        var start_date = $("input[name=sdate]")[0].value;
        if (typeof t01tb != "undefined" && start_date !== ""){
            var allow_day = getAllowDay(t01tb.style);
            console.log(allow_day);
            if (start_date.length == 7){
                var year = parseInt(start_date.substr(0,3)) + 1911;
                var month = parseInt(start_date.substr(3,2)) - 1;
                var day = start_date.substr(5,2);
                var class_day = parseInt(t01tb.day);
                start_date = new Date(year, month, day);

                while (class_day > 0){
                    var week = start_date.getDay();
                     
                    if (allow_day[week]){
                        class_day--;
                    }

                    if (class_day > 0){
                        start_date.setDate(start_date.getDate() + 1);
                    }

                }
                var end_date = (parseInt(start_date.getFullYear())-1911).toString() + 
                    padLeft((parseInt(start_date.getMonth())+1).toString(), 2) +
                    padLeft((parseInt(start_date.getDate())).toString(), 2)  ;
                    
                $("input[name=edate]")[0].value = end_date;
                console.log(start_date);
            }
        }else{
            console.log("t01tb is empty");
        }
    }

    function getAllowDay(style){
        var styles = [];
        var class_no = $("select[name=class]")[0].value;

        styles[1] = [false, true, true, true, true, true, false],
        styles[2] = [false, true, false, true, false, true, false],
        styles[3] = [false, false, true, false, true, false, false]                                                          

        if (typeof t01tb != "undefined"){
            if (class_no == t01tb.class){
                styles[4] = [
                    (t01tb.time7 == "Y"),
                    (t01tb.time1 == "Y"),
                    (t01tb.time2 == "Y"),
                    (t01tb.time3 == "Y"),
                    (t01tb.time4 == "Y"),
                    (t01tb.time5 == "Y"),
                    (t01tb.time6 == "Y"),
                ];               
            }
            if (t01tb.holiday == "Y"){
                styles[1][0] = "N";
                styles[1][6] = "N";
                styles[2][0] = "N";
                styles[2][6] = "N";
                styles[3][0] = "N";
                styles[3][6] = "N";
                styles[4][0] = "N";
                styles[4][6] = "N"; 
            }
        }else{
            console.log("t01tb is empty");
        }

        return styles[style];
    }
    function syncSection(){
        var section = $("select[name=sponsor]").find("option:selected")[0].dataset.section;
        $("select[name=section]").val(section).trigger("change");
    }
    
    function deleteSubmit()
    {
        if (confirm('確定要刪除嗎？')){
            $("#deleteForm").submit();
        }
    }

    function createSchedule()
    {
        if (oneTwoFloor.indexOf($("select[name=site]").val()) !== -1){
            alert("您選擇的是台北院區一二樓的場地，請自行與福華會館確認是否可預約該場地，謝謝。");
        }
        $("#form").submit();
    }

</script>
@endsection