@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/itineracy_schedule" class="text-info">實施日程表</a></li>
                        <li><a href="/admin/itineracy_schedule/edit/{{$queryData['yerly'].$queryData['term']}}" class="text-info">編輯日程表</a></li>
                        <li class="active">編輯日程表(縣市別)</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">編輯日程表(縣市別)</h3></div>
                    <div class="card-body pt-4">
                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-1 control-label pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <input type="text" class="form-control number-input-max" value="{{ old('yerly', (isset($queryData['yerly']))? $queryData['yerly'] : 109) }}" readonly>
                            </div>
                            <!-- 期別 -->
                            <label class="col-sm-1 control-label text-md pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" min="1" max="9" placeholder="請輸入期別" value="{{ old('term', (isset($queryData['term']))? $queryData['term'] : 1) }}" readonly>
                                </div>
                            </div>
                            <!-- 巡迴計畫名稱 -->
                            <label class="col-sm-2 control-label pt-2">巡迴計畫名稱<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" autocomplete="off"  placeholder="請輸入計畫名稱" value="{{ old('name', (isset($queryData['name']))? $queryData['name'] : '') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 縣市別 -->
                            <label class="col-sm-1 control-label pt-2">縣市別<span class="text-danger">*</span></label>
                            <div class="col-md-2">
                                <input type="text" class="form-control" autocomplete="off"  placeholder="請輸入縣市別" value="{{ config('app.city.'.$queryData['city']) }}" readonly>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th class="text-center" width="80">功能</th>
                                        <th>擬辦日期</th>
                                        <th>確認辦理日期</th>
                                        <th>調訓人數</th>
                                        <th>工作人員</th>
                                        <th>實施地點</th>
                                        <th>設定課程</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(isset($data))
                                    @foreach($data as $va)
                                        <tr>
                                            <!-- 修改 -->
                                            <td class="text-center">
                                                <input type="hidden" name="actualdate{{$va['class']}}" value="{{ $va['actualdate'] }}">
                                                <input type="hidden" name="quota{{$va['class']}}" value="{{ $va['quota'] }}">
                                                <input type="hidden" name="staff{{$va['class']}}" value="{{ $va['staff'] }}">
                                                <input type="hidden" name="address{{$va['class']}}" value="{{ $va['address'] }}">
                                                <input type="hidden" name="presetdate{{$va['class']}}" value="{{ $va['presetdate'] }}">
                                                <input type="hidden" name="fee{{$va['class']}}" value="{{ $va['fee'] }}">

                                                <a href="#">
                                                    <i class="fa fa-pencil" onclick="Edit({{ $va['class'] }}) ">編輯</i>
                                                </a>
                                            </td>
                                            <td>{{ $va['presetdate'] }}</td>
                                            <td>{{ $va['actualdate'] }}</td>
                                            <td>{{ $va['quota'] }}</td>
                                            <td>{{ $va['staff'] }}</td>
                                            <td>{{ $va['address'] }}</td>
                                            <td>
                                            <a href="/admin/itineracy_schedule/edit/city/settingclass/{{$va['class']}}">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">設定課程</button>
                                            </a>
                                        </td>
                                        </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="Create()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>新增</button>
                        <a href="/admin/itineracy_schedule/edit/{{$queryData['yerly'].$queryData['term']}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- 新增 -->
    <div class="modal fade" id="CreateModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'POST', 'url'=>'/admin/itineracy_schedule/edit/city', 'id'=>'form']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" id="yerly" name="yerly" value="{{ $queryData['yerly'] }}">
                            <input type="hidden" id="term" name="term" value="{{ $queryData['term'] }}">
                            <!-- 縣市別 -->
                            <label class="control-label pt-2">縣市別<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text"  class="form-control" autocomplete="off"  placeholder="請輸入縣市別" value="{{ config('app.city.'.$queryData['city']) }}" readonly>
                                <input type="hidden" id="city" name="city" value="{{ $queryData['city'] }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 確認辦理日期  -->
                            <label class="control-label pt-2">確認辦理日期<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" id="actualdate" name="actualdate" class="form-control" autocomplete="off"  placeholder="請輸入確認辦理日期" value="{{ old('actualdate', (isset($data['actualdate']))? $data['actualdate'] : '') }}" required>
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="form-group row">
                            <!-- 調訓人數 -->
                            <label class="control-label pt-2">調訓人數<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="quota" name="quota" class="form-control" autocomplete="off"  placeholder="輸入人數" value="" >
                            </div>
                            <!-- 工作人員 -->
                            <label class="control-label pt-2">工作人員<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="staff" name="staff" class="form-control" autocomplete="off"  placeholder="輸入人數" value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 鐘點費自付 -->
                            <label class="control-label pt-2">鐘點費自付<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <select id="fee" name="fee" class="browser-default custom-select">
                                    <option value="0">請選擇</option>
                                    <option value="N">否</option>
                                    <option value="Y">是</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 實施地點 -->
                            <label class="control-label pt-2">實施地點 <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="address" name="address" class="form-control" autocomplete="off"  placeholder="請輸入實施地點" value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                             <!-- 連絡電話1 -->
                            <label class="control-label pt-2">連絡電話1<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="phone1" name="phone1" class="form-control" autocomplete="off"  placeholder="請輸入連絡電話1" value="{{old('phone1', (isset($citydata['phone1']))? $citydata['phone1'] : '')  }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 連絡電話2 -->
                            <label class="control-label pt-2">連絡電話2</label>
                            <div class="col-md-8">
                                <input type="text" id="phone2" name="phone2" class="form-control" autocomplete="off"  placeholder="請輸入連絡電話2" value="{{old('phone2', (isset($citydata['phone2']))? $citydata['phone2'] : '')  }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 傳真 -->
                            <label class="control-label pt-2">傳真</label>
                            <div class="col-md-8">
                                <input type="text" id="fax" name="fax" class="form-control" autocomplete="off"  placeholder="請輸入傳真" value="{{old('fax', (isset($citydata['fax']))? $citydata['fax'] : '')  }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- EMAIL -->
                            <label class="control-label pt-2">EMAIL<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="mail" name="mail" class="form-control" autocomplete="off"  placeholder="請輸入EMAIL" value="{{old('mail', (isset($citydata['mail']))? $citydata['mail'] : '')  }}" >
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionStore()">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- 修改 -->
    <div class="modal fade" id="EditModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <!-- form start -->
                {!! Form::open([ 'method'=>'PUT', 'url'=>'/admin/itineracy_schedule/edit/city/999', 'id'=>'form2']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h4 class="modal-title">新增類別</h4> -->
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <input type="hidden" name="yerly" value="{{ $queryData['yerly'] }}">
                            <input type="hidden" name="term" value="{{ $queryData['term'] }}">
                            <input type="hidden" id="class" name="class" value="">
                            <!-- 縣市別 -->
                            <label class="control-label pt-2">縣市別<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text"  class="form-control" autocomplete="off"  placeholder="請輸入縣市別" value="{{ config('app.city.'.$queryData['city']) }}" readonly>
                                <input type="hidden" name="city" value="{{ $queryData['city'] }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 擬辦日期  -->
                            <label class="control-label pt-2">擬辦日期<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" id="presetdate" name="presetdate" class="form-control" autocomplete="off" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 確認辦理日期  -->
                            <label class="control-label pt-2">確認辦理日期<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" id="E_actualdate" name="E_actualdate" class="form-control" autocomplete="off"  placeholder="請輸入確認辦理日期" value="" required>
                            </div>
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="form-group row">
                            <!-- 調訓人數 -->
                            <label class="control-label pt-2">調訓人數<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="E_quota" name="E_quota" class="form-control" autocomplete="off"  placeholder="輸入人數" value="" >
                            </div>
                            <!-- 工作人員 -->
                            <label class="control-label pt-2">工作人員<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <input type="text" id="E_staff" name="E_staff" class="form-control" autocomplete="off"  placeholder="輸入人數" value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 鐘點費自付 -->
                            <label class="control-label pt-2">鐘點費自付<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <select id="E_fee" name="E_fee" class="browser-default custom-select">
                                    <option value="0">請選擇</option>
                                    <option value="N">否</option>
                                    <option value="Y">是</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 實施地點 -->
                            <label class="control-label pt-2">實施地點 <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="E_address" name="E_address" class="form-control" autocomplete="off"  placeholder="請輸入實施地點" value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                             <!-- 連絡電話1 -->
                            <label class="control-label pt-2">連絡電話1<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" name="phone1" class="form-control" autocomplete="off"  placeholder="請輸入連絡電話1" value="{{old('phone1', (isset($citydata['phone1']))? $citydata['phone1'] : '')  }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 連絡電話2 -->
                            <label class="control-label pt-2">連絡電話2</label>
                            <div class="col-md-8">
                                <input type="text" name="phone2" class="form-control" autocomplete="off"  placeholder="請輸入連絡電話2" value="{{old('phone2', (isset($citydata['phone2']))? $citydata['phone2'] : '')  }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 傳真 -->
                            <label class="control-label pt-2">傳真</label>
                            <div class="col-md-8">
                                <input type="text" name="fax" class="form-control" autocomplete="off"  placeholder="請輸入傳真" value="{{old('fax', (isset($citydata['fax']))? $citydata['fax'] : '')  }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- EMAIL -->
                            <label class="control-label pt-2">EMAIL<span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="E_mail" name="mail" class="form-control" autocomplete="off"  placeholder="請輸入EMAIL" value="{{old('mail', (isset($citydata['mail']))? $citydata['mail'] : '')  }}" >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="actionEdit()">修改</button>
                        <button type="button" class="btn btn-danger" onclick="actionDelete()">刪除</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>
                </div>
                {!! Form::close() !!}
                <!-- 刪除 -->

                {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/itineracy_schedule/edit/city/999', 'id'=>'deleteform']) !!}
                <input type="hidden" class="form-control " id="D_code" name="D_code"></input>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
@section('js')
<script>

    $(document).ready(function() {
            $("#actualdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker1').click(function(){
                $("#actualdate").focus();
            });
            $("#E_actualdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker2').click(function(){
                $("#E_actualdate").focus();
            });

     });
     function Create() {
        $("#actualdate").val("");
        $("#quota").val("");
        $("#staff").val("");
        $("#address").val("");
        $("#class").val("");
        $("#fee").val("");
        $("#presetdate").val("");
        $('#CreateModal').modal('show');
    };

    function Edit(code) {
        $("#class").val(code);
        var actualdate = $("input[name=actualdate"+code+"]").val();
        $("#E_actualdate").val(actualdate);
        var quota = $("input[name=quota"+code+"]").val();
        $("#E_quota").val(quota);
        var staff = $("input[name=staff"+code+"]").val();
        $("#E_staff").val(staff);
        var address = $("input[name=address"+code+"]").val();
        $("#E_address").val(address);
        var fee = $("input[name=fee"+code+"]").val();
        $("#E_fee").val(fee);
        var presetdate = $("input[name=presetdate"+code+"]").val();
        $("#presetdate").val(presetdate);
        $("#form2").attr('action', 'http://172.16.10.18/admin/itineracy_schedule/edit/city/'+code);
        $('#EditModal').modal('show');
    };

    //新增
    function actionStore(){
        // if( $("#actualdate").val()!=''){
        //     $("#form").submit();
        // }else{
        //     alert('請輸入辦理日期 !!');
        //     return ;
        // }
        if( $("#actualdate").val()==''){
            alert('請輸入辦理日期 !!');
            return false;
        }else if( $('#mail').val()==''){
            alert('請輸入EMAIL !!');
            return false;

        }else{
            $("#form").submit();
        }
    }
    //修改
    function actionEdit(){
        // if( $("#E_actualdate").val()!='' ){
        //     $("#form2").submit();
        // }else{
        //     alert('請輸入辦理日期 !!');
        //     return ;
        // }
        if( $("#E_actualdate").val()==''){
            alert('請輸入擬辦日期 !!');
            return false;
        }else if( $('#E_mail').val()==''){
            alert('請輸入EMAIL !!');
            return false;
        }else{
            $("#form2").submit();
        }
    }
    //刪除
    function actionDelete(){
        if( $("#class").val()!='' ){
            var code = $("#class").val();
            $("#deleteform").attr('action', 'http://172.16.10.18/admin/itineracy_schedule/edit/city/'+code);
            $("#deleteform").submit();
        }else{
            alert('請輸入代號名稱 !!');
            return ;
        }
    }
</script>
@endsection
