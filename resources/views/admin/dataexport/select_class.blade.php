@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts2')
@section('content')

    <?php $_menu = 'dataexport';?>

    <div class="content">
        <div class="container-fluid">
            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>挑選班期</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                    </div>

                                    <form methd="post" action="/admin/dataexport/select_class" id="form">
                                        <div class="search-float">
                                            <div class="float-md mobile-100 row mr-1 mb-3">      
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班號</label>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control">
                                                </div>  
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <label class="input-group-text">班別名稱</label>
                                                    </div>
                                                <input type="text" name="class_name" id="class_name" class="form-control">
                                            </div> 
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <button type="button" onclick="clean()" class="btn mobile-100 mb-3 mb-md-0">重設條件</button>                                                                                                                                                                                                                   
                                        </div>
                                        </div> 
                                    </form>

                                    <!--<form>-->
                                    {{csrf_field()}}
                                        <!--班期資料-->
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%"><input type="checkbox" id="checkAll" onclick="toggle(this)"></th>
                                                        <th class="text-center">班號</th>
                                                        <th class="text-center">班別名稱</th>
                                                        <th class="text-center">期別</th>
                                                        <th class="text-center">起訖日期</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row) {?>
                                                    <tr class="text-center">
                                                        <td><input type="checkbox" name="checkbox[]" value="{{$row->class}}_{{$row->name}}_{{$row->term}}"></td>
                                                        <td>{{$row->class}}</td>
                                                        <td>{{$row->name}}</td>
                                                        <td>{{$row->term}}</td>
                                                        <td>{{$row->sdate}}~{{$row->edate}}</td>
                                                    </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- 分頁 -->
                                        @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])

                                        <div class="col-md-12 row" style="margin-top:1%">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0" onclick="return submit();">送出</button>
                                        </div>
                                    <!--</form>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });
        $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });
        $("#sdate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#sdate2").focus();
        });
        $("#edate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#edate2").focus();
        });
        $("#sdate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#sdate3").focus();
        });
        $("#edate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#edate3").focus();
        });

        
    });

    function toggle(source) {
        checkboxes = document.getElementsByName('checkbox[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function clean()
    {
        var empty='';
        $("#class").val(empty);
        $("#class_name").val(empty);
        $("#form").submit();
    }
    

    function submit()
    {   
        var cbxVehicle = new Array();
        $('input:checkbox:checked[name="checkbox[]"]').each(function(i) { cbxVehicle[i] = this.value; });

        if(cbxVehicle.length==0){
            alert('尚未選擇班期');
            return false;
        }

        <?php
            echo 'window.opener.document.getElementById("' . $savefield . '").value = cbxVehicle;';
            echo 'window.opener.select_class("' . $savefield . '");';
        ?>
        window.close();
    }
    
    </script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

