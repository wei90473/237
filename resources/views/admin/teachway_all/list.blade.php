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

    <?php $_menu = 'teachway_all';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教學教法運用彙總表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教學教法運用彙總表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>教學教法運用彙總表</h3>
                        </div>
                        <div class="card-body">
                            <form id="exportFile" method="post" id="search_form" action="/admin/teachway_all/export" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <?php $sdatetw =''; $edatetw =''; ?>
                            <div class="form-group row align-items-center">
                                <label class="col-1">訓練期間<span class="text-danger"></span></label>
                                <input type="text" class="form-control col-2" value="{{$sdatetw}}" id="sdatetw" name="sdatetw" placeholder="請選擇要查詢的日期" readonly  min="1" autocomplete="off" readonly>
                                <p style="display: inline">～</p>
                                <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" name="edatetw" placeholder="請選擇要查詢的日期" readonly  min="1" autocomplete="off" readonly>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-1">班別性質<span class="text-danger"></span></label>
                                 <div class="col-2">
                                    <select class="select2 form-control select2-single input-max" id="classtype" name="classtype">
                                    <option value="0">請選擇班別性質</option>
                                                    @foreach ($class as $class)
                                                        <option value="{{$class->value}}">{{$class->text}}</option>
                                                    @endforeach
                                   </select>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <input type="text"  id="tways" name="tways" value="" autocomplete="off" style="visibility: hidden;">
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-md-1" >教法:<span class="text-danger"></span></label>
                                <div class="col-md-7" id="course_div" style='display: flex; '>
                                    <div style="flex:1;">
                                        <div>未選取</div>
                                        <select name="left_select[]" multiple="multiple" id="left_select" class="halfArea" >
                                            <?php foreach ($twaysArr as $key => $va) { ?>
                                                <option value='<?=$va->method."_".$va->name?>'><?=$va->method."_".$va->name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="arrow_con ml-5 col-2 mt-4" >
                                        <div class="form-group row  ">
                                            <button type="button" id="left_btn">->新增</button>
                                        </div>
                                        <div class="form-group row ">    
                                            <button type="button" id="right_btn"><-移除</button>
                                        </div>
                                    </div>
                                    <div style="flex:1;">
                                        <div>已選取</div>
                                        <select name="right_select[]" multiple="multiple" id="right_select" class="halfArea" >

                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div id="dvdoctype" class="form-group row  align-items-center">
                                <label class="col-2 text-right">請選檔案格式：</label>
                                <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                            </div>

                            <div class="form-group row align-items-center justify-content-center col-9">
                                <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                <label id="download" visible="false"></label>
                            </div>
                            </form>

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
 <!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script>
//  call datepicker
    $( function() {
    
        $('#sdatetw').datepicker({
            format: "twy-mm-dd",
        });
        $('#edatetw').datepicker({
            format: "twy-mm-dd",
        });
    } );

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    $(function(){
        //左邊去右邊
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
            fill();
            
        });

        //右邊去左邊
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
            fill();
        });
    });

    function fill() {
        let temp = '';
        $("#right_select option").each(function(index){
            if(index ==  $("#right_select option").length-1) {
                temp += $(this).val().substring(0,3);
            }
            else {
                temp += $(this).val().substring(0,3) + ',';
            }
        });

        $("#tways").val(temp);

    }

</script>
@endsection