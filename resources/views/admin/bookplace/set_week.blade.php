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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>設定週期</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label text-md-right">起日</label>
                                        <div class="input-group col-6">
                                            <input type="text" id="sdate3" class="form-control" autocomplete="off">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>       

                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label text-md-right">迄日</label>
                                        <div class="input-group col-6">
                                            <input type="text" id="edate3" class="form-control" autocomplete="off">
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label text-md-right">重複日期</label>
                                        <div class="mt-2 ml-2">
                                            <span><input type="checkbox" name="week[]" value="0">日</span>
                                            <span><input type="checkbox" name="week[]" value="1">一</span>
                                            <span><input type="checkbox" name="week[]" value="2">二</span>
                                            <span><input type="checkbox" name="week[]" value="3">三</span>
                                            <span><input type="checkbox" name="week[]" value="4">四</span>
                                            <span><input type="checkbox" name="week[]" value="5">五</span>
                                            <span><input type="checkbox" name="week[]" value="6">六</span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="select_output();">確認</button>
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
        $("#sdate3").datepicker({
            format: "twy/mm/dd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#sdate3").focus();
        });
        $("#edate3").datepicker({
            format: "twy/mm/dd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#edate3").focus();
        });
    });

    function select_output()
    {
        var cbxVehicle = new Array();
        $('input:checkbox:checked[name="week[]"]').each(function(i) { cbxVehicle[i] = this.value; });
        cbxVehicle[cbxVehicle.length]=$("#sdate3").val();
        cbxVehicle[cbxVehicle.length]=$("#edate3").val();
        console.log(cbxVehicle);
        <?php
            echo 'window.opener.document.getElementById("' . $savefield . '").value = cbxVehicle;';
            //echo 'window.opener.select_output("' . $savefield . '");';
        ?>
        window.close();
    }

    
 
</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

