@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'student_list';?>
    <style type="text/css">
        .col-2.special {
            flex: 0 0 11.666667%;
        }
        .align-items-center.special input {
            margin-bottom: .5rem;
        }
        .top-options {
            margin-left: 10px;
        }
    </style>
    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員名冊</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員名冊</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員名冊</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <form id="exportFile" method="post" id="search_form" action="/admin/student_list/export" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="form-group row align-items-center">
                                        <label class="col-sm-2"></label>
                                        <input class="mb-2 float-right top-options" type="radio" id="outputtype1" name="outputtype" value="1" checked>
                                        <label class="col-2 text-left">參訓人員名冊(已序號)</label>    
                                        <input class="mb-2 float-right" type="radio" id="outputtype2" name="outputtype" value="2" >
                                        <label class="col-3 text-left">參訓人員名冊(含未序號)</label>    
                                        <input class="mb-2 float-right" type="radio" id="outputtype3" name="outputtype" value="3" >
                                        <label class="col-2 text-left">結訓人員名冊</label>
                                        <input class="mb-2 float-right" type="radio" id="outputtype4" name="outputtype" value="4" >
                                        <label class="col-2 text-left">最新學員名冊</label>       
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                        <div class="col-6">
                                             <div class="input-group bootstrap-touchspin number_box">
                                                <select id="classes" name="classes" class="select2 form-control select2-single input-max" onchange="getTerms();">
                                                    <option value="">請選擇</option>
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
                                                <?php foreach ($termArr as $key => $va) { ?>
                                                    <option value='<?=$va->term?>'><?=$va->term?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2"></label>
                                        <label class="col-2 control-label special" >
                                        <input type="checkbox" id="checkteam" name="checkteam" value="1">組別選項
                                        </label>
                                        <label class="col-2 special" >
                                            <input type="checkbox" id="checkedu" name="checkedu" value="1"> 學歷
                                        </label>
                                        <label class="col-2 special" >
                                            <input type="checkbox" id="checkbirth" name="checkbirth" value="1"> 出生日期
                                        </label>
                                        <label class="col-2 special" >
                                            <input type="checkbox" id="checkphone" name="checkphone" value="1"> 辦公室電話
                                        </label>
                                        <label class="col-2 special" >
                                            <input type="checkbox" id="checkmobile" name="checkmobile" value="1"> 手機號碼
                                        </label>
                                        <label class="col-3" >
                                            <input type="checkbox" id="checkroom" name="checkroom" value="1"> 寢室編號(南投院區適用)
                                        </label>
                                    </div>
                                    <div class="form-group row align-items-center special">     
                                        <label class="col-2 text-md-right" > 姓名是否遮蔽</label>
                                        <input class="mb-2 float-right top-options" type="radio" id="nametypeY" name="nametype" value="Y" style="margin-left: 10px;">
                                        <label class="col-1 text-left">是</label>    
                                        <input class="mb-2 float-right" type="radio" id="nametypeN" name="nametype" value="N" checked>
                                        <label class="col-1 text-left">否</label>  
                                    </div>
                                    <div class="form-group row align-items-center specialmmm">     
                                        <label class="col-2 text-md-right" >是否依組別分頁</label>
                                        <input class="mb-2 float-right top-options" type="radio" id="grouptypeY" name="grouptype" value="Y" style="margin-left: 10px;" >
                                        <label class="col-1 text-left">是</label>    
                                        <input class="mb-2 float-right" type="radio" id="grouptypeN" name="grouptype" value="N" checked>
                                        <label class="col-1 text-left">否</label>  
                                    </div>  

                                    <div id="dvdoctype" class="form-group row  align-items-center">
                                        <label class="col-2 text-right">請選檔案格式：</label>
                                        <label class="mr-3 top-options"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
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

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/student_list/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#term").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};

</script>
@endsection