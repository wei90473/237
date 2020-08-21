@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teaching_material_form';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教材交印單</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>教材交印單</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form id="exportFile" method="post" id="search_form" action="/admin/teaching_material_form/export" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                        <div class="form-group row align-items-center">
                                            <input class=" pt-2 float-right" type="radio" id="rbserial"" name="type" value="1" checked>
                                            <label class="col-2 text-center">編&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp號：</label>
                                            <input type="text" class=" col-1 form-control input-max width ml-2" id="serial" name="serial" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">    
                                        </div>

                                        <div class="form-group row align-items-center">
                                            <input class="pt-2 float-right" type="radio" id="rbclass" name="type" value="2"  >
                                            <label class="col-2 text-center">班&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp別：</label>
                                            <div class="col-7">
                                                <div class="input-group bootstrap-touchspin number_box">
                                                    <select id="classes" name="classes" class="select2 form-control select2-single input-max mr-2" onchange="getMaterial();">
                                                    <option value='0'>請選擇</option>
                                                    <?php foreach ($classArr as $key => $va) { ?>
                                                        <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                    <?php } ?>
                                                    </select>
                                                </div>
                                                 <!-- 教材清單清單 -->
                                                 <div class="table-responsive" id='materia_list'>
                                                   
                                                </div>  
                                            </div>
                                       


                                        </div>

                                        <div class="form-group row align-items-center">
                                            <input class="pt-2 float-right" type="radio" id="rbmaterial" name="type" value="3">
                                            <label class="col-2 text-center">教&nbsp&nbsp材&nbsp&nbsp名&nbsp&nbsp稱：</label>
                                            <input type="text" class=" col-7 form-control input-max width ml-2" id="material" name="material" min="1" value="" autocomplete="off" >
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
    $('[name=type]').change(function () {
        if( $(this).val() != 2){
            $("#materia_list").html('');
        }
       
    })

    function getMaterial()	{
        let tempHTML = "";
        $.ajax({
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/teaching_material_form/getMaterial",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            if(dataArr.length>0){
                tempHTML = " <table class='table table-bordered mb-0'> <thead> <tr><th>選擇</th><th>期別</th><th>教材名稱</th><th>總份數</th> </tr></thead> <tbody> ";
        
            }        
            for(let i=0; i<dataArr.length; i++) {
                tempHTML += "  <tr>   <td><input type='checkbox' id='serno[]' name='serno[]' value='"+dataArr[i].serno+"'</td>\
                                <td>"+dataArr[i].term+"</td>\
                                <td>"+dataArr[i].material+"</td>\
                                <td>"+dataArr[i].copy+"</td>\
                               </tr>";
            }
           
            if(dataArr.length>0){
                tempHTML +=  '</tbody></table>';
            }
            if(dataArr.length==0){
                tempHTML = "本班級並無教材交印資料";
            }
            $("#materia_list").html(tempHTML);
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
	};

</script>
@endsection