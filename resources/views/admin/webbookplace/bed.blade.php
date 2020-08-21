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
            @if (!empty($data))
                {!! Form::open([ 'method'=>'put', 'url'=>'', 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'route'=>array("webbook.bed.post"), 'id'=>'form']) !!}
                <input type="hidden" name="applyno" value="{{$place[0]['applyno']}}">
                <input type="hidden" name="startdate" value="{{$place[0]['startdate']}}">
                <input type="hidden" name="enddate" value="{{$place[0]['enddate']}}">
                <input type="hidden" name="croomclsno" value="{{$place[0]['croomclsno']}}">
                <input type="hidden" name="applykind" value="{{$book_info[0]['applykind']}}">
                <input type="hidden" name="id" value="{{$book_info[0]['id']}}">
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">{{ $title }}</h3></div>
                        <div class="card-body pt-4">
                           
                            <div class="col-md-12">
                               <table>
                                   <tr>
                                       <th>借用場地：</th>
                                       <td><span style="color:white">{{$book_info[0]["croomclsfullname"]}}</span></td>
                                       <td>　　</td>
                                       <th>借用間數：</th>
                                       <td><span style="color:white">{{$book_info[0]['placenum']}}</span></td>
                                    </tr>
                                    <tr>
                                        <th>使用日期（起）：</th>
                                        <td><span style="color:white">{{$book_info[0]['startdate']}}</span></td>
                                        <td>　　</td>
                                        <th>使用日期（迄）：</th>
                                        <td><span style="color:white">{{$book_info[0]['enddate']}}</span></td>
                                    </tr>
                                    <tr>
                                        <th>借用時間（起）：</th>
                                        <td><span style="color:white">{{$book_info[0]['timestartname']}}時</span></td>
                                        <td>　　</td>
                                        <th>借用時間（起）：</th>
                                        <td><span style="color:white">{{$book_info[0]['timeendname']}}時</span></td>
                                    </tr>
                                    <?php if(empty($list2)){?>
                                    <tr>
                                        <th>活動人數：</th>
                                        <td><span style="color:white">{{$book_info[0]['num']}}</span></td>
                                    </tr>
                                    <tr>
                                        <th>男住宿人數：</th>
                                        <td><span style="color:white">{{$book_info[0]['mstay']}}</span></td>
                                        <td>　　</td>
                                        <th>男住宿人數：</th>
                                        <td><span style="color:white">{{$book_info[0]['fstay']}}</span></td>
                                    </tr>
                                    <?php } ?>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered mt-5">
                                    <thead>
                                        <?php if(empty($list2)) {?>
                                        <tr>
                                            <th><input type="checkbox" onclick="checkAll(this);">借出</th>
                                            <th>住宿人性別</th>
                                            <th>使用狀況</th>
                                            <th>場地名稱</th>
                                            <th>樓別</th>
                                            <th>寢室編號</th>
                                            <th>床數</th>
                                        </tr>
                                        <?php }else{?>
                                        <tr>
                                            <th><input type="checkbox" onclick="checkAll2(this);">借出</th>
                                            <th>使用狀況</th>
                                            <th>場地名稱</th>
                                            <th>教室名稱</th>
                                        </tr>
                                        <?php } ?>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($list)){ ?>
                                            <?php foreach($list as $row){?>
                                            <tr>
                                                <?php if(isset($row["sel"])){?>
                                                <td>
                                                    <input type="checkbox" name="checkboxs[]" checked value="{{$row['bedroom']}}_{{$row['floorno']}}_{{$row['bedamount']}}">
                                                    <!--<input type="hidden" name="bedrooms[]" value="{{$row['bedroom']}}">
                                                    <input type="hidden" name="floornos[]" value="{{$row['floorno']}}">
                                                    <input type="hidden" name="bedamounts[]" value="{{$row['bedamount']}}">-->
                                                </td>
                                                <?php }else{?>
                                                <td>
                                                    <input type="checkbox" name="checkboxs[]" value="{{$row['bedroom']}}_{{$row['floorno']}}_{{$row['bedamount']}}">
                                                    <!--<input type="hidden" name="bedrooms[]" value="{{$row['bedroom']}}">
                                                    <input type="hidden" name="floornos[]" value="{{$row['floorno']}}">
                                                    <input type="hidden" name="bedamounts[]" value="{{$row['bedamount']}}">-->
                                                </td>
                                                <?php } ?>
                                                <td>
                                                    <select class="custom-select" name="{{$row["bedroom"]}}" id="sexs[]">
                                                        <option value="1">男</option>
                                                        <option value="2">女</option>
                                                    </select>
                                                </td>
                                                <td>{{$row["usestatusname"]}}</td>
                                                <td>{{$row["croomclsfullname"]}}</td>
                                                <td>{{$row["floorno"]}}</td>
                                                <td>{{$row["roomname"]}}</td>
                                                <td>{{$row["bedamount"]}}</td>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>

                                        <?php if(!empty($list2)){?>
                                            <?php foreach($list2 as $row2){?>
                                                <?php if ($row2['usestatus']==0):?>
                                                <tr>
                                                    <?php if(isset($row2["sel"])){?>
                                                    <td><input type="checkbox" name="roomno[]" checked value="{{$row2['roomno']}}"></td>
                                                    <?php }else{?>
                                                    <td><input type="checkbox"  name="roomno[]" value="{{$row2['roomno']}}" ></td>
                                                    <?php } ?>
                                                    
                                                    <td>{{$row2["usestatusname"]}}</td>
                                                    <td>{{$row2["croomclsfullname"]}}</td>
                                                    <td>{{$row2["roomname"]}}</td>
                                                    <input type="hidden" name="timestart" value="{{$place[0]['timestart']}}">
                                                    <input type="hidden" name="timeend" value="{{$place[0]['timeend']}}">
                                                </tr>
                                                <?php endif;?>
                                                <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        @if ($edu_loanplace[0]["status"] == 'T' || $edu_loanplace[0]["locked"] == '1')
                        @else
                        <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @endif
                        <!-- <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button> -->
                         
                        <a href="{{route('webbook.edit.get',$place[0]['applyno'])}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 取消</button>
                        </a>
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

function checkAll(source) {
    checkboxes = document.getElementsByName('checkboxs[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}

function checkAll2(source) {
    checkboxes = document.getElementsByName('roomno[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>
@endsection