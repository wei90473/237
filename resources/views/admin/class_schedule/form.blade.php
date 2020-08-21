@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">課程表處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_schedule" class="text-info">課程表處理列表</a></li>
                        <li class="active">課程表處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_schedule/'.$queryData->class.$queryData->term, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/class_schedule/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">課程表處理</h3></div>
                    <div class="card-body pt-4">
                        <input type="hidden" name="class" value="{{ $queryData->class }}">
                        <input type="hidden" name="term" value="{{ $queryData->term }}">    
                        <fieldset style="border:groove; padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-3 ">班號：{{$queryData->class}}</label>
                                <label class="col-sm-2 ">期別：{{$queryData->term}}</label>
                                <label class="col-sm-4 ">辦班院區：{{ config('app.branch.'.$queryData->branch) }}</label>                                
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">訓練班別：{{$queryData->name}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">分班名稱：{{$queryData->branchname}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班別類型：{{ config('app.process.'.$queryData->process) }}</label>
                                <label class="col-sm-4 ">班務人員：{{$queryData->username}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">起迄期間：{{$queryData->sdate}}～{{$queryData->edate}}</label>
                            </div>
                        </fieldset> 
                        <!-- 課程表 -->
                        <div class="card-body form-group row">
                            <div class="float-md-left" id="Scale10"  >
                                <a href="/admin/class_schedule/calendar/{{ $queryData->class.$queryData->term }}" name="Scale10"  style="display: flex;">
                                    <button type="button" class="btn btn-primary btn-sm mb-3" style=" margin-right:10px;">課程表</button>
                                </a>
                                <a href="/admin/class_schedule/calendar/{{ $queryData->class.$queryData->term }}?Scale=5" name="Scale5"  style="display: none;">
                                    <button type="button" class="btn btn-primary btn-sm mb-3" style=" margin-right:10px;">課程表</button>
                                </a>
                            </div>
                            <!-- 調整主教室 -->
                            <div class="float-md-left">
                                <a href="/admin/class_schedule/siteedit/{{ $queryData->class.$queryData->term }}">
                                    <button type="button" class="btn btn-primary btn-sm mb-3" style=" margin-right:10px;">調整主教室</button>
                                </a>
                            </div>
                               
                            <!-- 網頁公告 -->
                            <div class="float-md-left">
                                <a href="/admin/class_schedule/publishedit/{{ $queryData->class.$queryData->term }}" >
                                    <button type="button" class="btn btn-primary btn-sm mb-3" style=" margin-right:10px;">網頁公告</button>
                                </a>
                            </div>
                            <!-- 前往開辦中研習班期** -->
                            <div class="float-md-left">
                                <a href="#">
                                    <span>前往開辦中研習班期</span>
                                </a>
                            </div>
                            <!-- 顯示刻度 -->
                            <div class="float-md-left" style="padding-left: 20px;">
                                <span>顯示刻度</span>
                                <input type="radio" id="Scale" name="Scale" value="10" onclick="selectScale(10)" checked>十分
                                <input type="radio" id="Scale" name="Scale" value="5"  onclick="selectScale(5)">五分
                            </div>
                        </div>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>分割</th>
                                    <th>日期</th>
                                    <th>時間</th>
                                    <th>課程名稱</th>
                                    <th>時數</th>
                                    <th>講座</th>
                                    <th>教室</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                @foreach($data as $va)
                                    <tr>
                                        <!-- 修改 -->
                                        <td align="center" >
                                            <a href="/admin/class_schedule/{{ $queryData->class.$queryData->term.$va->course }}/classedit?Scale=5" name="Scale5" style="display: none;">
                                                <i class="fa fa-pencil">編輯</i>
                                            </a>
                                            <a href="/admin/class_schedule/{{ $queryData->class.$queryData->term.$va->course }}/classedit?Scale=10" name="Scale10" style="display: flex;">
                                                <i class="fa fa-pencil">編輯</i>
                                            </a>
                                        </td>
                                        <td>
                                             <!-- 分割課程 -->
                                                <span onclick="$('#cutting_form').attr('action', '/admin/class_schedule/cuttingedit/{{ $queryData->class.$queryData->term.$va->course }}');" data-toggle="modal" data-target="#cutting_modol">
                                                    <a class="waves-effect waves-light tooltips">
                                                        <i class="fa fa-pencil">分割課程</i>
                                                    </a>
                                                </span>
                                        </td>
                                        <td>{{ $va->date }}</td>
                                        <td>{{ $va->stime }}～{{ $va->etime }}</td>
                                        <td>{{ $va->name }}</td>
                                        <td>{{ $va->hour }}</td>
                                        <td>{{ $va->cname }}</td>
                                        <td>{{ isset($va->sitename)?$va->sitename:$va->location }}</td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <!-- 備註 -->
                        <div class="form-group row pt-2">
                            <label class="col-md-1 col-form-label text-md-right">備註</label>
                            <div class="col-md-11">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="remark" id="remark">{{ old('remark', (isset($remark->remark))? $remark->remark : '') }}</textarea>
                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/class_schedule">
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
    <div id="cutting_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
    </div>
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