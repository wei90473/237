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
                            <div class="row">
                                <div class="col-12">
                                    <!--<form>-->                                        
                                        <div class="col-md-10 offset-md-1 p-0">
                                            <!-- 可選取欄位 -->
                                            <span>可選取欄位</span>
                                            <span style="margin-left:40%">已選取欄位</span>
                                            <div class="form-group row">
                                                <div class="col-md-10" id="course_div" style='display: flex;'>
                                                    <?php $key = 1;?>
                                                    
                                                    <div class="halfArea" style="flex:1;height:300px;max-width:400px;overflow:auto;">
                                                        <!-- 未選取的課程  class="checkbox"-->
                                                        <div class="item_con item_uncheck" style="visibility: hidden;"></div>
                                                            @foreach($not_select as $not_select_index => $not_select_temp)
                                                            <div class="item_con item_uncheck">
                                                                <input id="course{{ $key }}" name="course[]" value="{{$not_select_index}}_{{$not_select_temp}}"  type="checkbox" hidden>
                                                                <label onclick="selectItem(this)">
                                                                    {{$not_select_temp}}
                                                                </label>
                                                            </div>
                                                            <?php $key ++;?>
                                                            @endforeach
                                                        </div>

                                                        <div class="arrow_con">
                                                            <span onclick="changeClass(true)"><i class="fas fa-arrow-right arrow" style="margin-bottom:15px">新增</i></span>
                                                            <span onclick="changeClass(false)"><i class="fas fa-arrow-left arrow">移除</i></span>
                                                        </div>

                                                        <!-- 已選取欄位-->
                                                        <div class="halfArea" style="flex:1;height:300px;max-width:400px;overflow:auto;">
                                                            <div class="item_con item_check" style="visibility: hidden;"></div>
                                                            @foreach($select as $index => $select_temp)
                                                            <div class="item_con item_check">
                                                                <input id="course{{ $key }}" name="course[]" value="{{$index}}_{{$select_temp}}" type="checkbox" checked hidden>
                                                                <label onclick="selectItem(this)">
                                                                    {{$select_temp}}
                                                                </label>
                                                            </div>
                                                            <?php $key ++;?>
                                                            @endforeach
                                                        </div>

                                                        <div class="arrow_rank">
                                                            <i class="fa fa-arrow-up pointer arrow" onclick="prev();"></i>
                                                            <i class="fa fa-arrow-down pointer arrow" onclick="next();"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if($savefield=='output_info'){?>
                                                <div class="col-md-12">
                                                    <label>匯出檔案格式:</label>
                                                    <?php
                                                        $che1='checked';
                                                        $che2='';
                                                        if($radio=='txt'){
                                                            $che1='';
                                                            $che2='checked';
                                                        }
                                                    ?>
                                                    <input type="radio" name="export" value="excel" {{$che1}}>EXCEL
                                                    <input type="radio" name="export" value="txt" {{$che2}}>文字檔
                                                </div>
                                                <?php }?>
                                            </div>
                                            <div class="card-footer">
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0" onclick="select_output();">送出</button>

                                                <!--<a href="/admin/effectiveness_survey">
                                                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                                                </a>-->
                                            </div>
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
    function select_output()
    {
        var cbxVehicle = new Array();
        $('input:checkbox:checked[name="course[]"]').each(function(i) { cbxVehicle[i] = this.value; });

        indexarr=cbxVehicle.length;
        cbxVehicle[indexarr]=$('input:radio[name=export]:checked').val();
        console.log(cbxVehicle);
        <?php
            echo 'window.opener.document.getElementById("' . $savefield . '").value = cbxVehicle;';
            echo 'window.opener.select_output("' . $savefield . '");';
        ?>
        window.close();
    }
    function prev() {
        // // 取得自己
        // e = $(e).parent().parent();
        // // 取得前一個元素
        // var prev = $(e).prev();
        // // 檢查是否有上一個元素
        // if (prev.length) {
        //     // 刪除自己
        //     $(e).remove();
        //     // 將自己新增在前一個元素前
        //     $(prev).before(e)
        // }

        $(".item_check").eq(rightCon-2).after($(".item_check").eq(rightCon));
        rightCon = rightCon-1;
    }

    // 往下移動
    function next() {
        // // 取得自己
        // e = $(e).parent().parent();
        // // 取得下一個元素
        // var prev = $(e).next();
        // // 檢查是否有下一個元素
        // if (prev.length) {
        //     // 刪除自己
        //     $(e).remove();
        //     // 將自己新增在前一個元素前
        //     $(prev).after(e)
        // }

        $(".item_check").eq(rightCon+1).after($(".item_check").eq(rightCon));
        rightCon = rightCon+1;
    }

    // 點選課程
    let leftCon;
    let rightCon;
    function selectItem(e) {
        if($(e).parent().hasClass("active")) {
            $(e).parent().removeClass("active")
        }
        else {
            // if($(e).parent().hasClass("item_check")) {
            //     for(let i=0; i<$(".item_check").length; i++) {
            //         $(".item_check").eq(i).removeClass("active");
            //     }
            // }

            for(let i=0; i<$(".item_con").length; i++) {
                $(".item_con").eq(i).removeClass("active");
            }

            $(e).parent().addClass('active');
        }

        if($(e).parent().hasClass("item_uncheck")) {
            leftCon = $(e).parent().index();
        }
        else {
            rightCon = $(e).parent().index();
        }
    }

    // 左右換課程
    function changeClass(type) {
        let countIndex = 0;
        if(!type) {      // 取消選課
            countIndex = $(".item_uncheck").length;
            let a = $(".item_check").eq(rightCon);
            a.addClass("item_uncheck");
            a.removeClass("item_check");
            a.find('input').prop("checked", false);
            $(".item_uncheck").eq(countIndex-1).after(a);
            leftCon = countIndex;
        }
        else {      // 選課
            countIndex = $(".item_check").length;
            let b = $(".item_uncheck").eq(leftCon);
            b.addClass("item_check");
            b.removeClass("item_uncheck");
            b.find('input').prop("checked", true);
            $(".item_check").eq(countIndex).after(b);
            rightCon = countIndex;      
        }
    }

    


    
</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

