@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_categories';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座一覽表-類別</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座一覽表-類別</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座一覽表-類別</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form id="exportFile" method="post" id="search_form" action="/admin/lecture_categories/export" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <!--  variable declare  --> 
                                        <?php $sdatetw =''; $edatetw =''; ?>
                                        <div class="form-group row align-items-center">
                                        <!--  datepicker -->
                                            <label class="col-sm-1 text-right">起訖時間</label>
                                            <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" placeholder="請選擇要查詢的日期" readonly name="sdatetw"  min="1" autocomplete="off">
                                            <label class="text-center">~</label>
                                            <input type="text" class="form-control col-2" value="{{$edatetw}}" id="edatetw" placeholder="請選擇要查詢的日期" readonly name="edatetw"  min="1" autocomplete="off">
                                        </div>
                                        <div class="form-group row align-items-center">
                                            <input class="pt-2 float-right" type="radio" id="condition1" name="condition"" value="1" checked>    
                                            <label class="col-1 text-right">分類</label>
                                            <select id="category" name="category" class="select2 form-control select2-single input-max" required>
                                                <option value='0'>請選擇分類</option>
                                                <?php foreach ($CatArr as $key => $va) { ?>
                                                    <option value='<?=$va->code?>'><?=$va->code?> <?=$va->name?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group row align-items-center">
                                            <input class="pt-2 float-right" type="radio" id="condition2" name="condition" value="2">    
                                            <label class="col-sm-1 text-right">講座專長</label>
                                            <input type="text" class="form-control input-max width"  id="expertise" name="expertise"  autocomplete="off">
                                        </div>
                                        <div id="dvdoctype" class="form-group row  align-items-center">
                                            <label class="col-2 text-right">請選檔案格式：</label>
                                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                                        </div>
                                        <div class="form-group row justify-content-center">
                                            <div class="col-6">
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
<!--  src of datepicker  --> 
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>
<script>

     if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

$( function() {
    
    $('#sdatetw').datepicker({
        format: "twy-mm-dd",
    });
    $('#edatetw').datepicker({
        format: "twy-mm-dd",
    });
} );

// function getTerms()	{
//         $.ajax({
//             type: 'post',
//             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
//             dataType: "html",
//             url:"/admin/lecture_categories/getCategory",
//             data: { classes: $('#classes').val()},
//             success: function(data){
//             let dataArr = JSON.parse(data);
//             let tempHTML = "";
//             for(let i=0; i<dataArr.length; i++) 
//             {
//                 tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
//             }
//             $("#term").html(tempHTML);
//             },
//             error: function() {
//                 console.log('Ajax Error');
//             }
//         });
// 	};

</script>
@endsection