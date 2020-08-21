@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'each_training_all';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練成果維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">訓練成果維護</li>
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
                                <h3 class="card-title"><i class="fa fa-list pr-2"></i>訓練成果維護</h3>
                            </div>
                            <form id="save" method="post" id="search_form" action="/admin/each_training_all/save" >
                                {{ csrf_field() }}
                                <div class="card-body" align="center">
                                    <div class="row">
                                        <div class="col-12">
                                    
                                            <div class="form-group row" >
                                                <p style="display: inline">年度</p>
                                                <input type="text" class="form-control col-1 ml-1 pr-2 mr-2" id="startYear" name="startYear" min="1" 
                                                    value="{{ $dateData['yerly'] }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" onchange="datechange();">
                                                <p style="display: inline">月份</p>
                                                <input type="text" class="form-control col-1 ml-1" id="startMonth" name="startMonth" min="1"
                                                    value="{{ $dateData['month'] }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2" onchange="datechange();">

                                                <a id="qlink" href="/admin/each_training_all/0/0/query">
                                                    <button type="button" class="btn-sm btn-info mb-3 ml-2" style="width:70px;height:35px;"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                </a>

                                                <!-- <a href="/admin/each_training_all/send"> -->
                                                <button type="button" class="btn-sm btn-info ml-3" style="width:70px;height:35px;" onclick="modify()" ><i class="fa fa-pencil"></i>修改</button>
                                                <!-- </a>    -->
                                                <div id="dvsave" style="visibility:hidden;">
                                                <!-- <a href="/admin/each_training_all/save"> -->
                                                    <button type="submit" id="save" class="btn-sm btn-info ml-3" style="width:70px;height:35px;" onclick="return confirm('是否確認儲存資料？');"><i class="fa fa-save pr-2"></i>儲存</button>
                                                <!-- </a>    -->
                                                </div>
                                                <a href="/admin/each_training_all">
                                                    <button type="button" class="btn-sm btn-info ml-3" style="width:70px;height:35px;">取消</button>
                                                </a> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body" >
                                        <div class="row " >
                                            <div class="col-12"> 
                                                <div class="table-responsive">
                                                    <table class="table table-bordered mb-0" width="2000">
                                                        <thead>
                                                        <tr>
                                                            <th class="text-center" >年度</th>
                                                            <th class="text-center" >月份</th>
                                                            <th class="text-center" >訓練類別</th>
                                                            <th class="text-center" >期數(課數)</th>
                                                            <th class="text-center">訓練人數</th>
                                                            <th class="text-center">訓練人天數</th>
                                                            <th class="text-center">訓練人時數</th>
                                                            <!--th class="text-center" width="70">刪除</th-->
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        <?php if(count($data)==0) { ?>
                                                            <tr>
                                                                <td colspan="12">無資料!  請更改條件搜尋!</td>
                                                            </tr>
                                                        <?php } ?>

                                                        @foreach($data as $va)
                                                            <tr>
                                                                <td class="text-center">{{ $va['yerly']}}</td>  
                                                                <td class="text-left">{{ $va['mon']}}</td>
                                                                <td class="text-center">{{ $va['traintype_name']}}</td>
                                                                <td class="text-center"><input type="text" class="form-control text-right" value="{{ $va['termcnt']}}" id="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_termcnt"  name="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_termcnt" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"  min="1" autocomplete="off" readonly required></td>
                                                                <td class="text-center"><input type="text" class="form-control text-right" value="{{ $va['headcnt']}}" id="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_headcnt"  name="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_headcnt" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"  min="1" autocomplete="off" readonly required></td>
                                                                <td class="text-center"><input type="text" class="form-control text-right" value="{{ $va['daycnt']}}" id="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_daycnt"  name="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_daycnt" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"  min="1" autocomplete="off" readonly required></td>
                                                                <td class="text-center"><input type="text" class="form-control text-right" value="{{ $va['hourcnt']}}" id="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_hourcnt"  name="{{ $va['yerly']}}_{{ $va['mon']}}_{{ $va['type']}}_hourcnt" onkeyup="this.value=this.value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')"  min="1" autocomplete="off" readonly required></td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>


                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
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

    function datechange() {
            $("#qlink").attr("href","/admin/each_training_all/"+pad($("#startYear").val(),3)+"/"+pad($("#startMonth").val(),2)+"/query");
    }

    function modify() {
        var typearr=['1','2','3','14','A','B','C'];
        var fieldarr=['termcnt','headcnt','daycnt','hourcnt'];
        typearr.forEach(function(type) {
            fieldarr.forEach(function(field) {
                $("#"+$("#startYear").val()+"_"+$("#startMonth").val()+"_"+type+"_"+field).attr("readonly",false);
            });
        });
        document.getElementById("dvsave").style.visibility="visible";
    }

    function pad(num, size) {
        var s = num+"";
        while (s.length < size) s = "0" + s;
        return s;
    }

</script>
@endsection