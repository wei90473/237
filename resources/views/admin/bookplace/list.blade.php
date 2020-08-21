@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
  

    <?php $_menu = 'bookplace';?>
    <style>
        .custom-select {
            display: inline-block;
            width: 10%;
            height: calc(2.25rem + 2px);
            padding: .375rem 1.75rem .375rem .75rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            vertical-align: middle;
            background: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3e%3cpath fill='%23343a40' d='M2 0L0 2h4zm0 5L0 3h4z'/%3e%3c/svg%3e) no-repeat right .75rem center/8px 10px;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        .halfArea {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0px 25px;
        }
        .item_con label {
            cursor: pointer;
        }
        .item_check.active, .item_uncheck.active {
            background-color: #d2f1ff;
        }
       
    </style>
    <div class="content">
        <div class="container-fluid">
            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                <h4 class="pull-left page-title">場地預約處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地預約處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地預約處理</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div style="float:right">
                                        <a href="/admin/bookplace/batchVerify"><button class="btn btn-info" type="button">批次修改場地</button></a>
                                        <!--<button class="btn btn-info" type="button">場地釋出註記</button>-->
                                        <button class="btn btn-info" type="button" id="set_seat_button">修改座位</button>
                                    </div>
                                    <!--認證時數資料匯出-->
                                    <form id="form_two"  action="/admin/bookplace/index" method="post">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="classroom" value="{{ $classroom==''?'':$classroom[0] }}">
                                    
                                        <div class="input-group col-6">
                                            <label class="mt-1 mr-2">課程起始日期：</label>
                                            <?php 
                                                $search_html = '';
                                                if(!isset($sdate)){
                                                    $sdate=='';
                                                }
                                                if(!isset($edate)){
                                                    $edate=='';
                                                }
                                            ?>
                                            <input type="text" id="sdate3" name="sdate3" class="form-control" autocomplete="off" value={{$sdate}} >
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                            <span class="mt-1 ml-2 mr-2">到</span>
                                            
                                            <input type="text" id="edate3" name="edate3" class="form-control" autocomplete="off" value={{$edate}}>
                                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                        </div>
                                        <input type="hidden" id="final_sdate2" name="final_sdate2">
                                        <input type="hidden" id="final_edate2" name="final_edate2">
                                        <input type="hidden" name="site" >
                                        <div class="input-group col-md-4 mt-3">
                                            <?php
                                                // $select_1='';$select_2='';
                                                // if(isset($branch)){
                                                //     switch($branch){
                                                //         case 1:
                                                //             $select_1='selected';
                                                //             break;
                                                //         case 2:
                                                //             $select_2='selected';
                                                //             break;
                                                //         default:
                                                //             break;
                                                //     }
                                                // }
                                            ?>
                                            <!-- 辦班院區 -->
                                            <label>院區：</label>
                                            <select class="browser-default custom-select" name="branch" onchange="getsitelist()">
                                                <!-- <option value="0">全部</option> -->
                                                @foreach(config('app.branch') as $key => $va)
                                                    <option value="{{ $key }}" {{ (isset($branch)?$branch:'' )== $key? 'selected' : '' }}>{{ $va }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="input-group col-md-4 mt-3">
                                        <!-- 辦班院區 -->
                                        <label>搜尋：</label>
                                        <input type="search" class="light-table-filter" data-table="order-table" placeholder="請輸入場地關鍵字">
                                        </div>
                                        <!-- 場地 -->
                                        <div class="modal-body">
                                       
                                            <div class="col-md-12" id="course_div" style='display: flex;'>
                                                
                                                <!-- 未選取的課程  class="checkbox"-->
                                                <div style="flex:1;">
                                                    <span>場地</span>
                                                    <div class="halfArea" style="flex:1;height:200px;/*max-width:400px*/;overflow:auto;">
                                                        <table style="width:100%;" class="order-table">
                                                            <tbody id="orgchk_uncheckList">
                                                            <?php if($classroom !=''){
                                                                $branchname = $branch==1?'台北':'南投';
                                                                $html = '<tr class="item_con orgchk_item item_uncheck">
                                                                <th>代碼  </th>
                                                                <th>院區  </th>
                                                                <th>場地  </th>
                                                                </tr>';
                                                                $search_html = '<tr class="item_con orgchk_item item_check">
                                                                <th>代碼  </th>
                                                                <th>院區  </th>
                                                                <th>場地  </th>
                                                                </tr>';
                                                                foreach ($sitelist as $key => $value) {
                                                                    $html .= '<tr  class="item_con orgchk_item item_uncheck" onclick="selectItem(this, \'orgchk\')" value="'.$value['site'].'" name="'.$value['site'].'"><td>'.$value['site'].'</td><td>'.$branchname.'</td><td>'.$value['name'].'</td></tr>';
                                                                }
                                                                foreach($result as $key=>$row){
                                                                    $search_html .= '<tr  class="item_con orgchk_item item_check" onclick="selectItem(this, \'orgchk\')" value="'.$key.'" name="'.$key.'"><td>'.$key.'</td><td>'.$branchname.'</td><td>'.$row['name'].'</td></tr>';
                                                                }
                                                            echo  $html;
                                                            } ?>
                                                            </tbody>  
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="arrow_con">
                                                    <button class="btn btn-primary" onclick="changeItem(true, 'orgchk')" style="margin-bottom:10px;"  type="button"><i class="fas fa-arrow-right" style="margin-right:3px;"></i>新增</button>
                                                    <button class="btn btn-danger" onclick="changeItem(false, 'orgchk')"  type="button"><i class="fas fa-arrow-left" style="margin-right:3px;"></i>移除</button>

                                                </div>
                                                <div style="flex:1;">
                                                    <span>已選取欄位</span>
                                                    <div class="halfArea" style="flex:1;height:200px;/*max-width:400px*/;overflow:auto;">
                                                        <table style="width:100%;">
                                                            <tbody id="classroom" name="classroom[]">
                                                            <?=$search_html?>    
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-group col-md-6 mt-3">
                                            <!-- <label>場地：</label>
                                            <select class="custom-select" id="classroom" name="classroom">
                                            
                                            </select> -->
                                            <!-- <button type="button" id="but" class="btn btn-light" onclick="add_room(this);">...</button> -->
                                            <button type="button" class="btn btn-info" onclick="search()">查詢</button>

                                        </div>
                                        
                                    </form>

                                    <form id="list-form" method="post">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="table-responsive">
                                            <?php if($sdate!='' && $edate!=''){?>
                                            <table class="table table-bordered table-condensed">
                                                <head>
                                                    <tr class="text-center">
                                                        <td width="6%">場地</td>
                                                        <?php 
                                                        $s=str_replace("/","-",$sdate);
                                                        $s_y=substr($s,0,3)+1911;
                                                        $s=$s_y.substr($s,3,3).substr($s,6,3);
                                                        $s=strtotime($s);
                                                        $e=str_replace("/","-",$edate);
                                                        $e_y=substr($e,0,3)+1911;
                                                        $e=$e_y.substr($e,3,3).substr($e,6,3);
                                                        $e=strtotime($e);
                                                        $diff_time=0;
                                                        if($sdate!=''&&$edate!=''){
                                                            $diff_time=($e-$s)/(60*60*24)+1;
                                                        } 
                                                        for($i=0;$i<$diff_time;$i++){
                                                            echo '<td>'.date("Y/m/d",$s+($i*60*60*24)).'</td>';
                                                        } ?>
                                                    </tr>
                                                </head>
                                                <tbody>
                                                    <?php 
                                                    if( empty($result) ){
                                                        echo '<tr><td colspan="'.($i+1).'">查無資料</td></tr>';
                                                    }else{  
                                                        foreach($result as $key=>$row){
                                                            $html = '<tr><td rowspan="3">'.$row['name'].'</td>';
                                                            $timelist = array('A'=>'上午','B'=>'下午','C'=>'晚上');
                                                            for($times = 0;$times<3;$times++){
                                                                for($j=0;$j<$diff_time;$j++){
                                                                    $t=date("Ymd",$s+($j*60*60*24));
                                                                    $temp_year=substr($t,0,4)-1911;
                                                                    $final_t=$temp_year.substr($t,4,2).substr($t,6,2);
                                                                    if($row[$final_t]['is_holiday'] ==1){
                                                                        $arr='site='.$key.'&date='.$final_t.'&type=1&time='.key($timelist).'&branch='.$branch.'&action=post';
                                                                        $html .='<td bgcolor="#EDEDED">'.current($timelist).'<br><br><a href="'.route('bookplace_form',$arr).'"  target="_blank"><button type="button" value="'.$final_t.'_'.$key.'_1" class="btn-custom">可預約</button></a></td>';
                                                                    }elseif($row[$final_t][key($timelist)]==''){
                                                                        $arr='site='.$key.'&date='.$final_t.'&type=1&time=A'.'&branch='.$branch.'&action=post';
                                                                        $html .='<td>'.current($timelist).'<br><br><a href="'.route('bookplace_form',$arr).'"  target="_blank"><button type="button" value="'.$final_t.'_'.$key.'_1" class="btn btn-light">可預約</button></a></td>';
                                                                    }else{
                                                                        $arr=['site'=>$key,'date'=>$final_t,'class'=>$row[$final_t][key($timelist)]["class"],'term'=>$row[$final_t][key($timelist)]['term'],'time'=>$row[$final_t][key($timelist)]['time'],'stime'=>$row[$final_t][key($timelist)]["stime"],'etime'=>$row[$final_t][key($timelist)]["etime"],"branch"=>$branch,"action"=>"edit"];
                                                                        if($row[$final_t][key($timelist)]["time"]=='E' || $row[$final_t][key($timelist)]["time"]=='F' || $row[$final_t][key($timelist)]["time"]=='D'){
                                                                            $color='green';
                                                                        }else{
                                                                            $color='#317eeb';
                                                                        }
                                                                        $class = isset($row[$final_t][key($timelist)]["class"])? $row[$final_t][key($timelist)]["class"]:'';
                                                                        $html .='<td bgcolor="'.$color.'"><font color="white">'.current($timelist).'</font><br><br><a style="color:white;" href="'.route('bookplace_form',$arr).'" target="_blank">已預約('.$class.')</a></td>';
                                                                    } 
                                                                } 
                                                                $html .='</tr><tr>';
                                                                next($timelist);
                                                            }    
                                                            echo $html;
                                                        }
                                                    }?>
                                                </tbody>
                                            </table>
                                            <?php }?>
                                        </div>
                                    </form>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="set_seattype" class="modal inmodal fade" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <form method="post" action="{{route('seattype_update')}}" id="send_modal">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                                <div class="row">
                                    <div class="col-md-12 pt-4">
                                        <!-- 日期 -->
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label text-md-right pt-2">班別</label>
                                            <div class="col-sm-4">
                                                <select id="class" name="class" class="select2 select2-single input-max" onchange="classChange()" required>
                                                    @foreach($classList as $key => $va)
                                                    <option value="{{ $va->class }}"
                                                        {{ old('class', (isset($arr["class"]))? $arr["class"] : 1) == $va->class? 'selected' : '' }}>
                                                        {{ $va->class }} {{ $va->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <!--場地-->
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label text-md-right pt-2">期別</label>
                                            <div class="col-sm-4">
                                                <!-- <select id="term" name="term" class="select2  select2-single input-max" onchange="getCourse();" required> -->
                                                <select id="term" name="term" class="select2  select2-single input-max" required>
                                                </select>
                                            </div>
                                        </div>

                                        <!--時段-->
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label text-md-right pt-2">座位方式</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="seattype" id="seattype">
                                                    <option value="A">A 標準型</option>
                                                    <option value="B">B 馬蹄型</option>
                                                    <option value="C">C T型</option>
                                                    <option value="D">D 菱型</option>
                                                    <option value="E">E 期他</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="btn" form="send_modal">儲存</button>
                            <button class="btn" type="button" class="close" data-dismiss="modal">取消</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
    
    function search(){
        if($('#sdate3').val()=='' || $('#edate3').val()==''){
            alert('日期不可為空!');
            return false;
        }else if($('#sdate3').val() > $('#edate3').val()){
            alert('日期格式錯誤!');
            return false;
        }else{
            let sitearray = '';
            for(let i=1; i<$(".orgchk_item.item_check").length; i++) {
                sitearray += $(".orgchk_item.item_check").eq(i).find('td').html() + ',';
            }

            $("input[name=site]").val(sitearray);
            // if(sitearray ==''){
            //     alert('您尚未選擇任一場地');
            // }else{
                $("#form_two").submit();
            // }
            
        }
    }

    // function select_action()
    // {
    //     $("#classroom").find('option').remove();
    //     $("#classroom2").val("");
    //     if(choose=='part'){
    //         $("#classroom option[value='part']").remove();
    //     }
    // }
    function filterFunction() {
        var input, filter, ul, li, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        div = document.getElementById("myDropdown");
        a = div.getElementsByTagName("a");
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
            a[i].style.display = "";
            } else {
            a[i].style.display = "none";
            }
        }
    }

    function _ajax(type) //取得場地
    {
        if(type < 3){
            var url="/admin/bookplace/getPlace/"+type;
            $("#orgchk_uncheckList").html('<a>Loading...</a>');
            $("#classroom").html('<tr class="item_con orgchk_item item_check">\
                                            <th>代碼  </th>\
                                            <th>院區  </th>\
                                            <th>場地</th>\
                                        </tr>');
        }else{
            alert('執行錯誤，請重新整理');
            return false;
        }
        var listHTML ='';
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: "POST", //傳送方式
            url: url, //傳送目的地
            data: {classroom: $('input[name=classroom]').val()},
            dataType: "json", //資料格式
            success: function(data) {
                console.log(data);
                $.each(data, function (key, value) {
                    var branch = '';
                    if(value.branch=='1'){
                        branch = '台北';
                    }else{
                        branch = '南投';
                    }

                    listHTML += '<tr  class="item_con orgchk_item item_uncheck" onclick="selectItem(this, \'orgchk\')" value="'+value.site+'" name="'+value.site+'"><td>'+value.site+'</td><td>'+branch+'</td><td>'+value.name+'</td></tr>';
                });
                $("#orgchk_uncheckList").html('<tr class="item_con orgchk_item item_uncheck">\
                        <th>代碼  </th>\
                        <th>院區  </th>\
                        <th>場地</th>\
                    </tr>'+listHTML);
                // $.each(data, function (i, data) {
                //     $('#classroom').append($('<option>', { 
                //         value: data.site,
                //         text : data.name 
                //     }));
                // });
            },
            error: function(data) {
                console.log(data);
                console.log('error');
            }
        });
    }
    // 點選項目
    let leftCon; // lef 宣告leftCon
    let rightCon;// lef 宣告rightCon
    let leftCons = [];
    let rightCons = [];
    function selectItem(e, name) {
     
        let classname = name+"_item";

        if($(e).hasClass("active")) { //取消選取
            $(e).removeClass("active");

            if($(e).hasClass("item_uncheck")) { //左_未選取
                var pos = leftCons.indexOf($(e).index());
                leftCons.splice(pos, 1);
            }else { //右_已選取
                var pos = rightCons.indexOf($(e).index());
                rightCons.splice(pos, 1);
            }
        } else { //選取
            // $("."+classname).removeClass("active");
            $(e).addClass('active');
            if($(e).hasClass("item_uncheck")) {
                leftCons.push($(e).index());
            }else {
                rightCons.push($(e).index());
            }
        }

        // if($(e).hasClass("item_uncheck")) {
        //     leftCon += ($(e).index()+',');
        //     //rightCon = -1;
        // }
        // else {
        //     rightCon += ($(e).index()+',');
        //     //leftCon = -1;
        // }
    }


    // $(function selectItem(e, name) {

    // $('table').on('click', 'tr', function(e, name){

    // if($(e).find('td:eq(0) input').is(':checked')){


     
    // $(e).find('td:eq(0) input').prop('checked',false);

    // }else{

    // $(e).find('td:eq(0) input').prop('checked',true);

    // }

    // })

    // });
    // 左右換項目
    function changeItem(type, name) {
        let classname = name+"_item";
        let countIndex = 0;
        if(!type) {      // 移除項目
  
            if(rightCons.length == 0) {
                return;
            }
            // rightCon = rightCon.replace('undefined', '');           
            // let rightCons_all = rightCon.split(',');
            // let rightCons = Array.from(new Set(rightCons_all));
            var rightConObj = [];
            countIndex = $("."+classname+".item_uncheck").length; //取得左邊目前的數字
    
            rightCons.forEach(function(value){   
                console.log(value);  
                if(value==''){
                    return true;
                } 
                let b = $("."+classname+".item_check").eq(value);
                rightConObj.push(b);               
            });
            rightCons = [];
            rightConObj.forEach(function(value){   
                console.log("countIndex:"+countIndex);  
                value.addClass("item_uncheck");
                value.removeClass("item_check");
                value.removeClass("active");
                value.find('input').prop("checked", false);
                $("."+classname+".item_uncheck").eq(countIndex-1).after(value);
                countIndex++;
         
            });
            rightCon = ''; 
            // leftCon = countIndex;

        }
        else {      // 新增項目
            if(leftCons.length == 0) {
                return;
            }
            // leftCon = leftCon.replace('undefined', '');       
            // console.log(leftCon);      
            // let leftCons_all = leftCon.split(',');
            // let leftCons = Array.from(new Set(leftCons_all));
            console.log(leftCons);
            var leftConObj = [];
            countIndex = $("."+classname+".item_check").length; //取得右邊目前的數字
    
            leftCons.forEach(function(value){   
                console.log(value);  
                if(value==''){
                    return true;
                } 
                let b = $("."+classname+".item_uncheck").eq(value);
                leftConObj.push(b);               
            });
            leftCons = [];
            leftConObj.forEach(function(value){   
                console.log("countIndex:"+countIndex);  
                value.addClass("item_check");
                value.removeClass("item_uncheck");
                value.removeClass("active");
                value.find('input').prop("checked", true);
                $("."+classname+".item_check").eq(countIndex).after(value);
                countIndex++;         
            });
            leftCon = '';
            // rightCon = countIndex;    
        }
    }
    

    // function select_output(savefield)
    // {
    //     //alert(savefield);
    //     var c2=$("#classroom2").val();
    //     var c2_arr=c2.split(",");
    //     if(c2_arr.length>1){
    //         $('#classroom').prepend($('<option>', { 
    //             value: "part",
    //             text : "部分場地" 
    //         }));
    //         $("#classroom")[0].selectedIndex = 0; ; 
    //     }
    // }

    function add_room(obj)
    {
        var iHeight=(window.screen.availHeight)*0.6;
        var iWidth=(window.screen.availWidth)*0.3;
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2; 
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2; 
        var action_value=obj.value;
        if(!action_value){
            return false;
        }
        var classroom2=$("#classroom2").val();
        //alert(classroom2);
        window.open("/admin/bookplace/addClassroom/"+action_value,"set_column", 'height=' + iHeight + ',innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
    }
    
    
    //檢查輸入時間
    function checkdate()
    {
        var sdate=$("#sdate3").val();
        var edate=$("#edate3").val();

        if(sdate>edate || sdate=='' ||edate==''){
            return false;
        }else{
            return true;
        }
    }

    //認證時數清除
    function clear_condition()
    {   
        document.all.sdate3.value = " ";
        document.all.edate3.value = " ";
        document.all.final_sdate2.value = " ";
        document.all.final_edate2.value = " ";
        //alert('why');
    }

    function batch_verify()
    {
        window.open("/admin/bookplace/batchVerify");
    }

    // 取得期別
    function classChange()
    {
        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/training_survey/getterm',
            data: { classes: $('#class').val(), selected: ''},
            success: function(data){
                $('#term').html(data);
                $("#term").trigger("change");
            },
            error: function() {
                alert('Ajax Error');
            }
        });
    }
    // 取得課程
    // function getCourse()
    // {
    //     $.ajax({
    //         type: "post",
    //         headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    //         dataType: "html",
    //         url: '/admin/training_process/getcourse',
    //         data: { classes: $('#class').val(), term: $('#term').val()},
    //         success: function(data){
    //             $('#course_div').html(data);
    //         },
    //         error: function() {
    //             alert('Ajax Error');
    //         }
    //     });
    // }
    // 取得場地清單
    function getsitelist(){
        var branch = $('select[name=branch]').val();
        console.log(branch);
        if(branch < 3){
            _ajax(branch);
            $("#but").val(branch);
            // select_action();
        }else{
            $("#but").val('');
            // select_action();
        }
    }
    $(document).ready(function() {
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

        
        $("#set_seat_button").click(function(){
            $('#set_seattype').modal('show');
        });
        if($('input[name=classroom]').val()==''){
            getsitelist();
        }
        classChange();

        
    });

    (function(document) {
  'use strict';

  // 建立 LightTableFilter
  var LightTableFilter = (function(Arr) {

    var _input;

    // 資料輸入事件處理函數
    function _onInputEvent(e) {
      _input = e.target;
      var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
      Arr.forEach.call(tables, function(table) {
        Arr.forEach.call(table.tBodies, function(tbody) {
          Arr.forEach.call(tbody.rows, _filter);
        });
      });
    }

    // 資料篩選函數，顯示包含關鍵字的列，其餘隱藏
    function _filter(row) {
      var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
      row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
    }

    return {
      // 初始化函數
      init: function() {
        var inputs = document.getElementsByClassName('light-table-filter');
        Arr.forEach.call(inputs, function(input) {
          input.oninput = _onInputEvent;
        });
      }
    };
  })(Array.prototype);

  // 網頁載入完成後，啟動 LightTableFilter
  document.addEventListener('readystatechange', function() {
    if (document.readyState === 'complete') {
      LightTableFilter.init();
    }
  });

})(document);
</script>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

