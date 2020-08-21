@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'itineracy_surveylogin';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">巡迴研習</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/itineracy_surveylogin" class="text-info">巡迴研習需求調查登錄</a></li>
                        <li class="active">填報資料</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">填報資料</h3></div>
                    <div class="card-body pt-4">
                        <!-- 年度 -->
                        <div class="form-group row">
                            <label class="col-sm-1 control-label pt-2">年度<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <input type="text" class="form-control number-input-max" id="yerly" name="yerly" value="{{ old('yerly', (isset($queryData['yerly']))? $queryData['yerly'] : 109) }}" readonly>
                            </div>
                            <!-- 期別 -->
                            <label class="col-sm-1 control-label text-md pt-2">期別<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="term" name="term" min="1" max="9" placeholder="請輸入期別" value="{{ old('term', (isset($queryData['term']))? $queryData['term'] : 1) }}" readonly>
                                </div>
                            </div>
                            <!-- 巡迴計畫名稱 -->
                            <label class="col-sm-2 control-label pt-2">巡迴計畫名稱<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <input type="text" id="name" name="name" class="form-control" autocomplete="off"  placeholder="請輸入計畫名稱" value="{{ old('name', (isset($queryData['name']))? $queryData['name'] : '') }}" readonly>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center" width="80">功能</th>
                                    <th>縣市別</th>
                                    <th>擬辦日期</th>
                                    <th>擬辦天數</th>
                                    <th>聯絡人</th>
                                    <th>連絡電話</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                @foreach($data as $va)
                                    <tr>
                                        <!-- 修改 -->
                                        <td class="text-center">
                                            <a href="/admin/itineracy_surveylogin/list/edit/{{ $va->yerly.$va->term.$va->city }}">
                                                <i class="fa fa-pencil">編輯</i>
                                            </a>
                                        </td>
                                        <td>{{ config('app.city.'.$va->city) }}</td>
                                        <td>{{ $va->presetdate }}</td>
                                        <td>{{ $va->day }}</td>
                                        <td>{{ $va->sponsor }}</td>
                                        <td>{{ $va->phone1 }}</td>
                                        <td>
                                            <a href="/admin/itineracy_surveylogin/print/{{ $va->id }}">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">日程表列印</button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="/admin/itineracy_surveylogin?yerly={{$queryData['yerly']}}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
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