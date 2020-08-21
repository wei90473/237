@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'method';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教學方法處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/method" class="text-info">教學方法處理列表</a></li>
                        <li class="active">教學方法處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/method/'.$queryData->class.$queryData->term, 'id'=>'form']) !!}
            <input type="hidden" name="class" value="{{ $queryData->class }}">
            <input type="hidden" name="term" value="{{ $queryData->term }}">
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">教學教法編輯</h3></div>
                    <div class="card-body pt-4">
                        <fieldset style="border:groove; padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-3 ">班號：{{$queryData->class}}</label>
                                <label class="col-sm-2 ">期別：{{$queryData->term}}</label>
                                <label class="col-sm-4 ">辦班院區：{{ config('app.branch.'.$queryData->branch) }}</label>                                
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">班別名稱：{{$queryData->name}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">分班名稱：</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班別類型：{{ config('app.process.'.$queryData->process) }}</label>
                                <label class="col-sm-4 ">班務人員：{{$queryData->username}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">受訓期間：{{$queryData->sdate}}～{{$queryData->edate}}</label>
                            </div>
                        </fieldset>    
                        <?php $config = $base->getMethodlist($queryData->class);
                        //$config = (mb_substr($queryData->class, 0, 3, 'utf-8') > 107)? config('app.method_method_new') : config('app.method_method_old');?>
                        <!-- 篩選 -->
                        <div class="form-group row pt-2">
                            <label class="col-md-2 col-form-label text-md-right">篩選：</label>
                            <div class="col-md-4">
                                <select id="filter" name="filter" class="browser-default custom-select">
                                    @foreach(config('app.filter') as $key => $va)
                                        <option  value="{{ $key }}" {{ old('filter', (isset($data->filter))? $data->filter : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <button type="button" class="btn btn-primary" onclick="search()">查詢</button>
                        </div>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>不列入調查</th>
                                    <th>日期</th>
                                    <th>時間</th>
                                    <th>課程名稱</th>
                                    <th>講座</th>
                                    <th>教學教法一</th>
                                    <th>教學教法二</th>
                                    <th>教學教法三</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                @foreach($data as $va)
                                    <tr>
                                        <!-- 修改 -->
                                        <td align="center" >
                                            <input type="checkbox" id="{{ 'mark'.$va->course.$va->idno }}" name="{{ 'mark'.$va->course.$va->idno }}"  style="zoom:180%;" value="Y" {{ old('mark', (isset($va->mark))? $va->mark : '') == 'Y'? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $va->date }}</td>
                                        <td>{{ $va->stime }}～{{ $va->etime }}</td>
                                        <td>{{ $va->name }}</td>
                                        <td>{{ $va->cname }}</td>
                                        <!-- 教學教法一 -->
                                        <td>
                                            <select id="{{ 'method1'.$va->course.$va->idno }}" name="{{ 'method1'.$va->course.$va->idno }}" class="select2 form-control select2-single input-max">
                                                <option value="">無</option>
                                                @foreach($config as $key => $value)
                                                    <option value="{{ $value['method'] }}" {{ old('method1', (isset($va->method1))? $va->method1 : NULL) == $value['method']? 'selected' : '' }}>{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!-- 教學教法二 -->
                                        <td>
                                            <select id="{{ 'method2'.$va->course.$va->idno }}" name="{{ 'method2'.$va->course.$va->idno }}" class="select2 form-control select2-single input-max">
                                                <option value="">無</option>
                                                @foreach($config as $key => $value)
                                                    <option value="{{ $value['method'] }}" {{ old('method2', (isset($va->method2))? $va->method2 : NULL) == $value['method']? 'selected' : '' }}>{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <!-- 教學教法三 -->
                                        <td>
                                            <select id="{{ 'method3'.$va->course.$va->idno }}" name="{{ 'method3'.$va->course.$va->idno }}" class="select2 form-control select2-single input-max">
                                                <option value="">無</option>
                                                @foreach($config as $key => $value)
                                                    <option value="{{ $value['method'] }}" {{ old('method3', (isset($va->method3))? $va->method3 : NULL) == $value['method']? 'selected' : '' }}>{{ $value['name'] }}</option>
                                                @endforeach
                                            </select>
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
                    <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                    <a href="/admin/method">
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                    </a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection

@section('js')
    <script>
    // 送出前檢查比重
    function verification(result) {
        var course =<?php echo isset($data)?count($data):'0' ; ?>;
        var base = 0;
        var method1 = '';
        var method2 = '';
        var method3 = '';
        for(i=1;i<course;i++){
            base = (i-1)*3;
            method1 = $('.select2-selection__rendered')[base].title;
            method2 = $('.select2-selection__rendered')[base+1].title;
            method3 = $('.select2-selection__rendered')[base+2].title;
            if(method1 !="無"){
                if (method1 == method2 || method1 == method3) {
                    swal('錯誤，教學方法重複');
                    return false;
                }    
            }

            if(method2 !="無"){
                if (method2 == method3 ) {
                    swal('錯誤，教學方法重複');
                    return false;
                }    
            }
        }
        return result;
    }

    function search(){
        var filter = $('#filter').val();
        var id = String('<?=$queryData->class.$queryData->term?>');
        var url = '/admin/method/'+id+'/edit?filter='+filter;
        window.location.href = url;
    }
    </script>
@endsection