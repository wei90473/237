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
                        <li class="active">編輯日程表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">編輯日程表</h3></div>
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
                                    <th>確認辦理日期</th>
                                    <th>實辦天數</th>
                                    <th>匯入需求調查</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                @foreach($data as $va)
                                    <tr>
                                        <!-- 修改 -->
                                        <td class="text-center">
                                            <a href="/admin/itineracy_schedule/edit/city/{{ $va->yerly.$va->term.$va->city }}">
                                                <i class="fa fa-pencil">編輯</i>
                                            </a>
                                        </td>
                                        <td>{{ config('app.city.'.$va->city) }}</td>
                                        <td>{{ $va->actualdate }}</td>
                                        <td>{{ $va->actualdays }}</td>
                                        <td>
                                            <a href="/admin/itineracy_schedule/edit/{{$va->yerly.$va->term.$va->city}}/batchimport">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">匯入需求調查</button>
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
                        <a href="/admin/itineracy_schedule">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>    
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
   
@endsection
