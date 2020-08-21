@extends('admin.layouts.layouts')
@section('content')

    <style>
        .halfArea {
            padding: 5px;
            width: 100%;
            height: 300px;
            max-width: 300px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
            overflow: auto;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0px 5px;
        }
        .arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        /* .select2-container--default .select2-results>.select2-results__options {
            max-height: 500px !important;
        } */
    </style>

    <?php $_menu = 'studyplan_distribution_all';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">名額分配總表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">報表列印</li>
                        <li class="active">名額分配總表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>名額分配總表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                        <form method="post" action="/admin/studyplan_distribution_all/export" id="search_form">
                                        {{ csrf_field() }}
                                            <!-- 年度 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger"></span></label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <!-- 輸入欄位 -->
                                                        <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" 
                                                        value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" required onchange="getTimes();">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- 第幾次調查 -->
                                            <div class="form-group row">
                                                <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查</label>
                                                <div class="col-sm-10">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <input type="text" class="form-control input-max" id="times" name="times" value="" autocomplete="off" required onclick="chooseTimes()" onkeyup="this.value=this.value.replace(/[^\d]/g,'');chooseTimes()">
                                                        <button id="btn-times" type="button" class="btn btn-primary" onclick="chooseTimes()">+</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row align-items-center">
                                                <label class="col-2 pl-2 pr-2 text-right" > 院區 </label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="taipei" value="1" checked >台北院區</label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="nantou" value="2">南投院區</label>
                                                <label class="pl-2 pr-2" ><input type="radio" name="area" id="allarea" value="3" >全部</label>
                                            </div>
                                            <div id="dvdoctype" class="form-group row  align-items-center">
                                                    <label class="col-2 text-right">請選檔案格式：</label>
                                                    <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                                    <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                            </div>
                                            <div align="center">
                                                <button type="submit" class="btn mobile-100 mb-3 mr-1"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
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

    <!-- 第幾次調查 modal -->
	<div class="modal fade bd-example-modal-lg timesPop" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">第幾次調查</strong></h4>
			        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button> -->
		        </div>
		        <div class="modal-body">
                    <div class="col-md-12" id="course_div" style='display: flex;'>
                        <div style="flex:1;">
                            <div>所有的梯次</div>
                            <select name="left_select[]" multiple="multiple" id="left_select" class="halfArea">
                            </select>
                        </div>
                        <div class="arrow_con">
                        	<button type="button" id="All_left_btn">>></button>
                            <button type="button" id="left_btn">></button>
                            <button type="button" id="right_btn"><</button>
							<button type="button" id="All_right_btn"><<</button>
                        </div>
                        <div style="flex:1;">
                            <div>選取的梯次</div>
                            <select name="right_select[]" multiple="multiple" id="right_select" class="halfArea">
                            </select>
                        </div>
                        <div></div>
                    </div>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmTimes()">確定</button>
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
    
    function getTimes()
    {
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/studyplan_distribution_all/gettime',
            data: {yerly: $('#yerly').val()},
            success: function(response){
                let dataArr =JSON.parse(response);
                let tempHTML = "";
                for(let i=0; i<dataArr.length; i++) {
                    if(dataArr[i].times!="" && dataArr[i].times!=null){
                        tempHTML += "<option value='"+dataArr[i].times+"'>"+dataArr[i].times+"</option> ";
                    }
                }

                $("#left_select").html(tempHTML);
                $("#right_select").html("");

            },
            error: function(){
                alert('Ajax Error');
            }
        });
    }

    // 選擇梯次，開啟pop
    function chooseTimes() {
        if($("#yerly").val() == '') {
            $("#times").blur();
            alert("請輸入年度");
            return;
        }

        // if ($("#left_select option").length==0) {
        //     alert("此年份無資料");
        //     return;
        // }
        $(".timesPop").modal('show');
        $("#times").html("");
    }

    // pop選擇第幾次調查
    function confirmTimes() {
        let tempTimes = '';
        $("#right_select option").each(function(index){
            if(index ==  $("#right_select option").length-1) {
                tempTimes += $(this).val();
            }
            else {
                tempTimes += $(this).val() + ',';
            }
        });

        $("#times").val(tempTimes);

        if(tempTimes == '') {
            $("#times").prop('readonly', false);
        }
        else {
            $("#times").prop('readonly', true);
        }
    }

    $(function(){
        //页面加载完毕后开始执行的事件
        //点击左边select 去右边
        $("#left_btn").click(function(){

        	let sortArr = []
        	
            $("#left_select option:selected").each(function(){
                $("#right_select").append($(this).prop("outerHTML"));
                $(this).remove();
			});
                
            $("#right_select option").each(function(index){
                sortArr.push($(this).val());
            });
            
            sortArr.sort();
            
            let tempHTML = "";
            for(let i=0; i<sortArr.length; i++) {
                tempHTML += "<option value='"+sortArr[i]+"'>"+sortArr[i]+"</option>"
            }
            
            $("#right_select").html(tempHTML);
            
        });

        //点击右边select 去左边
        $("#right_btn").click(function(){
        	let sortArr = []
            $("#right_select option:selected").each(function(){
                $("#left_select").append($(this).prop("outerHTML"));
                $(this).remove();
            });
                       
            $("#left_select option").each(function(index){
                sortArr.push($(this).val());
            });
            
            sortArr.sort();
            
            let tempHTML = "";
            for(let i=0; i<sortArr.length; i++) {
                tempHTML += "<option value='"+sortArr[i]+"'>"+sortArr[i]+"</option>"
            }
            
            $("#left_select").html(tempHTML);

        });

        //左全選往右
        $("#All_left_btn").click(function(){
        	let sortArr = []
            $("#right_select option").each(function(){
                sortArr.push($(this).val());
            });
            
            $("#left_select option").each(function(){
            	sortArr.push($(this).val());
                $(this).remove();
            });
            
            sortArr.sort();
            
            let tempHTML = "";
            for(let i=0; i<sortArr.length; i++) {
                tempHTML += "<option value='"+sortArr[i]+"'>"+sortArr[i]+"</option>"
            }
            $("#right_select").html(tempHTML);

        });

        //右全選往左
        $("#All_right_btn").click(function(){
        	let sortArr = []
            $("#left_select option").each(function(){
                sortArr.push($(this).val());
            });
            
            $("#right_select option").each(function(){
            	sortArr.push($(this).val());
                $(this).remove();
            });
            
            sortArr.sort();
            
            let tempHTML = "";
            for(let i=0; i<sortArr.length; i++) {
                tempHTML += "<option value='"+sortArr[i]+"'>"+sortArr[i]+"</option>"
            }
            $("#left_select").html(tempHTML);

        });
    });
</script>
@endsection