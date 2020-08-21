@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_annual';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/itineracy_annual" class="text-info">年度主題設定</a></li>
                        @if ( isset($data) )
                        <li class="active">年度主題設定編輯</li>
                        @else
                        <li class="active">年度主題設定新增</li>
                        @endif
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/itineracy_annual/edit/'.$data['yerly'].$data['term'], 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/itineracy_annual/create', 'id'=>'form']) !!}
            @endif


            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    @if ( isset($data) )
                    <div class="card-header"><h3 class="card-title">年度主題設定編輯</h3></div>
                    @else
                    <div class="card-header"><h3 class="card-title">年度主題設定新增</h3></div>
                    @endif
                    <div class="card-body pt-4">
                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select class="browser-default custom-select" name="yerly">
                                @foreach($queryData['choices'] as $key => $va)
                                    <option value="{{ $key }}" {{ (isset($data['yerly'])? $data['yerly']:'') == $key? 'selected' : '' }}>{{ $va }}</option>
                                @endforeach
                                </select>
                            </div>
                            <!-- 期別 -->
                            <label class="control-label text-md pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-sm-2 md-left">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="term" name="term" min="1" max="9" placeholder="請輸入期別" value="{{ old('term', (isset($data['term']))? $data['term'] : 1) }}" required>
                                </div>
                            </div>
                            <!-- 主題數量上限 -->
                            <label class="control-label text-md pt-2">主題數量上限<span class="text-danger">*</span></label>
                            <div class="col-sm-2 md-left">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="topics" name="topics" min="1" max="99" placeholder="請輸入主題數量上限" value="{{ old('topics', (isset($data['topics']))? $data['topics'] : 1) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">開課日期<span class="text-danger">*</span></label>
                                <div class="col-md-3">
                                    <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off"  placeholder="請輸入開課日期" value="{{ old('sdate', (isset($data['sdate']))? $data['sdate'] : '') }}" required>
                                </div>
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                <label class="col-form-label text-md">結束日期<span class="text-danger">*</span></label>
                                <div class="col-md-3">
                                    <input type="text" id="edate" name="edate" class="form-control" autocomplete="off"  placeholder="請輸入結束日期" value="{{ old('edate', (isset($data['edate']))? $data['edate'] : '') }}" required>
                                </div>
                                <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">巡迴計畫名稱<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <input type="text" id="name" name="name" class="form-control" autocomplete="off"  placeholder="請輸入計畫名稱" value="{{ old('name', (isset($data['name']))? $data['name'] : '') }}" required>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="checksigninsubmit()" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))
                            <!-- <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>                          -->
                        @endif
                        <a href="/admin/itineracy_annual">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
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
    
    
    
    //檢查
    function checksigninsubmit(){
        if( $("#edate").val()=='' || $("#sdate").val()=='' ){
            alert('請填寫日期');
            return;
        }else if( $("#edate").val() < $("#sdate").val() ){
            alert('日期錯誤');
            return;
        }
        if( $("#name").val()=='' ){
            alert('請填寫名稱');
            return;
        }

        $("#form").submit();
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
            $('#datepicker2').click(function(){
                $("#edate").focus();
            });
           
     });
</script>
@endsection