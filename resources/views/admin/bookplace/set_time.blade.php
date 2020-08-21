@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts2')
@section('content')
<style>
    .halfArea {
        padding: 5px;
        border: 1px solid #d2d6de;
        border-radius: 5px;
    }

    .arrow_con {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .arrow {
        font-size: 30px !important;
        color: #969696;
        padding: 10px;
        cursor: pointer;
    }

    .arrow:hover {
        color: #696969;
    }

    .item_con {
        display: flex;
        align-items: center;
    }

    .item_con label {
        cursor: pointer;
    }

    .item_con.active {
        background-color: #d2f1ff;
    }

    .arrow_rank {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
</style>
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
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>設定欄位</h3>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <!--上左搜尋欄-->
                                <div class="col-md-6 pt-4" style="border:groove;">
                                    <!-- 日期 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="left_site" id="left_site"
                                                value="{{$condition['left_site']}}" disabled>
                                        </div>
                                    </div>

                                    <!--場地-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="left_date" id="left_date"
                                                value="{{$condition['left_date']}}" disabled>
                                        </div>
                                    </div>

                                    <!--班別-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時段</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="left_time" id="left_time"
                                                value="{{$condition['left_time']}}" disabled>
                                        </div>
                                    </div>

                                    <!--期別-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時間</label>
                                        <div class="col-sm-4">
                                            <?php
                                            $temp=explode("_",$condition['left_setime']);
                                            //var_dump($temp);  
                                        ?>
                                            <input type="text" class="col-xs-2" size="2" name="left_stime"
                                                id="left_stime" value="{{$temp[0]}}">-
                                            <input type="text" class="col-xs-2" size="2" name="left_etime"
                                                id="left_etime" value="{{$temp[1]}}">
                                        </div>
                                    </div>
                                </div>

                                <!--上右搜尋欄-->
                                <div class="col-md-6 pt-4" style="border:groove;">
                                    <!-- 日期 -->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="right_site" id="right_site"
                                                value="{{$condition['right_site']}}" disabled>
                                        </div>
                                    </div>
                                    <!--場地-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="right_date" id="right_date"
                                                value="{{$condition['right_date']}}" disabled>
                                        </div>
                                    </div>

                                    <!--班別-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時段</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" name="right_time" id="right_time"
                                                value="{{$condition['right_time']}}" disabled>
                                        </div>
                                    </div>

                                    <!--期別-->
                                    <div class="form-group row">
                                        <label class="col-sm-2 control-label text-md-right pt-2">時間</label>
                                        <div class="col-sm-4">
                                            <?php
                                            $temp=explode("_",$condition['right_setime']);
                                            //var_dump($temp);  
                                        ?>
                                            <input type="text" class="col-xs-2" size="2" name="right_stime"
                                                id="right_stime" value="{{$temp[0]}}">-
                                            <input type="text" class="col-xs-2" size="2" name="right_etime"
                                                id="right_etime" value="{{$temp[1]}}">
                                        </div>
                                    </div>

                                </div>
                                <button class="btn btn-info" type="button" onclick="submit_form();">確定</button>
                                <button class="btn btn-danger" type="button">取消</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    function submit_form()
    {
        /*var cbxVehicle = new Array();
        $('input:checkbox:checked[name="course[]"]').each(function(i) { cbxVehicle[i] = this.value; });

        indexarr=cbxVehicle.length;
        cbxVehicle[indexarr]=$('input:radio[name=export]:checked').val();*/
        //console.log(cbxVehicle);
        <?php
            echo 'window.opener.document.getElementById("test").value = true;';
            //echo 'window.opener.test();';
        ?>        
        window.returnValue = true;
        window.close();
    }

</script>
<!-- 刪除確認視窗 -->
@include('admin/layouts/list/del_modol')

@endsection