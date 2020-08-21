@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'leave';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學員請假處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學員請假處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員請假處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px; ">
                                        訓練班別：{{ $t04tb->t01tb->name }}<br>
                                        期別：{{ $t04tb->term }}<br>
                                        分班名稱：<br>
                                        班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                                        委訓機關：{{ $t04tb->client }}<br>
                                        起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                                        班務人員：
                                    </div>                                
                                    {!! Form::open(['method' => 'post', 'id' => 'suspend_form', 'onsubmit' => 'return suspend_form_submit()']) !!}
                                    <div>
                                        <label>停班課種類：</label>
                                        <input type="radio" name="suspend_type" value="all" onclick="allSuspend()" checked required>整班停班課
                                        <input type="radio" name="suspend_type" value="part" onclick="partSuspend()" required>部份停班課
                                    </div>
                                    <div>
                                        <label>請假時數(整班停班課):</label>
                                        <input type="text" name="hour" style="width:70px;">
                                    </div>                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th width="70">選取</th>
                                                <th>學號</th>
                                                <th>姓名</th>
                                                <th>機關</th>
                                                <th width="200">日期(起)</th>
                                                <th width="200">時間(起)</th>
                                                <th width="200">日期(迄)</th>
                                                <th width="200">時間(迄)</th>
                                                <th width="70">請假時數</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($t13tbs as $key => $t13tb)
                                                <tr>
                                                    <td class="text-center"><input type="checkbox" class="suspend" name="suspend[{{$t13tb->idno}}]" onChange="selectStudent(this.dataset.idno)" data-no='{{$t13tb->no}}' data-idno='{{$t13tb->idno}}' disabled></td>
                                                    <td>{{ $t13tb->no }}</td>
                                                    <td>{{ $t13tb->m02tb->cname }}</td>
                                                    <td>{{ $t13tb->m02tb->dept }}</td>
                                                    <td>
                                                        <div class="input-group">
                                                            {{ Form::text("suspend_info[{$t13tb->idno}][sdate]", null, ['id' => "sdate_{$key}", 'class' => 'form-control sdate', 'autocomplete' => 'off', 'disabled' => true]) }}
                                                            <span class="input-group-addon sdate_datepicker" style="cursor: pointer;height:calc(2.25rem + 2px);" data-key="{{$key}}"><i class="fa fa-calendar"></i></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="suspend_info[{{$t13tb->idno}}][stime]" class="form-control stimepicker" id="stimepicker_{{$key}}" disabled>
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            {{ Form::text("suspend_info[{$t13tb->idno}][edate]", null, ['id' => "edate_{$key}", 'class' => 'form-control edate', 'autocomplete' => 'off',  'disabled' => true]) }}
                                                            <span class="input-group-addon edate_datepicker" style="cursor: pointer;height:calc(2.25rem + 2px);" data-key="{{$key}}"><i class="fa fa-calendar"></i></span>
                                                        </div>                                                       
                                                    
                                                    </td>
                                                    <td>
                                                        <input type="text" name="suspend_info[{{$t13tb->idno}}][etime]" class="form-control etimepicker" id="etimepicker_{{$key}}" disabled>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="suspend_info[{{$t13tb->idno}}][hour]" class="form-control hour" disabled>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                    {!! Form::close() !!}    
                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->

                        <div class="card-footer">
                            <div>
                                <button type="button" class="btn btn-primary" onclick="$('#suspend_form').submit()">儲存</button>
                                <a href="/admin/leave/{{$t04tb->class}}/{{$t04tb->term}}"><button class="btn btn-danger">取消</button></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<!-- <script src="http://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>
<link href="http://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/> -->

    <script>
        var datepicker_num = {{ count($t13tbs) }};
        $(document).ready(function(){

            $('.sdate_datepicker').click(function(datepicker){
                $("#sdate_" + $(this)[0].dataset.key).focus();
            }); 
            $('.edate_datepicker').click(function(datepicker){
                $("#edate_" + $(this)[0].dataset.key).focus();
            });                        
            for(var i=0; i<=datepicker_num; i++){
                $("#sdate_" + i).datepicker({
                    format: "twymmdd",
                    language: 'zh-TW'
                });
                $("#edate_" + i).datepicker({
                    format: "twymmdd",
                    language: 'zh-TW'
                });   
               
                $('#stimepicker_' + i).timepicker({
                    maxHours: 24,
                    showMeridian: false,
                    minuteStep: 1,
                    defaultTime: false
                });                
                $('#etimepicker_' + i).timepicker({
                    maxHours: 24,
                    showMeridian: false,
                    minuteStep: 1,
                    defaultTime: false
                });                

            }
        });


        function allSuspend()
        {
            $(".sdate").attr("disabled", true);
            $(".edate").attr("disabled", true);
            $(".sdate_datepicker").attr("disabled", true);
            $(".edate_datepicker").attr("disabled", true);
            $(".stimepicker").attr("disabled", true);
            $(".etimepicker").attr("disabled", true);
            $('.suspend').attr("disabled", true);  
            $('.hour').attr("disabled", true); 
            $('input[name=hour]').attr("disabled", false); 
            $('.suspend').prop("checked", false);  
        }

        function partSuspend()
        {
            $('.suspend').attr("disabled", false);     
            $('input[name=hour]').attr("disabled", true);       
        }

        function selectStudent(idno)
        {
            if ($("input[name=suspend_type]:checked").val() == "part"){
                let disabled = !$("input[name='suspend[" + idno + "]']")[0].checked;
                $("input[name='suspend_info[" + idno + "][sdate]']").attr('disabled', disabled);
                $("input[name='suspend_info[" + idno + "][edate]']").attr('disabled', disabled);
                $("input[name='suspend_info[" + idno + "][stime]']").attr('disabled', disabled);
                $("input[name='suspend_info[" + idno + "][etime]']").attr('disabled', disabled);
                $("input[name='suspend_info[" + idno + "][hour]']").attr('disabled', disabled);
            }
        }

        function suspend_form_submit()
        {
            let suspend_checked = $(".suspend:checked");

            if ($("input[name=suspend_type]:checked").val() == "part"){
                for(let i=0; i<=suspend_checked.length; i++){

                    let no = suspend_checked[0].dataset.no;
                    let idno = suspend_checked[0].dataset.idno;

                    if ($("input[name='suspend_info[" + idno + "][sdate]']").val() == ''){
                        alert("學號 " + no  + " 請輸入日期(起)");
                        return false;
                    }
                    if ($("input[name='suspend_info[" + idno + "][edate]']").val() == ''){
                        alert("學號 " + no  + " 請輸入日期(迄)");
                        return false;
                    }

                    if (
                        parseInt($("input[name='suspend_info[" + idno + "][sdate]']").val()) > 
                        parseInt($("input[name='suspend_info[" + idno + "][edate]']").val())
                       ){
                        alert("學號" + no + " 日期(起) 不能大於 日期(迄)");
                        return false;
                    }
                
                    if ($("input[name='suspend_info[" + idno + "][hour]']").val() == ''){
                        alert("學號 " + no  + " 請輸入時數");
                        return false;
                    }


                    // if ($("input[name='suspend_info[" + no + "][stime]']").val() == ''){
                    //     alert("學號 " + no  + "請輸入時間(起)");
                    //     return false;
                    // }
                    // if ($("input[name='suspend_info[" + no + "][etime]']").val() == ''){
                    //     alert("學號 " + no  + "請輸入時間(迄)");
                    //     return false;
                    // }

                }
            }else if ($("input[name=suspend_type]:checked").val() == "all"){
                if ($("input[name=hour]").val() == ""){
                    alert("請輸入請假時數");
                    return false;                    
                }
            }
            return true;
        }
    </script>
@endsection