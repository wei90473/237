@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'training_performance';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練績效報表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">訓練績效報表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form method="post" action="/admin/training_performance/export" id="search_form">
                        {{ csrf_field() }}
                        <div class="card">
                            <div class="card-header">
                            
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group bootstrap-touchspin number_box">
                                                    <!-- 輸入欄位 -->
                                                    <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" required
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" onchange="yearchange();" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>下列選項請點選其一</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        
                                        <div class="form-group row align-items-center">
                                            <input class="mr-2 pt-2 float-right" type="radio" id="allYear" name="monthYear" value="1">
                                            <label class="">全年<span class="text-danger"></span></label>    
                                        </div>

                                        <div class="form-group row align-items-center">
                                            <input class="control-label text-md-right pt-2 mr-2" type="radio" id="month" name="monthYear" value="2" checked >
                                            <label class="mr-2">月份<span class="text-danger"></span></label>
                                            <div class="col-2 ml-4">
                                                <select class="select2 form-control select2-single input-max" name="selectMonth">
                                                    @for ($i = 1; $i<=12; $i++)
                                                        <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center">
                                            <input class="control-label text-md-right pt-2 mr-2" type="radio" id="startEnd" name="monthYear" value="3" onchange="yearchange()">
                                            <label class="mr-2">起始日期<span class="text-danger"></span></label>
                                          
                                                <input type="text" class="form-control col-1" id="startYear" name="startYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3"  >
                                                <p style="display: inline">年</p>

                                                <input type="text" class="form-control col-1" id="startMonth" name="startMonth" min="1"
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                <p style="display: inline">月</p>
                                      
                                        </div>
                                        <div class="form-group row align-items-center">
                                            <input class="control-label text-md-right pt-2 mr-2" type="radio" id="start" name="start" value="" style="visibility:hidden" onchange="yearchange();"> 
                                            <label class="mr-2">結束日期<span class="text-danger"></span></label>
                                                <input type="text" class="form-control col-1" id="endYear" name="endYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                                <p style="display: inline">年</p>
                                                <input type="text" class="form-control col-1" id="endMonth" name="endMonth" min="1"
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                <p style="display: inline">月</p>
                                        </div>
                                        

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>其他條件(Optional)</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">訓練性質<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <select class="select2 form-control select2-single input-max" name="training" >
                                                    <option value="0">請選擇訓練性質</option>
                                                    <option value="1">1 中高階公務人員訓練</option>
                                                    <option value="2">2 人事人員專業訓練</option>
                                                    <option value="3">3 一般公務人員訓練</option>
                                                </select>
                                            </div>   
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-2 text-right">班別性質<span class="text-danger"></span></label>
                                            <div class="col-10">
                                                <select class="select2 form-control select2-single input-max" name="classes">

                                                    <option value="0">請選擇班別性質</option>
                                                    <option value="A0">訓練計畫四大類</option>
                                                    <option value="23">領導力發展</option>
                                                    <option value="24">政策能力訓練</option>
                                                    <option value="25">部會業務知能訓練</option>
                                                    <option value="26">自我成長及其他</option>
                                                </select>
                                            </div>   
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-2 text-right">開班性質<span class="text-danger"></span></label>
                                            <div class="col-10">
                                                <select class="select2 form-control select2-single input-max" name="startClass">
                                                    <option value="0">請選擇開班性質</option>
                                                    <option value="1">年度訓練計畫班期</option>
                                                    <option value="2">年度臨時增開班期</option>
                                                </select>
                                            </div>   
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">天數(>=)<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control input-max" id="yerly" name="days" min="1" placeholder="請輸入天數" 
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                            </div>   
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">部門<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <select class="select2 form-control select2-single input-max" id="department" name="department">
                                                <option value="0">請選擇部門</option>
                                                <option value="A0">各組室</option>
                                                    @foreach ($department as $department)
                                                        <option value="{{$department->section}}">{{$department->section}}</option>
                                                    @endforeach
                                                </select>
                                            </div>   
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">辦班人員<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <select class="select2 form-control select2-single input-max" name="staff">
                                                <option value="0">請選擇辦班人員</option>
                                                    @foreach ($employee as $employee)
                                                        <option value="{{$employee->userid}}">{{$employee->username}}</option>
                                                    @endforeach
                                                </select>
                                            </div>   
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">班別類型<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <select class="select2 form-control select2-single input-max" name="checkClass">
                                                <option value="0">請選擇班別類型</option>
                                                    <option value="1">自辦班</option>
                                                    <option value="2">委訓班</option>
                                                    <option value="4">外地班</option>
                                                    <option value="5">巡迴研習</option>
                                                    <option value="3">合作辦理</option>
                                                </select>
                                            </div>   
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card">

                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <input type="checkbox" name="satisfaction" value="1" id="satisfaction" onchange="checkT()">辦班人員及滿意度
                                    </div>
                                    <div class="col-3">
                                        <input type="checkbox" name="service" id="service" value="2">含【行政服務】
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row justify-content-around">
                                    <div class="col-2">
                                        <input type="radio" name="area" id="taipei" value="1">台北院區
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="area" id="nantou" value="2">南投院區
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="area" id="allarea" value="3" checked >全部
                                    </div>
                                </div>
                                <br>
                            </div>

                        </div>
                        <div id="dvdoctype" class="form-group row  align-items-center justify-content-center">
                            <label class="col-2 text-right">請選檔案格式：</label>
                            <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                            <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                            <label class="col-1"></label>
                        </div>
                        <div align="center">
                            <button type="submit" class="btn mobile-100 mb-3 mr-1"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>

//跳出訊息視窗
    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    function checkT(){
        
        if($('#satisfaction').prop('checked') == true){
            $('#service').attr('disabled', false);
            $('#service').prop('checked', true);
        }else{
            $('#service').prop('checked', false);
            $('#service').attr('disabled', true);
        }
        
    }

    function timeChange(){
        $('#endYear').val($('#startYear').val());
        $('#endMonth').val($('#startMonth').val());
    }

    function yearchange(){
        $('#startYear').val($('#yerly').val());
        $('#endYear').val($('#yerly').val());
        $('#startMonth').val("1");
        $('#endMonth').val("12");
    }
    
</script>
@endsection