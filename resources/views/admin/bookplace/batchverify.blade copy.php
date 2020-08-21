@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
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
        /*.item_con {
            display: flex;
            align-items: center;
        }
        .item_con label {
            cursor: pointer;
        }*/
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

    <?php $_menu = 'bookplace';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">批次場地修改</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/effectiveness_survey" class="text-info">批次場地修改</a></li>
                        <li class="active">批次場地修改</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) && !isset($create))
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/effectiveness_survey/'.serialize($data), 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/effectiveness_survey/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">批次場地修改</h3></div>
                    <div class="card-body pt-4">
                        <div class="row">
                        <div class="col-md-6" style="border:groove;">                                    
                            
                         <!-- 日期 -->
                         <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                            <div class="col-sm-4">
                                <div class="input-group roc-date input-max">
                                    <div class="input-group col-sm-10">
                                        <input type="text" id="sdate" name="fillsdate" class="form-control number-input-max" autocomplete="off" value="{{ old('fillsdate', (isset($data['fillsdate']))? $data['fillsdate'] : '') }}">
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="input-group roc-date input-max">
                                    <div class="input-group col-sm-10">                                        
                                        <input type="text" id="edate" name="filledate" class="form-control number-input-max" autocomplete="off"  value="{{ old('filledate', (isset($data['filledate']))? $data['filledate'] : '') }}">
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                            <div class="col-sm-4">
                                <input type="text"  class="form-control number-input-max" autocomplete="off" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                            <div class="col-sm-4">
                                <input type="text"  class="form-control number-input-max" autocomplete="off" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                            <div class="col-sm-4">
                                <input type="text"  class="form-control number-input-max" autocomplete="off" >
                            </div>
                        </div>
                        </div>

                        
                        <div class="col-md-6" style="border:groove;">                                    
                            
                         <!-- 日期 -->
                         <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">日期</label>
                            <div class="col-sm-2">
                                <div class="input-group roc-date input-max">
                                    <div class="input-group col-sm-10">
                                        <input type="text" id="sdate" name="fillsdate" class="form-control number-input-max" autocomplete="off" value="{{ old('fillsdate', (isset($data['fillsdate']))? $data['fillsdate'] : '') }}">
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="input-group roc-date input-max">
                                    <div class="input-group col-sm-10">                                        
                                        <input type="text" id="edate" name="filledate" class="form-control number-input-max" autocomplete="off"  value="{{ old('filledate', (isset($data['filledate']))? $data['filledate'] : '') }}">
                                        <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">場地</label>
                            <div class="col-sm-4">
                                <input type="text"  class="form-control number-input-max" autocomplete="off" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                            <div class="col-sm-4">
                                <input type="text"  class="form-control number-input-max" autocomplete="off" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                            <div class="col-sm-4">
                                <input type="text"  class="form-control number-input-max" autocomplete="off" >
                            </div>
                        </div>
                        </div>
                        </div>


                        <hr>
                       


                        <!-- 選取課程 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">選取課程</label><br>
                            <div class="col-md-10" id="course_div" style='display: flex;'>

                                @if(isset($data))
                                    <?php $key = 1;?>
                                    
                                <!-- 未選取的課程  class="checkbox"-->
                                <div style="flex:1;">
                                    <span>課程名稱</span>
                                    <div class="halfArea" style="flex:1;height:300px;max-width:400px;overflow:auto;">
                                        <table style="width:100%;">
                                            <tbody id="orgchk_uncheckList">
                                                <tr class="item_con orgchk_item item_uncheck">
                                                    <th>日期</th>
                                                    <th>課程名稱</th>
                                                    <th>講座</th>
                                                </tr>
                                                @foreach($courseNot as $temp_coursenot)
                                                    <tr class="item_con orgchk_item item_uncheck" onclick="selectItem(this, 'orgchk')" name="course[]">
                                                        <td>{{$temp_coursenot->date}}</td>
                                                        <td>{{$temp_coursenot->coursename}}</td>
                                                        <td>{{$temp_coursenot->cname}}</td>
                                                        <td><input hidden id="course{{ $key }}" name="course[]" value="{{ $temp_coursenot->course }}_{{ $temp_coursenot->idno }}" type="checkbox" ></td>
                                                    </tr>
                                                
                                                @endforeach
                                            </tbody>  
                                        </table>
                                    </div>
                                </div>

                                <?php if(isset($control_edit)&&$control_edit=='0') { ?>
                                    <div class="arrow_con" style="margin-right:50px">
                                        <button   type="button" style="margin-bottom:15px"><i class="fas fa-arrow-right arrow"></i>新增</button>
                                        <button   style="margin-bottom:10px;" type="button"><i class="fas fa-arrow-left arrow"></i>移除</button>
                                    </div>
                                <?php }else{?>
                                
                                <div class="arrow_con" style="margin-right:50px">
                                    <button class="btn btn-primary" onclick="changeClass(true)" type="button" style="margin-bottom:15px"><i class="fas fa-arrow-right arrow"></i>新增</button>
                                    <button class="btn btn-danger"  style="margin-bottom:10px;" type="button" onclick="changeClass(false)"><i class="fas fa-arrow-left arrow"></i>移除</button>
                                </div>

                                <?php }?>

                                <!-- 已選取的課程 class="checkbox checkbox-primary"-->
                                <div style="flex:1;">
                                    <span>已選取課程</span>
                                    <div class="halfArea" style="flex:1;height:300px;max-width:400px;overflow:auto;">
                                        <table style="width:100%;">
                                            <tbody id="test_course" name="test_course[]">
                                                <tr class="item_con orgchk_item item_check">
                                                    <th>日期</th>
                                                    <th>課程名稱</th>
                                                    <th>講座</th>
                                                </tr>
                                                @if(isset($data))
                                                <?php foreach($course as $temp) { ?>
                                                    <tr class="item_con orgchk_item item_check" onclick="selectItem(this, 'orgchk')" name="course[]" value="{{ $temp->course }}_{{ $temp->idno }}">
                                                        <td>{{$temp->date}}</td>
                                                        <td>{{$temp->coursename}}</td>
                                                        <td>{{$temp->cname}}</td>
                                                        <td><input hidden id="course{{ $key }}" name="course[]" value="{{ $temp->course }}_{{ $temp->idno }}" type="checkbox" checked ></td>
                                                    </tr>
                                                <?php $key++; } ?>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                    <?php if(isset($control_edit)&&$control_edit=='0') { ?>
                                    <div class="arrow_rank">
                                        <i class="fa fa-arrow-up pointer arrow" ></i>
                                        <i class="fa fa-arrow-down pointer arrow"></i>
                                    </div>
                                    <?php }else{?>
                                    <div class="arrow_rank">
                                        <i class="fa fa-arrow-up pointer arrow" onclick="prev();"></i>
                                        <i class="fa fa-arrow-down pointer arrow" onclick="next();"></i>
                                    </div>
                                    <?php }?>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="confirmOrgchk();submitform()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>

                        <!--刪除按鍵-->
                        @if(isset($data)&&!isset($create))
                        <span onclick="$('#del_survey_form').attr('action', '/admin/effectiveness_survey/{{serialize($data)}}');" data-toggle="modal" data-target="#del_survey_modol" >
                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash text-danger"></i>刪除</button>
                            </span>
                        </span>
                        @endif
                        
                        <a href="/admin/effectiveness_survey">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    @include('admin/layouts/list/del_modol')
    
    

@endsection

@section('js')
<script type= text/javascript>
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
</script>

<script>
    // 往上移動
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

    // 點選課程 原本的
    /*let leftCon;
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
    }*/

    // 點選項目
    let leftCon;
    let rightCon;
    function selectItem(e, name) {
        leftCon = -1;
        rightCon = -1;
        let classname = name+"_item";

        if($(e).hasClass("active")) {
            $(e).removeClass("active");
            rightCon = -1;
            leftCon = -1;
            return;
        }
        else {
            $("."+classname).removeClass("active");

            $(e).addClass('active');
        }

        if($(e).hasClass("item_uncheck")) {
            leftCon = $(e).index();
            rightCon = -1;
        }
        else {
            rightCon = $(e).index();
            leftCon = -1;
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

<script>
    

    function confirmOrgchk() {
        let classarray = '';
        let test='';
        for(let i=1; i<$(".orgchk_item.item_check").length; i++) {
            classarray += $(".orgchk_item.item_check").eq(i).find('td').html()+',';
            test +=$("input:checkbox[name=course]:checked").val();
        }
        //alert(test);
        //alert(classarray);
        $("input[name=test_course]").val(classarray);
    }
</script>
@endsection