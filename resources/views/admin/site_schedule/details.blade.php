@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">排程明細</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/site_schedule">洽借場地班期排程處理</a></li>
                        <li class="active">排程明細</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>排程明細</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">										
										    <!-- 年度 -->
                                            <input type="hidden" id="yerly" name="yerly" value="{{ $queryData['yerly'] }}">
                                            <!-- 月份 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">月份</span>
                                                    </div>
                                                    <select class="form-control select2 " name="smonth">
                                                        <option value="">請選擇</option>    
                                                    @for($i=1;$i<13;$i++)
                                                        <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}" {{ $queryData['smonth'] == str_pad($i,2,'0',STR_PAD_LEFT)? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                    </select>
                                                    <label for=""> ～ </label>
                                                    <select class="form-control select2 " name="emonth">
                                                        <option value="">請選擇</option>    
                                                    @for($i=1;$i<13;$i++)
                                                        <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}" {{ $queryData['emonth'] == str_pad($i,2,'0',STR_PAD_LEFT)? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                    </select>
                                                    <div class="input-group-prepend ml-3">
                                                        <span class="input-group-text">顯示刻度：</span>
                                                        <input type="radio" name="weekday" class="mt-2" value="1" style="min-width:20px; margin-left:5px;" {{ old('weekday', (isset($queryData['weekday']))? $queryData['weekday']: '') == '1'? 'Checked' : '' }}>週
                                                        <input type="radio" name="weekday" class="mt-2" value="2" style="min-width:20px; margin-left:5px;" {{ old('weekday', (isset($queryData['weekday']))? $queryData['weekday']: '') == '2'? 'Checked' : '' }}>天
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                        <br/>
                                        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/site_schedule/', 'id'=>'form']) !!}
                                            <!-- 年度 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別</span>
                                                    </div>
                                                    <div class="input-group-prepend">
                                                        <select class="form-control select2 " name="class" id="class" onchange="getTerms(this.value);geturl();">
                                                            @foreach($list as $va)
                                                                <option value="{{ $va['class'] }}"  >{{ $va['class'].'_'. $va['name'] }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- <select class="select2 form-control select2-single input-max" name=""></select> -->
                                                    <div class="input-group-prepend ml-2">
                                                        <span class="input-group-text">期數</span>
                                                    </div>
                                                    <div class="input-group-prepend">
                                                        <select class="form-control select2 " name="term" id="term" onchange="geturl()">
                                                            @for($i=1;$i<9;$i++)
                                                                <option value="{{ str_pad($i,2,'0',STR_PAD_LEFT) }}">{{ str_pad($i,2,'0',STR_PAD_LEFT) }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <!-- <select class="select2 form-control select2-single input-max" name=""></select> -->
                                                    <div class="input-group-prepend ml-2">
                                                        <span class="input-group-text">開課日期</span>
                                                    </div>
                                                    <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off" value=""  onchange="getedate()">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                    <label for="">～</label>
                                                    <input type="text" id="edate" class="form-control" autocomplete="off" value="系統計算" disabled>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-pencil"></i>修改</button>
                                        {!! Form::close() !!}
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0 text-center">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" width="70">班號</th>
                                                    <th width="300">班別</th>
                                                    <th>期數</th>
                                                    <th>人次</th>
                                                    <th colspan="48">{{$queryData['yerly']}}年</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><!-- 月份 -->
                                                    <td rowspan="2">日期</td>
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2"></td>
                                                    @for($i = 1;$i < 13;$i++)
                                                    <td colspan="{{$queryData['month'][$i]}}">{{$i}}月</td>
                                                    @endfor
                                                </tr>
                                                <tr><!-- 第幾週 日使用for動態產生 -->
                                                    @for($j=0;$j < count($queryData['day']);$j++)
                                                    <td>{{$queryData['day'][$j]}}</td>
                                                    @endfor
                                                    
                                                </tr>
                                                <tr><!-- 合計 -->
                                                    <td>合計</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{$queryData['sumquota']['all']}}</td>
                                                    @for($j=0;$j < count($queryData['day']);$j++)
                                                    <td>{{isset($queryData['sumquota'][$j])? $queryData['sumquota'][$j]:'' }}</td>
                                                    @endfor
                                                </tr>
                                                <!-- 班 -->
                                                <?php $rule = 0; ?>
                                                @foreach($data as $va)
                                                <tr>
                                                    <td>{{ $va->class }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->quota }}</td>
                                                    @for($j=0;$j < count($queryData['day']);$j++)
                                                        @if($va->days == $j)
                                                            <?php $rule = ($va->days == $va->enddays)?0:1; ?>
                                                            <td style="background-color:blue">{{ $va->quota }}</td>
                                                        @elseif($rule=='1' && $va->enddays != $j)
                                                            <td style="background-color:blue"></td>
                                                        @elseif($rule=='1' && $va->enddays == $j)
                                                            <?php $rule = 0; ?>
                                                            <td style="background-color:blue"></td>
                                                        @else
                                                            <td></td>
                                                        @endif
                                                    @endfor
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="/admin/site_schedule">
                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                </a>
            </div>

        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
    function getTerms(class_no){
        $.ajax({
            url: "/admin/schedule/getTerms/" + class_no
        }).done(function(response) {
            console.log(response);
            var select_term = $("select[name=term]");
            select_term.html("");
            for(var i = 0; i<response.terms.length; i++){
                select_term.append("<option value='"+ response.terms[i] +"'>" + response.terms[i] + "</option>");
            }
        });  
    }
    function getedate(){
    var sdate = $("#sdate").val();
    var _class = $("select[name=class]").val();
    console.log(_class);
    $.ajax({
        url:'/admin/site_schedule/getedate',
        type:'get',
        data:{_class:_class,sdate:sdate},
        dataType:'json',
        success:function(data){
            console.log(data);
            if(data.status == '0'){
               $("#edate").val(data.msg); 
               return false; 
               
            }else{
                alert(data.msg);
                $("#edate").val(''); 
                return false; 
            }
        }
    });
}
    function geturl(){
        var getclass = $("#class").val();
        var getterm = $("#term").val();
        $('#form').attr('action','/admin/site_schedule/'+getclass+getterm);
    }
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
           
        geturl();
    });


    

</script>