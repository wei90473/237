@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')


    <?php $_menu = 'entryexport';?>
    <style>
        .custom-select {
            display: inline-block;
            width: 5%;
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
        .search-float input {
            min-width:1px;
        }
        .btndiv {
            padding:5px;
        }
        .paginationjs {
            line-height:1.6;
            font-family:Marmelad,"Lucida Grande",Arial,"Hiragino Sans GB",Georgia,sans-serif;font-size:14px;box-sizing:initial
        }
        .paginationjs:after{
            display:table;content:" ";clear:both
        }
        .paginationjs .paginationjs-pages{
            float:left
        }
        .paginationjs .paginationjs-pages ul{
            float:left;margin:0;padding:0
        }
        .paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input,.paginationjs .paginationjs-nav{
            float:left;margin-left:10px;font-size:14px
        }
        .paginationjs .paginationjs-pages li{
            float:left;border:1px solid #aaa;border-right:none;list-style:none
        }
        .paginationjs .paginationjs-pages li>a{
            min-width:30px;height:28px;line-height:28px;display:block;background:#fff;font-size:14px;color:#333;text-decoration:none;text-align:center}.paginationjs .paginationjs-pages li>a:hover{background:#eee}.paginationjs .paginationjs-pages li.active{border:none}.paginationjs .paginationjs-pages li.active>a{height:30px;line-height:30px;background:#aaa;color:#fff}.paginationjs .paginationjs-pages li.disabled>a{opacity:.3}.paginationjs .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs .paginationjs-pages li:first-child,.paginationjs .paginationjs-pages li:first-child>a{border-radius:3px 0 0 3px}.paginationjs .paginationjs-pages li:last-child{border-right:1px solid #aaa;border-radius:0 3px 3px 0}.paginationjs .paginationjs-pages li:last-child>a{border-radius:0 3px 3px 0}.paginationjs .paginationjs-go-input>input[type=text]{width:30px;height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;padding:0;font-size:14px;text-align:center;vertical-align:baseline;outline:0;box-shadow:none;box-sizing:initial}.paginationjs .paginationjs-go-button>input[type=button]{min-width:40px;height:30px;line-height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;text-align:center;padding:0 8px;font-size:14px;vertical-align:baseline;outline:0;box-shadow:none;color:#333;cursor:pointer;vertical-align:middle\9}.paginationjs.paginationjs-theme-blue .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-blue .paginationjs-pages li{border-color:#289de9}.paginationjs .paginationjs-go-button>input[type=button]:hover{background-color:#f8f8f8}.paginationjs .paginationjs-nav{height:30px;line-height:30px}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input{margin-left:5px\9}.paginationjs.paginationjs-small{font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li>a{min-width:26px;height:24px;line-height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li.active>a{height:26px;line-height:26px}.paginationjs.paginationjs-small .paginationjs-go-input{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-input>input[type=text]{width:26px;height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button>input[type=button]{min-width:30px;height:26px;line-height:24px;padding:0 6px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-nav{height:26px;line-height:26px;font-size:12px}.paginationjs.paginationjs-big{font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li>a{min-width:36px;height:34px;line-height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li.active>a{height:36px;line-height:36px}.paginationjs.paginationjs-big .paginationjs-go-input{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{width:36px;height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button>input[type=button]{min-width:50px;height:36px;line-height:34px;padding:0 12px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-nav{height:36px;line-height:36px;font-size:16px}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a{color:#289de9}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a:hover{background:#e9f4fc}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.active>a{background:#289de9;color:#fff}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]{background:#289de9;border-color:#289de9;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-green .paginationjs-pages li{border-color:#449d44}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]:hover{background-color:#3ca5ea}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a{color:#449d44}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a:hover{background:#ebf4eb}.paginationjs.paginationjs-theme-green .paginationjs-pages li.active>a{background:#449d44;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]{background:#449d44;border-color:#449d44;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-yellow .paginationjs-pages li{border-color:#ec971f}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]:hover{background-color:#55a555}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a{color:#ec971f}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a:hover{background:#fdf5e9}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.active>a{background:#ec971f;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]{background:#ec971f;border-color:#ec971f;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-red .paginationjs-pages li{border-color:#c9302c}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]:hover{background-color:#eea135}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a{color:#c9302c}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a:hover{background:#faeaea}.paginationjs.paginationjs-theme-red .paginationjs-pages li.active>a{background:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]{background:#c9302c;border-color:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]:hover{background-color:#ce4541}.paginationjs .paginationjs-pages li.paginationjs-next{border-right:1px solid #aaa\9}.paginationjs .paginationjs-go-input>input[type=text]{line-height:28px\9;vertical-align:middle\9}.paginationjs.paginationjs-big .paginationjs-pages li>a{line-height:36px\9}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{height:36px\9;line-height:36px\9}

    </style>
    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">入口網站資料匯出</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">入口網站資料匯出</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>入口網站資料匯出</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" onclick="btcolor(this);" style="font-size:16px;" class="btn" id="one">班別資料匯出</button>
                                    <button type="submit" onclick="btcolor(this);" style="font-size:16px;" class="btn" id="two">認證時數資料匯出</button>
                                    <button type="submit" onclick="btcolor(this);" style="font-size:16px;" class="btn" id="three">匯出紀錄查詢</button>

                                    <!--班別資料匯出-->
                                    <form id="form_one" style="display:none;margin-top:2%" action="" method="post" target="_blank">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <label>上課期間:</label>
                                        <select class="custom-select" id="sdate_year">
                                            <option>111</option>
                                            <option>110</option>
                                            <option selected>109</option>
                                            <option>108</option>
                                            <option>107</option>
                                            <option>106</option>
                                            <option>105</option>
                                            <option>104</option>
                                            <option>103</option>
                                            <option>102</option>
                                            <option>101</option>
                                        </select>年
                                        <select class="custom-select" id="sdate_month">
                                            <?php for($i=1;$i<=12;$i++){?>
                                            <option>{{$i}}</option>
                                            <?php }?>
                                        </select>月
                                        ～
                                        <select class="custom-select" id="edate_year">
                                            <option>111</option>
                                            <option>110</option>
                                            <option selected>109</option>
                                            <option>108</option>
                                            <option>107</option>
                                            <option>106</option>
                                            <option>105</option>
                                            <option>104</option>
                                            <option>103</option>
                                            <option>102</option>
                                            <option>101</option>
                                        </select>年
                                        <select class="custom-select" id="edate_month">
                                            <?php for($i=1;$i<=12;$i++){?>
                                            <option>{{$i}}</option>
                                            <?php }?>
                                        </select>月
                                        <input type="hidden" id="final_sdate" name="final_sdate">
                                        <input type="hidden" id="final_edate" name="final_edate">
                                        <input type="hidden" id="final_course" name="final_course">
                                        <button type="button" onclick="selectclass();" class="btn" style="background-color:#317eeb;color:white;">挑選班期</button>

                                        <div class="col-xs-12">
                                            <span style="margin-right:1%"><input type="checkbox" id="class" name="class" value="class">班別</span>
                                            <span style="margin-right:1%"><input type="checkbox" id="teacher" name="teacher" value="teacher">講座</span>
                                            <span style="margin-right:1%"><input type="checkbox" id="course" name="course" value="course">班級課程</span>
                                        </div>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-light" onclick="print();">資料匯出</button>
                                        </div>
                                    </form>

                                    <!--認證時數資料匯出-->
                                    <form id="form_two" style="display:none;margin-top:2%" action="" method="post" target="_blank">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <div class="input-group col-6">
                                            <label class="mt-1 mr-2">結業期間:</label>
                                            <input type="text" id="sdate3" name="sdate3" class="form-control" autocomplete="off">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                            <span class="mt-1 ml-2 mr-2">到</span>
                                            <input type="text" id="edate3" name="edate3" class="form-control" autocomplete="off">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>

                                            <label class="mt-1 ml-2 mr-2">班務人員:</label>
                                            <div class="input-group col-3">
                                                <select class="form-control select2" id="sponsor" name="sponsor">
                                                <?php for($i=0;$i<count($user);$i++){?>
                                                <?php foreach($user[$i] as $key=>$row){?>
                                                <option value="{{$key}}" {{ auth()->user()->userid == $key? 'selected' : '' }} >{{$row}}</option>
                                                <?php }?>
                                                <?php }?>
                                                </select>
                                            </div>
                                        </div>

                                        <input type="hidden" id="final_sdate2" name="final_sdate2">
                                        <input type="hidden" id="final_edate2" name="final_edate2">
                                        <input type="hidden" id="class2" name="class2">
                                        <input type="hidden" id="term2" name="term2">

                                        <div class="col-xs-12 mt-3">
                                            <button class="btn btn-light mobile-100 mb-3 mb-md-0" type="button" id="search1"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                            <button  class="btn btn-light mobile-100 mb-3 mb-md-0" type="button" onclick="clear_condition();">重設條件</button>
                                            <button class="btn" type="button" value="print2" style="background-color:#317eeb;color:white;" onclick="selectActionForm3(this);" >列印</button>

                                        </div>

                                        <div class="col-xs-12 mt-3">
                                            <label>匯出選項:</label>
                                            <input type="checkbox" value="score" name="score" id="score">含成績資料
                                            <input type="checkbox" value="sickkk" name="sickkk" id="sickkk">含請假資料
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="table_two" style="display:none;margin-top:2%">
                                            <thead>
                                            <tr>
                                                <th class="text-center">班別</th>
                                                <th class="text-center">期別</th>
                                                <th class="text-center">已開班/未開班</th>
                                                <th class="text-center">結束日期</th>
                                                <th class="text-center">是否已匯出</th>
                                                <th class="text-center">匯出日期</th>
                                                <th class="text-center">匯出</th>
                                            </tr>

                                            </thead>
                                            <tbody id="tbody2">
                                                <?php if($lock_class!=''&&$data!=''){?>
                                                    <tr class="text-center">
                                                        <td>{{$data[0]['name']}}({{$lock_class['class']}})</td>
                                                        <td>{{$lock_class['term']}}</td>
                                                        <td>
                                                            <?php if($data[0]['upload1'] == 'Y'){ ?>
                                                            已開班
                                                            <?php }else{ ?>
                                                            未開班
                                                            <?php } ?>
                                                        </td>
                                                        <td>{{$lock_class['edate']}}</td>
                                                        <?php $check='否'; if($data[0]['file5']!=''){$check='是';}?>
                                                        <td>{{$check}}</td>
                                                        <td>{{$data[0]['file5']}}</td>
                                                        <td><button class="btn btn-info" type="button" onclick="export_csv(this);" value="{{$data[0]['class']}}_{{$data[0]['term']}}_{{$data[0]['upload1']}}">匯出時數資料</button></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!--匯出紀錄查詢-->
                                    <form id="form_three" style="display:none;margin-top:2%" action="" method="post" target="_blank">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <label>上課期間:</label>
                                        <select class="custom-select" id="sdate_year3" >
                                            <option>111</option>
                                            <option>110</option>
                                            <option selected>109</option>
                                            <option>108</option>
                                            <option>107</option>
                                            <option>106</option>
                                            <option>105</option>
                                            <option>104</option>
                                            <option>103</option>
                                            <option>102</option>
                                            <option>101</option>
                                        </select>年
                                        <select class="custom-select" id="sdate_month3">
                                            <?php for($i=1;$i<=12;$i++){?>
                                            <option>{{$i}}</option>
                                            <?php }?>
                                        </select>月
                                        ～
                                        <select class="custom-select" id="edate_year3">
                                            <option>111</option>
                                            <option>110</option>
                                            <option selected>109</option>
                                            <option>108</option>
                                            <option>107</option>
                                            <option>106</option>
                                            <option>105</option>
                                            <option>104</option>
                                            <option>103</option>
                                            <option>102</option>
                                            <option>101</option>
                                        </select>年
                                        <select class="custom-select" id="edate_month3">
                                            <?php for($i=1;$i<=12;$i++){?>
                                            <option>{{$i}}</option>
                                            <?php }?>
                                        </select>月
                                        <input type="hidden" id="final_sdate3" name="final_sdate">
                                        <input type="hidden" id="final_edate3" name="final_edate">
                                        <div class="col-xs-12">
                                            <button class="btn btn-light mobile-100 mb-3 mb-md-0" type="button" id="search2" value="search" onclick="selectActionForm3(this);"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>

                                            <button class="btn" style="background-color:#317eeb;color:white;" type="button" value="print" onclick="selectActionForm3(this);">列印</button>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" id="table_three" style="display:none;margin-top:2%">
                                            <thead>
                                            <tr>
                                                <th class="text-center" rowspan="2">班別</th>
                                                <th class="text-center" rowspan="2">期別</th>
                                                <th class="text-center" rowspan="2">開始日期</th>
                                                <th class="text-center" rowspan="2">結束日期</th>
                                                <th class="text-center" rowspan="2">課程表公告</th>
                                                <th class="text-center" colspan="2">入口網站開班方式</th>
                                                <th class="text-center "colspan="5">轉檔日期</th>
                                            </tr>
                                            <tr>
                                                <th>已開班</th>
                                                <th>未開班</th>
                                                <th>班別</th>
                                                <th>講座</th>
                                                <th>課程</th>
                                                <th>班別/課程/講師</th>
                                                <th>成績</th>
                                            </tr>
                                            </thead>
                                            <tbody id="tbody1">

                                            </tbody>
                                        </table>
                                    </div>

                                    <div id="p1" style="margin-top:1%">
                                        <section>
                                        	<div id="total"></div>
                                            <div id="pagination"></div>
                                        </section>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>

@endsection
<script type="text/javascript">
    //匯出記錄查詢 搜尋ajax
    $(document).ready(function()
    {

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

        $("#search2").click(function() { //ID 為 search2 的按鈕被點擊時
            message=checkdate(3);
            if(message){
                _ajax(2);
            }else{
                alert("時間輸入有誤");
            }
        });
        var lock_class_date="<?=$lock_class['sdate']?>";
        if(lock_class_date!=''){
            var lock_sdate="<?=$lock_class['sdate'];?>";
            var lock_edate="<?=$lock_class['edate'];?>";
            //console.log(test1);
            //console.log(<?=$lock_class['edate'];?>);
            $("#final_sdate2").val(lock_sdate);
            $("#final_edate2").val(lock_edate);
        }

        $("#search1").click(function() { //ID 為 search1 的按鈕被點擊時
            var sdate3=$("#sdate3").val();
            var edate3=$("#edate3").val();
            var find = '/';
            var re = new RegExp(find, 'g');
            sdate3=sdate3.replace(re,"");
            edate3=edate3.replace(re,"");
            $("#final_sdate2").val(sdate3);
            $("#final_edate2").val(edate3);
            message=checkdate(2);
            if(message){
                _ajax(1);
            }else{
                alert("時間輸入有誤");
            }
        });
    });

    function _ajax(type)
    {
        if(type==1){//認證時數資料匯出
            var url="/admin/entryexport/search/1";
            var final_sdate = $("#final_sdate2").val();
            var final_edate = $("#final_edate2").val();
            var sponsor = $("#sponsor").val();
        }

        if(type==2){//匯出記錄查詢
            var url="/admin/entryexport/search/2";
            var final_sdate = $("#final_sdate3").val();
            var final_edate = $("#final_edate3").val();
            var sponsor = $("#sponsor").val();
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST", //傳送方式
            url: url, //傳送目的地
            dataType: "json", //資料格式
            data:{ //傳送資料
                    final_sdate: final_sdate,
                    final_edate: final_edate,
                    sponsor: sponsor
                 },

            beforeSend: function() {
                if(type==1){
                    var test="<tr><td class='text-center'  rowspan='2' colspan='6'>搜尋中......</td><tr>";
                    $("#tbody2").html(test);
                }else{
                    var test="<tr><td class='text-center'  rowspan='2' colspan='12'>搜尋中......</td><tr>";
                    $("#tbody1").html(test);
                }

            },

            success: function(data) {
                if(type==1){
                    var tableData='';
                    if(data.length==0){
                        tableData+='<tr><td class="text-center" colspan="6">查無資料</td></tr>';
                    }else{
                        for(var i=0;i<data.length;i++){
                            tableData+='<tr class="text-center">';
                            tableData+='<td>'+data[i].name+'('+data[i].class+')'+'</td>';
                            tableData+='<td>'+data[i].term+'</td>';
                            if(data[i].upload1 == 'Y'){
                                tableData+='<td>已開班</td>';
                            }else{
                                tableData+='<td>未開班</td>';
                            }
                            var edate = data[i].edate.substr(0,3)+'/'+data[i].edate.substr(3,2)+'/'+data[i].edate.substr(5,2);
                            tableData+='<td>'+edate+'</td>';
                            var file5='';
                            var check='否';
                            if(data[i].file5){
                                file5 = data[i].file5.substr(0,3)+'/'+data[i].file5.substr(3,2)+'/'+data[i].file5.substr(5,2);
                                check='是';
                            }
                            tableData+="<td>"+check+"</td>";

                            tableData+='<td>'+file5+'</td>';
                            tableData+='<td><button class="btn btn-info" type="button" onclick="export_csv(this);" value="'+data[i].class+'_' +data[i].term+ '_' +data[i].upload1+ '">匯出時數資料</button></td>'
                            tableData+='</tr>';
                        }
                    }
                    $("#tbody2").html(tableData);
                }

                if(type==2){
                    var tableData='';
                    paginate(data,data.length);
                }

            },
            error: function(data) {
                console.log('error');
            }

        });
    }

    //分頁
    function paginate(data, total)
    {
        var container = $('#pagination');
        container.pagination({
            dataSource: data,
            //locator: 'data',
            totalNumber: total,
            pageSize: 10,
            showpages: true,
            showPrevious: true,
            showNext: true,
            showNavigator: true,
            showFirstOnEllipsisShow: true,
            showLastOnEllipsisShow: true,
            callback: function(data, pagination) {
                var tableData='';
                if(data.length==0){
                    tableData="<tr><td class='text-center'  rowspan='2' colspan='12'>查無資料</td><tr>";
                }else{
                    for(var i=0;i<data.length;i++){
                        tableData+='<tr class="text-center">';
                        tableData+='<td>'+data[i].name+'('+data[i].class+')'+'</td>';
                        tableData+='<td>'+data[i].term+'</td>';
                        var sdate = data[i].sdate.substr(0,3)+'/'+data[i].sdate.substr(3,2)+'/'+data[i].sdate.substr(5,2);
                        var edate = data[i].edate.substr(0,3)+'/'+data[i].edate.substr(3,2)+'/'+data[i].edate.substr(5,2);
                        tableData+='<td>'+sdate+'</td>';
                        tableData+='<td>'+edate+'</td>';
                        if(data[i].publish2=='Y'){
                            pub='是';
                        }else{
                            pub='否';
                        }
                        tableData+='<td>'+pub+'</td>';
                        var check='';
                        var check2='';
                        if(data[i].upload1=='Y'){
                            check='O';
                        }else{
                            check2='O';
                        }
                        tableData+='<td>'+check+'</td>';
                        tableData+='<td>'+check2+'</td>';
                        var file1='';
                        var file2='';
                        var file3='';
                        var file5='';
                        if(data[i].file1){
                            file1 = data[i].file1.substr(0,3)+'/'+data[i].file1.substr(3,2)+'/'+data[i].file1.substr(5,2);
                        }
                        tableData+='<td>'+file1+'</td>';
                        if(data[i].file2){
                            file2 = data[i].file2.substr(0,3)+'/'+data[i].file2.substr(3,2)+'/'+data[i].file2.substr(5,2);
                        }
                        tableData+='<td>'+file2+'</td>';
                        if(data[i].file3){
                            file3 = data[i].file3.substr(0,3)+'/'+data[i].file3.substr(3,2)+'/'+data[i].file3.substr(5,2);
                        }
                        tableData+='<td>'+file3+'</td>';
                        tableData+='<td></td>';
                        if(data[i].file5){
                            file5 = data[i].file5.substr(0,3)+'/'+data[i].file5.substr(3,2)+'/'+data[i].file5.substr(5,2);
                        }
                        tableData+='<td>'+file5+'</td>';
                        tableData+='</tr>';
                    }
                }

                $("#total").html('共有'+total+'筆資料');
                $("#tbody1").html(tableData);
            }
        });
    }



    //檢查輸入時間
    function checkdate(type)
    {
        if(type==1){
            var sdate=$("#final_sdate").val();
            var edate=$("#final_edate").val();

            if(sdate>edate || sdate=='' ||edate==''){
                return false;
            }else{
                return true;
            }
        }
        if(type==2){
            var sdate=$("#sdate3").val();
            var edate=$("#edate3").val();

            if(sdate>edate || sdate=='' ||edate==''){
                return false;
            }else{
                return true;
            }
        }

        if(type==3){
            var sdate = $("#final_sdate3").val();
            var edate = $("#final_edate3").val();
            if(sdate>edate || sdate=='' ||edate==''){
                return false;
            }else{
                return true;
            }
        }

    }

    //認證時數清除
    function clear_condition()
    {
        document.all.sdate3.value = " ";
        document.all.edate3.value = " ";
        document.all.final_sdate2.value = " ";
        document.all.final_edate2.value = " ";
        //alert('why');
    }

    //認證時數資料匯出
    function export_csv(obj)
    {
        var temp=obj.value;
        var result=temp.split("_");
        $("#class2").val(result[0]);
        $("#term2").val(result[1]);
        if(result[2]=='Y'){
            $("#form_two").attr("action","/admin/entryexport/export/6");
            $("#form_two").submit();
        }else{
            $("#form_two").attr("action","/admin/entryexport/export/7");
            $("#form_two").submit();
        }
        _ajax(1);
    }

    //匯出記錄查詢
    function selectActionForm3(obj)
    {
        var act=obj.value;
        var s_y3=$('#sdate_year3').val();
        var s_m3=$('#sdate_month3').val();
        if(s_m3.length<2){
            s_m3='0'+s_m3;
        }
        var e_y3=$('#edate_year3').val();
        var e_m3=$('#edate_month3').val();
        if(e_m3.length<2){
            e_m3='0'+e_m3;
        }
        var s_y_23=Number(s_y3)+1911;
        var e_y_23=Number(e_y3)+1911;
        var lastDay_s3= '01';
        //var day_s = lastDay_s.getDate();
        var lastDay_e3= new Date(e_y_23,e_m3,0);
        var day_e3 = lastDay_e3.getDate();
        var final_sdate3=s_y3+s_m3+lastDay_s3;
        var final_edate3=e_y3+e_m3+day_e3;
        $("#final_sdate3").val(final_sdate3);
        $("#final_edate3").val(final_edate3);

        if(act=='print'){
            message=checkdate(3);
            if(message){
                $("#form_three").attr("action","/admin/entryexport/export/4");
                $("#form_three").submit();
            }else{
                alert("時間輸入有誤!");
            }

        }
        if(act=='print2'){
            message=checkdate(2);
            if(message){
                var sdate3=$("#sdate3").val();
                var edate3=$("#edate3").val();
                var find = '/';
                var re = new RegExp(find, 'g');
                sdate3=sdate3.replace(re,"");
                edate3=edate3.replace(re,"");
                $("#final_sdate2").val(sdate3);
                $("#final_edate2").val(edate3);
                $("#form_two").attr("action","/admin/entryexport/export/5");
                $("#form_two").submit();
            }else{
                alert('時間輸入有誤');
            }

        }
    }
    //班別資料匯出
    function selectclass()
    {
        var iHeight=(window.screen.availHeight)*0.6;
        var iWidth=(window.screen.availWidth)*0.3;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;

            var s_y=$('#sdate_year').val();
            var s_m=$('#sdate_month').val();
            if(s_m.length<2){
                s_m='0'+s_m;
            }
            var e_y=$('#edate_year').val();
            var e_m=$('#edate_month').val();
            if(e_m.length<2){
                e_m='0'+e_m;
            }
            var s_y_2=Number(s_y)+1911;
            var e_y_2=Number(e_y)+1911;
            var lastDay_s= '01';
            var lastDay_e= new Date(e_y_2,e_m,0);
            var day_e = lastDay_e.getDate();
            var final_sdate=s_y+s_m+lastDay_s;
            var final_edate=e_y+e_m+day_e;
            $("#final_sdate").val(final_sdate);
            $("#final_edate").val(final_edate);
            message=checkdate(1);
            console.log(message);
            if(message){
                $("#form_one").attr("action","/admin/entryexport/select_class");
                window.open('/admin/entryexport/select_class/'+final_sdate+'_'+final_edate,'mywin','height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
            }else{
                alert("時間輸入有誤!");
            }

    }
    function print()
    {
        var export_class=$('input:checkbox[id=class]:checked').val();
        var export_teacher=$('input:checkbox[id=teacher]:checked').val();
        var export_course=$('input:checkbox[id=course]:checked').val();
        var s_y=$('#sdate_year').val();
        var s_m=$('#sdate_month').val();
        if(s_m.length<2){
            s_m='0'+s_m;
        }
        var e_y=$('#edate_year').val();
        var e_m=$('#edate_month').val();
        if(e_m.length<2){
            e_m='0'+e_m;
        }
        var s_y_2=Number(s_y)+1911;
        var e_y_2=Number(e_y)+1911;
        var lastDay_s= '01';
        var lastDay_e= new Date(e_y_2,e_m,0);
        var day_e = lastDay_e.getDate();
        var final_sdate=s_y+s_m+lastDay_s;
        var final_edate=e_y+e_m+day_e;
        $("#final_sdate").val(final_sdate);
        $("#final_edate").val(final_edate);

        if(export_class=='class'){
            $("#form_one").attr("action","/admin/entryexport/export/1");
            $("#form_one").submit();
        }
        if(export_teacher=='teacher'){
            $("#form_one").attr("action","/admin/entryexport/export/2");
            $("#form_one").submit();
        }
        if(export_course=='course'){
            $("#form_one").attr("action","/admin/entryexport/export/3");
            $("#form_one").submit();
        }


    }
    function btcolor(obj)
    {
        var id=obj.id;
        document.getElementById("one").style.color = "";
        document.getElementById("two").style.color = "";
        document.getElementById("three").style.color = "";
        document.getElementById("form_one").display="none";
        document.getElementById(id).style.color = "blue";
        document.getElementById("form_one").style.display="none";
        document.getElementById("form_two").style.display="none";
        document.getElementById("form_three").style.display="none";
        document.getElementById("form_"+id).style.display="block";
        if(id=='three'){
            document.getElementById("table_three").style.display="block";
            document.getElementById("p1").style.display="block";
        }else{
            document.getElementById("table_three").style.display="none";
            document.getElementById("p1").style.display="none";
        }
        if(id=='two'){
            document.getElementById("table_two").style.display="block";
        }else{
            document.getElementById("table_two").style.display="none";
        }
    }

    var tableData='';
    var class_info='';
    var j=1;
    function select_class(savefield)
    {

            var class_info_stirng=$("#class_info").val();
            class_info += $("#class_info").val();
            class_info +=',';
            $("#class_info").val(class_info);
            $("#class_info2").val(class_info);
            var class_info_array=class_info_stirng.split(",");

            for(var i=0;i<class_info_array.length;i++){
                var class_detail=class_info_array[i].split("_");
                tableData+='<tr class="text-center">';
                tableData+='<td>'+j+'</td>';
                tableData+='<td>'+class_detail[0]+'</td>';
                tableData+='<td>'+class_detail[1]+'</td>';
                tableData+='<td>'+class_detail[2]+'</td>';
                tableData+='<td><input type="button" value="delete" onclick="deleteRow(this)"></td>'
                tableData+='</tr>';
                j++;
            }
            $("#tbody1").html(tableData);


    }
    var classinfo='';
    function select_output(savefield_2)
    {
        var output = $("#"+savefield_2).val();
        //console.log(output);
        $("#"+savefield_2+'2').val(output);
    }

    function deleteRow(r)
    {
        var i = r.parentNode.parentNode.rowIndex;
        var condition1=document.getElementById("myTable").rows[i].cells[1].innerHTML;
        var condition2=document.getElementById("myTable").rows[i].cells[2].innerHTML;
        var condition3=document.getElementById("myTable").rows[i].cells[3].innerHTML;

        var final_condition=condition1+'_'+condition2+'_'+condition3;
        var input_text=$("#class_info").val();
        var final_input_text=input_text.replace(final_condition,"");
        var final_input_text2=final_input_text.replace(",","");
        $("#class_info").val(final_input_text2);
        document.getElementById("myTable").deleteRow(i);

    }

    function check()
    {
        var class_info=$("#class_info").val();
        var output_info=$("#output_info").val();
        var exporttype=$("#exporttype").val();
        if(exporttype.value='basic_info'){
            if(class_info.length==0){
                alert("請選擇班期")
                return false;
            }
            if(output_info.length==0){
                alert("請選擇欄位")
                return false;
            }
            $("#form").submit();
            //console.log(class_info);
            //console.log(output_info);
        }
        //var class_info=$("#class_info").val();
        //var class_info=$("#class_info").val();
    }

</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

