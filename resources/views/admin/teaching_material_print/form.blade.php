@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teaching_material_print';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teaching_material_print" class="text-info">教材交印資料處理</a></li>
                        <li class="active">教材交印資料處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">教材交印資料處理</h3></div>
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
                        <div class="card-body form-group row">
                            <!-- 新增 -->
                            <div class="float-md-left">
                                <a href="/admin/teaching_material_print/edit/{{ $queryData->class.$queryData->term }}" >
                                    <button type="button" class="btn btn-primary btn-sm mb-3" style=" margin-right:10px;">新增</button>
                                </a>
                            </div>
                        </div>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>功能</th>
                                    <th>教材名稱</th>
                                    <th>總份數</th>
                                    <th>申請單位</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($data))
                                <?php $list = $base->getSponsor(); ?>
                                @foreach($data as $va)
                                    <tr>
                                        <!-- 修改 -->
                                        <td align="center" >
                                            <a href="/admin/teaching_material_print/edit/{{ $queryData->class.$queryData->term.$va['serno'] }}">
                                                <i class="fa fa-pencil">編輯</i>
                                            </a>
                                            
                                        </td>
                                        <td>{{ $va['material'] }}</td>
                                        <td>{{ $va['copy'] }}</td>
                                        <td>{{ $list[$va['applicant']] }}</td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <!-- <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button> -->
                        <a href="/admin/teaching_material_print">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection

<script type="text/javascript">
    
    
</script>