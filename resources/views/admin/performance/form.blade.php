@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'performance';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練績效處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/performance" class="text-info">訓練績效處理</a></li>
                        <li class="active">訓練績效處理維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($queryData) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/performance/'.$queryData->class.$queryData->term, 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">訓練績效處理維護</h3></div>
                    <div class="card-body pt-4">
                        <fieldset style="border:groove; padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-3 " name="class" value="{{$queryData['class']}}">班號：{{$queryData['class']}}</label>
                                <label class="col-sm-2 " name="term" value="{{$queryData['term']}}">期別：{{$queryData['term']}}</label>
                                <label class="col-sm-4 " id="branch" name="branch" value="{{$queryData['branch']}}">辦班院區：{{ config('app.branch.'.$queryData['branch']) }}</label>                                
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">班別名稱：{{$queryData['name']}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班務人員：{{$queryData['username']}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">開課日期：{{$queryData['sdate']}}</label>
                                <label class="col-sm-10 ">結束日期：{{$queryData['edate']}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">總天數：{{$queryData['trainday']}}</label>
                                <label class="col-sm-10 ">總時數：{{$queryData['trainhour']}}</label>
                            </div>
                        </fieldset> 
                       
                        <!-- 人數 -->
                        <div class="float-md mobile-100 row pt-3 mb-3">
                            <div class="input-group col-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">應到人數</span>
                                </div>
                                <input type="text" id="regcnt" name="regcnt" class="form-control" autocomplete="off" value="{{ $queryData['regcnt'] }}">
                            </div>
                            <div class="input-group col-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">實到人數</span>
                                </div>
                                <input type="text" id="passcnt" name="passcnt" class="form-control" autocomplete="off" value="{{ $queryData['passcnt'] }}">
                            </div>
                            <div class="input-group col-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">結業人數</span>
                                </div>
                                <input type="text" id="endcnt" name="endcnt" class="form-control" autocomplete="off" value="{{ $queryData['endcnt'] }}">
                            </div>
                        </div>
                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/performance">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    <!-- 分割課程確認視窗 -->
    <<!-- div id="cutting_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">警告</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">你確定要分割課程嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'put', 'url'=>'', 'id'=>'cutting_form' ]) !!}
                            <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                            <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div> -->
@endsection

<script type="text/javascript">
    
    function selectScale(scale) {
        if(scale==5){
            $("a[name='Scale5']").css('display','flex');
            $("a[name='Scale10']").css('display','none');
        }else{
            $("a[name='Scale5']").css('display','none');
            $("a[name='Scale10']").css('display','flex');
        }
    }
</script>