@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

<style>
        /* .item_con {
            display: flex;
            align-items: center;
        } */
        .display_inline {
            display: inline-block;
            margin-right: 5px;
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
            margin: 0px 5px;
        }
        .item_con label {
            cursor: pointer;
        }
        .item_check.active, .item_uncheck.active {
            background-color: #d2f1ff;
        }
        .arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
    </style>
    <?php $_menu = 'webbook';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路預約場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes" class="text-info">網路預約場地審核處理</a></li>
                        <li class="active">網路預約場地審核處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ($mode =='modify')
                {!! Form::open([ 'method'=>'put', 'route'=>array("webbook.place.put",$id), 'id'=>'form']) !!}
                <input type="hidden" value="{{$applyno}}" name="applyno">
            @else
                {!! Form::open([ 'method'=>'post', 'route'=>array('webbook.place.post',$applyno), 'id'=>'form']) !!}
            @endif
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">場地管理-調整借用場地資料{!! $locked == 1?'<font style="color: red">(已安排借用場地不可調整資料)</font>':'' !!}</h3></div>
                        <div class="card-body pt-4">
                            <!-- 借用場地 借用間數-->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2"><span class="text-danger">*</span>借用場地:</label>
                                <div class="col-md-2">
                                    <select class="custom-select" name="croomclsno" id="croomclsno">
                                    @foreach ($placeList as $value => $text)
                                        <option value="{{ $text['croomclsno'] }}" {{ (isset($data[0]['croomclsno']) && $data[0]['croomclsno'] == $text['croomclsno'])? 'selected' : '' }}>{{ $text['croomclsname'] }}</option>
                                    @endforeach
                                    </select>
                                </div>

                                <label class="col-form-label text-md pl-3" ><span class="text-danger">*</span>借用間數:</label>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="placenum" id="placenum" value="{{isset($data[0]['placenum'])? $data[0]['placenum'] :0}}">
                                </div>
                            </div>
                          
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right"><span class="text-danger">*</span>借用日期(起):</label>
                                <div class="input-group  col-md-2 ">
                                    <input type="text" id="sdate3" name="startdate" value="{{ isset($data[0]['startdate'])?$data[0]['startdate']:'' }}" class="form-control" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                </div>

                                <label class="col-form-label text-md pl-3 ">借用日期(迄):</label>
                                <div class="input-group  col-md-2 ">
                                    <input type="text" id="edate3" name="enddate" value="{{ isset($data[0]['enddate'])?$data[0]['enddate']:'' }}" class="form-control" autocomplete="off">
                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right"><span class="text-danger">*</span>借用時間(起):</label>
                                <div class="col-md-2">
                                    <?php 
                                        $time=['0000','0100','0200','0300','0400','0500','0600','0700','0800','0900','1000','1100','1200','1300'
                                                ,'1400','1500','1600','1700','1800','1900','2000','2100','2200','2300'];
                                        $time_end=['0059','0159','0359','0459','0559','0659','0759','0859','0959','1059','1159','1259','1359'
                                                ,'1459','1559','1659','1759','1859','1959','2059','2159','2259','2359'];
                                        $time_index[0]='';        
                                        for($j=1;$j<=count($time_end)+1;$j++){
                                            $time_index[$j]=' ';
                                            $time_index_end[$j]=' ';
                                        }
                                        $time_index[15]='selected';
                                        $time_index_end[15]='selected';
                                        if(!empty($data[0]['timestart'])){
                                            $time_index[15]='';
                                            $time_index_end[15]='';
                                            for($i=0;$i<count($time);$i++){
                                                if($time[$i]==$data[0]["timestart"]){
                                                    $time_index[$i]='selected';
                                                }
                                            }
                                            for($k=0;$k<count($time_end);$k++){
                                                if($time_end[$k]==$data[0]["timeend"]){
                                                    $time_index_end[$k+2]='selected';
                                                }
                                            }
                                        }
                                    ?>
                                    <select class="custom-select" name="timestart" id="timestart">
                                        <option></option>
                                        <option value="0000" {{$time_index[0]}}>00</option>
                                        <option value="0100" {{$time_index[1]}}>01</option>
                                        <option value="0200" {{$time_index[2]}}>02</option>
                                        <option value="0300" {{$time_index[3]}}>03</option>
                                        <option value="0400" {{$time_index[4]}}>04</option>
                                        <option value="0500" {{$time_index[5]}}>05</option>
                                        <option value="0600" {{$time_index[6]}}>06</option>
                                        <option value="0700" {{$time_index[7]}}>07</option>
                                        <option value="0800" {{$time_index[8]}}>08</option>
                                        <option value="0900" {{$time_index[9]}}>09</option>
                                        <option value="1000" {{$time_index[10]}}>10</option>
                                        <option value="1100" {{$time_index[11]}}>11</option>
                                        <option value="1200" {{$time_index[12]}}>12</option>
                                        <option value="1300" {{$time_index[13]}}>13</option>
                                        <option value="1400" {{$time_index[14]}}>14</option>
                                        <option value="1500" {{$time_index[15]}}>15</option>
                                        <option value="1600" {{$time_index[16]}}>16</option>
                                        <option value="1700" {{$time_index[17]}}>17</option>
                                        <option value="1800" {{$time_index[18]}}>18</option>
                                        <option value="1900" {{$time_index[19]}}>19</option>
                                        <option value="2000" {{$time_index[20]}}>20</option>
                                        <option value="2100" {{$time_index[21]}}>21</option>
                                        <option value="2200" {{$time_index[22]}}>22</option>
                                        <option value="2300" {{$time_index[23]}}>23</option>
                                    </select>
                                </div>

                                <label class="col-form-label text-md pl-3 "><span class="text-danger">*</span>借用時間(迄):</label>
                                <div class="col-md-2">
                                    <select class="custom-select" name="timeend" id="timeend">
                                        <option></option>
                                        <option value="0059" {{$time_index_end[1]}}>01</option>
                                        <option value="0159" {{$time_index_end[2]}}>02</option>
                                        <option value="0259" {{$time_index_end[3]}}>03</option>
                                        <option value="0359" {{$time_index_end[4]}}>04</option>
                                        <option value="0459" {{$time_index_end[5]}}>05</option>
                                        <option value="0559" {{$time_index_end[6]}}>06</option>
                                        <option value="0659" {{$time_index_end[7]}}>07</option>
                                        <option value="0759" {{$time_index_end[8]}}>08</option>
                                        <option value="0859" {{$time_index_end[9]}}>09</option>
                                        <option value="0959" {{$time_index_end[10]}}>10</option>
                                        <option value="1059" {{$time_index_end[11]}}>11</option>
                                        <option value="1159" {{$time_index_end[12]}}>12</option>
                                        <option value="1259" {{$time_index_end[13]}}>13</option>
                                        <option value="1359" {{$time_index_end[14]}}>14</option>
                                        <option value="1459" {{$time_index_end[15]}}>15</option>
                                        <option value="1559" {{$time_index_end[16]}}>16</option>
                                        <option value="1659" {{$time_index_end[17]}}>17</option>
                                        <option value="1759" {{$time_index_end[18]}}>18</option>
                                        <option value="1859" {{$time_index_end[19]}}>19</option>
                                        <option value="1959" {{$time_index_end[20]}}>20</option>
                                        <option value="2059" {{$time_index_end[21]}}>21</option>
                                        <option value="2159" {{$time_index_end[22]}}>22</option>
                                        <option value="2259" {{$time_index_end[23]}}>23</option>
                                        <option value="2359" {{$time_index_end[24]}}>24</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <fieldset>
                                    <ul style="border:none">
                                        <legend>欄位說明</legend> 
                                        <li>上列欄位出現<span style="color:red">紅色</span>部份為必需有資料的欄位。                        
                                        </li>
                                        <li>借用教學場地時，借用日期不接受區間。                      
                                        </li>
                                        <li>借用寢室時，毋需設定「借用起始時間」、「借用截止時間」及「借用間數」，<span style="text-decoration: underline">如寢室借用日期二日(含)以上，請輸入借用日期區間。</span>                        
                                        </li>
                                        <li>借用寢室時，進房時間為借用當日下午三時，退房時間為借用翌日十二時。                       
                                        </li>
                                        <li>借用餐廳時：
                                            <p style="text-indent: 2em"><span style="color:blue">早餐</span>借用時間請輸入 07~08</p>
                                            <p style="text-indent: 2em"><span style="color:blue">中餐</span>借用時間請輸入 11~13</p>
                                            <p style="text-indent: 2em"><span style="color:blue">晚餐</span>借用時間請輸入 17~19</p>                       
                                        </li>
                                    </ul>
                                    
                                </fieldset>
                                <input type="hidden" value="0" name="delete" id="delete">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                    @if($locked == 1)
                        <a href="{{route('webbook.edit.get',$applyno)}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 取消</button>
                        </a>
                    @else
                        <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <?php if($mode=='modify'){?>
                            <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>
                        <?php }?>
                        <a href="{{route('webbook.edit.get',$applyno)}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 取消</button>
                        </a>
                    @endif
                    </div>
                </div>
            </div>
            
            {!! Form::close() !!}

        </div>
    </div>
   	  	
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
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
    
    var sdate3 = "<?= isset($data[0]['startdate'])? $data[0]['startdate'] :''?>";
    if(sdate3 != ''){
        var sdate3 = sdate3.substr(0,3)+'/'+sdate3.substr(3,2)+'/'+sdate3.substr(5,2);
    }
    var edate3 = "<?= isset($data[0]['enddate'])? $data[0]['enddate'] :''?>";
    if(edate3 != ''){
        var edate3 = edate3.substr(0,3)+'/'+edate3.substr(3,2)+'/'+edate3.substr(5,2);
    }
    $("#sdate3").val(sdate3);
    $("#edate3").val(edate3);
    
});
function deleteClass()
{
    var txt=confirm("是否要刪除?");
    if(txt==true){
        $("#delete").val(1);
        $("#form").submit();
    }
    
}
</script>
@endsection