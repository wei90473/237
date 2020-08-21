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
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教材印製統計處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>教材印製統計處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 35%;" >
                                        <form method="get" id="search_form">
                                            <div class="float-md mobile-100 row mr-1 mb-3" style="width: 100%;">

                                                <div class="input-group col-8" style="display: flex;align-items:center;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">月份選項</span>
                                                    </div>
                                                    <input style="min-width:15px;margin-left: 5px;margin-right: 5px;height: 27px;" type="radio"  name="monthType" value="1" {{ isset($queryData['monthType'])?($queryData['monthType']=='1'?'checked':''):'checked' }}>預定交貨月份
                                                    <input style="min-width:15px;margin-left: 5px;margin-right: 5px;height: 27px;" type="radio"  name="monthType" value="2" {{ isset($queryData['monthType'])?($queryData['monthType']=='2'?'checked':''):'' }}>支付月份
                                                </div>

                                                <div class="input-group col-4">

                                                    <input type="text" id="date" name="date" class="form-control" autocomplete="off" placeholder="YYYMM" value="{{ isset($queryData['date'])?$queryData['date']:'' }}">
                                                </div>

                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3" style="width: 100%;">

                                                <div class="input-group col-12" style="display: flex;align-items:center;">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">選項</span>
                                                    </div>
                                                    <input style="min-width:15px;margin-left: 5px;margin-right: 5px;height: 27px;" type="radio"  name="ispaid" value="1" {{ isset($queryData['ispaid'])?($queryData['ispaid']=='1'?'checked':''):'checked' }}>全部
                                                    <input style="min-width:15px;margin-left: 5px;margin-right: 5px;height: 27px;" type="radio"  name="ispaid" value="2" {{ isset($queryData['ispaid'])?($queryData['ispaid']=='2'?'checked':''):'' }}>未支付
                                                    <input style="min-width:15px;margin-left: 5px;margin-right: 5px;height: 27px;" type="radio"  name="ispaid" value="3" {{ isset($queryData['ispaid'])?($queryData['ispaid']=='3'?'checked':''):'' }}>已支付
                                                </div>

                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <!-- 重設條件 -->
                                                <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" type="button">重設條件</button>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>支付
                                                    <span onclick="$('#pay_form').attr('action', '/admin/teaching_material_statistics/changepay/{{$allserno}}');" data-toggle="modal" data-target="#pay_modol">
                                                    <input type="checkbox" id="chkall" {{$cakall=='N'?'':'checked'}} onclick="check_all()"  >
                                                    </span>
                                                </th>
                                                <th align="center">功能</th>
                                                <th>班期/申請單位</th>
                                                <th>編號</th>
                                                <th>教材</th>
                                                <th>金額</th>
                                                <th>類別</th>
                                                <th align="center">功能</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                                <tr>
                                                    <td align="center" >
                                                        <span onclick="$('#pay_form').attr('action', '/admin/teaching_material_statistics/changepay/{{$va->serno}}');" data-toggle="modal" data-target="#pay_modol">
                                                        <input type="checkbox" name="chkall[]" {{$va->paiddate==''?'':'checked'}} >
                                                        </span>
                                                    </td>
                                                    <td align="center" >
                                                        <a href="/admin/teaching_material_statistics/edit/{{$va->serno}}?{{$_SERVER['QUERY_STRING']}}">
                                                        <button type="button" class="btn btn-primary">編輯</button></a>
                                                    </td>
                                                    <td>{{ $va->serno }}</td>
                                                    <td>{{ $va->serno }}</td>
                                                    <td>{{ $va->material }}</td>
                                                    <td>
                                                        @if(empty($va->total))
                                                        0
                                                        @else
                                                        {{ $va->total }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $va->accname }}</td>
                                                    <td align="center" >
                                                        <a href="/admin/teaching_material_statistics/upprice/{{$va->serno}}_{{$_SERVER['QUERY_STRING']}}">
                                                        <button type="button" class="btn btn-primary">更新單價</button></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($data))
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(isset($data))
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="pay_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-warning">
                        <h3 class="card-title float-left text-white">支付與否</h3>
                        <button type="button" onclick="location.reload();" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">確定要修改支付狀態嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'pay_form' ]) !!}
                        <input type="hidden" id="QUERY_STRING" name="QUERY_STRING" class="form-control" value="{{$_SERVER['QUERY_STRING']}}">
                        <input type="hidden" id="cakid" name="cakid" class="form-control" value="{{$cakid}}">
                        <input type="hidden" id="cakall" name="cakall" class="form-control" value="{{$cakall}}">
                        <button type="button" class="btn mr-2 btn-info pull-left" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')

<script type="text/javascript">

    function doClear(){
      document.all.date.value = "";
    }

    function check_all()
    {
        if($("#chkall").prop("checked")) {
             $("input[name='chkall[]']").each(function() {
                 $(this).prop("checked", true);
             });
           } else {
             $("input[name='chkall[]']").each(function() {
                 $(this).prop("checked", false);
             });
           }
    }
</script>

@endsection