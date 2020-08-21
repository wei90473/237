@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'student_seat_list_south';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員座位表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員座位表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員座位表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/student_seat_list_south/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label class="col-3 control-label text-center pt-2">輸入列印條件</label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                        <div class="col-6">
                                             <div class="input-group bootstrap-touchspin number_box">
                                                <select id="classes" name="classes" class="select2 form-control select2-single input-max" onchange="getTerms();" required>
                                                <option value='0'>請選擇</option>
                                                <?php foreach ($classArr as $key => $va) { ?>
                                                    <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-2 control-label text-md-right pt-2">期別</label>
                                        <div class="col-3">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select id="term" name="term" class="select2 form-control select2-single input-max" required>                                                
                                                <option value='0'>請選擇期別</option>
                                                <?php foreach ($termArr as $key => $va) { ?>
                                                    <option value='<?=$va->term?>'><?=$va->term?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-2 control-label text-md-right pt-2">教室</label>
                                        <div class="col-3">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <select id="site" name="site" class="select2 form-control select2-single input-max" onchange="setseat();" required>
                                                <option value='0'>請選擇教室</option>

                                                    <option value='001'>電腦教室</option>
                                                    <option value='501'>501教室</option>
                                                    <option value='503'>503教室</option>
                                                    <option value='504'>504教室</option>
                                                    <option value='601'>601教室</option>
                                                    <option value='602'>602教室</option>
                                                    <option value='603'>603教室</option>
                                                    <option value='604'>604教室</option>
                                                    <option value='701'>701教室</option>
                                                    <option value='702'>702教室</option>
                                                    <option value='703'>703教室</option>
                                                    <option value='801'>研討室</option>
                                                    <option value='901'>國際會議廳</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="dvtype" class="form-group row justify-content-left align-items-center" style="visibility:hidden">
                                        <label class="col-2 text-right">座位類型</label>
                                        <input class="mb-2 float-right" type="radio" id="type1" name="optType" value="A" checked>
                                        <label class="col-1 text-left " id="label1">標準</label>  
                                        <input class="mb-2 float-right" type="radio" id="type3" name="optType" value="B" >
                                        <label class="col-1 text-left" id="label3">T型</label>  
                                        <input class="mb-2 float-right" type="radio" id="type4" name="optType" value="C" >
                                        <label class="col-1 text-left" id="label4">工型</label>     
                                    </div>

                                    <!-- <div id="dvtype2" class="form-group row justify-content-left align-items-center" style="visibility:hidden">
                                        <label class="col-2 text-right">座位類型</label>
                                        <input class="mb-2 float-right" type="radio" id="type21" name="seat" value="1" checked>
                                        <label class="col-1 text-left " id="label1">30人</label>
                                        <input class="mb-2 float-right" type="radio" id="type22" name="seat" value="2" >
                                        <label class="col-1 text-left" id="label2" >40人</label>    
                                    </div> -->

                                    <div class="form-group row justify-content-left align-items-center">
                                        <label class="col-2 text-right">排列方式</label>
                                        <input class="mb-2 float-right" type="radio" id="arrange1" name="optSet" value="0" checked>
                                        <label class="col-1 text-left ">依學號</label>   
                                        <input class="mb-2  float-right" type="radio" id="arrange2" name="optSet" value="1" >
                                        <label class="col-1 text-left">依組別</label>    
                                    </div>    
                                    <div class="form-group row justify-content-left align-items-center">
                                        <label class="col-2 text-right">列印格式</label>
                                        <input class="mb-2 float-right" type="radio" id="print1" name="optFormat" value="1"checked>
                                        <label class="col-1 text-left ">講座</label>
                                        <input class="mb-2 float-right" type="radio" id="print2" name="optFormat" value="0">
                                        <label class="col-1 text-left">學員</label>
                                    </div>
                                    <!-- <div class="form-group row justify-content-left">
                                        <label class="col-7 text-center">註:十四樓貴賓廳菱形座位為一列9人的座位表。</label>
                                    </div> -->

                                    <div id="dvblock" class="form-group row" style="visibility:hidden">
                                        <label class="col-sm-2 control-label text-right">保留空位</label>
                                        <div class="col-sm-10">
                                            <div class="input-group bootstrap-touchspin number_box">
                                                <input type="text" class="form-control input-max" id="blocks" name="blocks" placeholder="請以逗號隔開輸入儲存格位置，如:D5,E7,F8..." value="" autocomplete="off" >
                                            </div>
                                        </div>
                                    </div>

                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                        <label class="col-2 text-right">請選檔案格式：</label>
                                        <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                        <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                    </div>

                                    <div class="form-group row justify-content-center">
                                            <div class="col-6 ">
                                                <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                <label id="download"></label>
                                            </div> 
                                        </div>
                                </form>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    $(document).ready(function(){
        setseat();
    });

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_seat_list_south/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#term").html(tempHTML);
            // getSites();
            },
            error: function() {
                console.log('Ajax Error');
            }
        });

	};

    function getSites()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_seat_list_south/getSites",
            data: { classes: $('#classes').val(),term: $('#term').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].site+"'>"+dataArr[i].site+" "+dataArr[i].name+"</option>";                     
            }
            $("#site").html(tempHTML);
            // setseat();
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};

    function setseat(){
        // console.log($('#site').val());
        switch ($('#site').val()) {
            
            case '501':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            case '503':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            case '504':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            case '601':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            case '602':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            case '603':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            case '604':
                document.getElementById("dvtype").style.visibility="visible";
                break;
            default:            
                document.getElementById("dvtype").style.visibility="hidden";
                           
                // document.getElementById("dvtype2").style.visibility="visible";
                // document.getElementById("dvtype2").style.visibility="hidden";
        }

        if($('#site').val()=="901"){
            document.getElementById("dvblock").style.visibility="visible";
        }else{
            document.getElementById("dvblock").style.visibility="hidden";
        }

    };

</script>
@endsection




                                    