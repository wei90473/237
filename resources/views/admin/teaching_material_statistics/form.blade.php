@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teaching_material_statistics';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teaching_material_statistics" class="text-info">教材印製統計處理</a></li>
                        <li class="active">教材印製統計處理</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">教材印製統計處理</h3></div>
                    <div class="card-body pt-4">
                        <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                        <input type="hidden" name="term" value="{{ $queryData['term'] }}">    
                        <fieldset style="border:groove; padding: inherit">
                            <div class="form-group row">
                                <label class="col-sm-3 ">班號：{{$queryData['class']}}</label>
                                <label class="col-sm-2 ">期別：{{$queryData['term']}}</label>
                                <label class="col-sm-4 ">辦班院區：{{ config('app.branch.'.$data->branch) }}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">訓練班別：{{$data->name}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">分班名稱：{{$data->branchname}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 ">班別類型：{{ config('app.process.'.$data->process) }}</label>
                                <label class="col-sm-4 ">班務人員：{{$data->username}}</label>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-10 ">起迄期間：{{$data->sdate}}～{{$data->edate}}</label>
                            </div>
                        </fieldset>
                        <form method="get" id="search_form"> 
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">預定交貨月份</label>
                                <div class="col-md-2">
                                    <input type="text" id="duedate" name="duedate" class="form-control" autocomplete="off" placeholder="YYYMM" value="{{ isset($queryData['duedate'])?$queryData['duedate']:'' }}">
                                </div>
                                <label class="col-form-label">支付月份</label>
                                <div class="col-md-2">
                                    <input type="text" id="paiddate" name="paiddate" class="form-control" autocomplete="off" placeholder="YYYMM" value="{{ isset($queryData['paiddate'])?$queryData['paiddate']:'' }}">
                                </div>
                                <div class="col-md-4">
                                <label class="col-form-label">選項</label>
                                    <input type="radio"  name="ispaid" value="1" {{ isset($queryData['ispaid'])?($queryData['ispaid']=='1'?'checked':''):'checked' }}>全部
                                    <input type="radio"  name="ispaid" value="2" {{ isset($queryData['ispaid'])?($queryData['ispaid']=='2'?'checked':''):'' }}>未支付
                                    <input type="radio"  name="ispaid" value="3" {{ isset($queryData['ispaid'])?($queryData['ispaid']=='3'?'checked':''):'' }}>已支付
                                </div>
                                <button type="submit" class="btn btn-primary">查詢</button>
                            </div>
                        </form>
                        <!-- 課程清單 -->
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                <tr>
                                    <th>支付</th>
                                    <th>編號</th>
                                    <th>教材</th>
                                    <th>金額</th>
                                    <th>類別</th>
                                    <th align="center">功能</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($materialdata))
                                @foreach($materialdata as $va)
                                    <tr>
                                        <td align="center" ><input type="checkbox" {{$va['paiddate']==''?'':'checked'}} disabled></td>
                                        <td>{{ $va['serno'] }}</td>
                                        <td>{{ $va['material'] }}</td>
                                        <td>{{ $va['total'] }}</td>
                                        <td>{{ $va['accname'] }}</td>
                                        <td align="center" >
                                            <a href="/admin/teaching_material_statistics/edit/{{$queryData['class'].$queryData['term'].$va['serno']}}">
                                            <button type="button" class="btn btn-primary">編輯</button></a>
                                            <a href="/admin/teaching_material_statistics/upprice/{{$queryData['class'].$queryData['term'].$va['serno']}}">
                                            <button type="button" class="btn btn-primary">更新單價</button></a>  
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <a href="/admin/teaching_material_statistics">
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