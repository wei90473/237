@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'transfer_processing';?>
<style>
    .panel-heading .accordion-toggle {
        display: inline-block;
        text-align: center;
        color: rgb(255, 255, 255);
        font-size: 16px;
        box-sizing: border-box;
        line-height: 1.8em;
        vertical-align: middle;
        font-family: 微軟正黑體, "Microsoft JhengHei", Arial, Helvetica, sans-serif !important;
        background: rgb(250, 160, 90);
        padding: 5px 20px;
        border-width: initial;
        border-style: none;
        border-color: initial;
        border-image: initial;
        margin: 2px 0px;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .panel-heading .accordion-toggle:hover {
        background-color: #f79448 !important;
        border: 1px solid #f79448 !important;
            -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        opacity: 1;
    }
    .panel-heading .accordion-toggle::before {
        background-color: inherit; !important;
    }
    .panel-heading .accordion-toggle.collapsed::before {
        background-color: inherit; !important;
    }
    .search-float input,
    .search-float .select2-selection--single, .search-float select {
        min-width: initial;
    }
</style>
    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">鐘點費轉帳處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">鐘點費轉帳處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>鐘點費轉帳處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">
                                            <!-- 年度 -->

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-2">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                        </div>
                                                        <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                                <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                                                </option>
                                                            @endfor
                                                        </select>
                                                </div>
                                                <!-- 班號 -->
                                                <div class="input-group col-3">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                                <!-- 期別 -->
                                                <div class="input-group col-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="term" class="form-control" autocomplete="off" value="{{ $queryData['term'] }}">
                                                </div>
                                                <!-- 班別名稱 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                            </div>

                                            <!-- 辦班院區 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">

                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班院區</span>
                                                    </div>
                                                    <select class="form-control select2 " id="branch" name="branch">
                                                       <option value="">全部</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate" name="sdate" class="form-control" autocomplete="off" value="{{ $queryData['sdate'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="edate" name="edate" class="form-control" autocomplete="off" value="{{ $queryData['edate'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
<!-- 進階/簡易搜尋開始 -->
                                            <div class="panel-group" id="accordion">
                                            <header class="panel-heading">

                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#search"> </a>

                                                </header>
                                                <footer id="search" class="panel-collapse collapse">
<!-- 進階/簡易搜尋開始 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">上課地點</span>
                                                    </div>
                                                    <select class="form-control select2" id="sitebranch" name="sitebranch">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['sitebranch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                        <option value="3" {{ $queryData['sitebranch'] == $key? 'selected' : '' }} >外地</option>
                                                    </select>
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="class_branch_name" name="class_branch_name" class="form-control" autocomplete="off" value="{{ $queryData['class_branch_name'] }}">
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別類型</span>
                                                    </div>
                                                    <select class="form-control select2" id="process" name="process">
                                                        <option value="">全部</option>
                                                        @foreach(config('app.process') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['process'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班務人員</span>
                                                    </div>
                                                    <select class="form-control select2" id="sponsor" name="sponsor">
                                                        <option value="">請選擇</option>
                                                        <?php foreach($sponsor as $key => $row){ ?>
                                                        <option value="<?=$key;?>" <?=($queryData['sponsor'] == $key)?'selected':'';?> ><?=$row;?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 訓練性質 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練性質</span>
                                                    </div>
                                                    <select class="form-control select2" id="traintype" name="traintype">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.traintype') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['traintype'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班別性質 -->
                                                <?php $typeList = $base->getSystemCode('K')?>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別性質</span>
                                                    </div>
                                                    <select class="form-control select2" id="type" name="type">
                                                        <option value="">請選擇</option>
                                                        @foreach($typeList as $code => $va)
                                                            <option value="{{ $code }}" {{ $queryData['type'] == $code? 'selected' : '' }}>{{ $va['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- **類別1 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">類別1</span>
                                                    </div>
                                                    <?php $categoryoneList = $base->getSystemCode('M')?>
                                                    <select id="categoryone" name="categoryone" class="form-control select2">
                                                    <option value="" selected>請選擇</option>
                                                    @foreach($categoryoneList as $code => $va)
                                                        <option value="{{ $va['code'] }}" {{ $queryData['categoryone'] == $va['code']? 'selected' : '' }} >{{ $va['name'] }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate2" name="sdate2" class="form-control" autocomplete="off" value="{{ $queryData['sdate2'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="edate2" name="edate2" class="form-control" autocomplete="off" value="{{ $queryData['edate2'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓日期(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate3" name="sdate3" class="form-control" autocomplete="off" value="{{ $queryData['sdate3'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓日期(訖)</span>
                                                    </div>
                                                    <input type="text" id="edate3" name="edate3" class="form-control" autocomplete="off" value="{{ $queryData['edate3'] }}">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-2">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend" style="display:flex; align-items:center;">
                                                        <span class="input-group-text">篩選</span>
                                                        <input type="radio" id="paid" name="paid" style="min-width:20px; margin-left:5px;" value="" <?=($queryData['paid']=='')?'checked':'';?> >全部資料
                                                        <input type="radio" id="paid" name="paid" style="min-width:20px; margin-left:5px;" value="1" <?=($queryData['paid']=='1')?'checked':'';?> >已轉帳
                                                        <input type="radio" id="paid" name="paid" style="min-width:20px; margin-left:5px;" value="2" <?=($queryData['paid']=='2')?'checked':'';?> >未轉帳
                                                    </div>
                                                </div>
                                            </div>

<!-- 進階/簡易搜尋結束 -->
                                            </footer>
                                            </div>
<!-- 進階/簡易搜尋結束 -->

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button>


                                            </div>

                                        </form>
                                        <div class="float-left">
                                            &nbsp;
                                            <span onclick="$('#transfer_form').attr('action', '/admin/transfer_processing/transfer');" data-toggle="modal" data-target="#transfer_modol">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">執行轉帳</button>
                                            </span>
                                            <span onclick="$('#frmFile_form').attr('action', '/admin/transfer_processing/frmFile');" data-toggle="modal" data-target="#frmFile_modol">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">產生磁片</button>
                                            </span>
                                            <span onclick="$('#cancel_form').attr('action', '/admin/transfer_processing/cancelTransfer');" data-toggle="modal" data-target="#cancel_modol">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">執行沖銷轉帳</button>
                                            </span>
                                        </div>
                                    </div>


                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>轉存日期</th>
                                                <th>上課日期</th>
                                                <th>講座姓名</th>
                                                <th>轉帳金額</th>
                                                <th>帳號</th>
                                                <th>班別</th>
                                                <th>期別</th>
                                                <th>帳戶種類</th>
                                                <th>付款方式</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        {{ $va->paidday }}
                                                    </td>
                                                    <td class="text-center">{{ $va->date }}</td>
                                                    <td class="text-center">{{ $va->cname }}</td>
                                                    <td>{{ $va->totalpay }}</td>
                                                    <td>{{ $va->postno_bankno }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->transfor_name }}</td>
                                                    <td>
                                                        <?php if($va->paymk == '1'){ ?>
                                                        郵局轉帳
                                                        <?php } ?>
                                                        <?php if($va->paymk == '2'){ ?>
                                                        支付處付款
                                                        <?php } ?>
                                                    </td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="transfer_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" >
                <div class="card mb-0">
                    <div class="card-header bg-primary">
                        <h3 class="card-title float-left text-white">執行轉帳</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>

                    <div class="modal-footer py-2 text-center"  >
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'transfer_form', 'style'=>'width:100%;' ]) !!}

                        <div class="float-md mobile-100 row mr-1 mb-3">
                            <div class="input-group col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">院區</span>
                                </div>
                                <input type="radio" id="branch" name="branch" style="min-width:20px; margin-left:5px;" value="1" checked >台北院區
                            <input type="radio" id="branch" name="branch" style="min-width:20px; margin-left:5px;" value="2"  >南投院區
                            </div>
                        </div>
                        <div class="float-md mobile-100 row mr-1 mb-3">
                            <div class="input-group col-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">上課期間(起)</span>
                                </div>
                                <input type="text" id="date1" name="date1" class="form-control" autocomplete="off" value="" required="">
                                <span class="input-group-addon" style="cursor: pointer;" id="transfer_datepicker1"><i class="fa fa-calendar"></i></span>
                            </div>
                            <div class="input-group col-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">上課期間(訖)</span>
                                </div>
                                <input type="text" id="date2" name="date2" class="form-control" autocomplete="off" value="" required="">
                                <span class="input-group-addon" style="cursor: pointer;" id="transfer_datepicker2"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="float-md mobile-100 row mr-1 mb-3">
                            <div class="input-group col-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">轉存日期</span>
                                </div>
                                <input type="text" id="date3" name="date3" class="form-control" autocomplete="off" value="" required="">
                                <span class="input-group-addon" style="cursor: pointer;" id="transfer_datepicker3"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>

                        <div class="float-md mobile-100 row mr-1 mb-3">
                            <div class="input-group col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">請選擇</span>
                                </div>
                                <input type="radio" id="clss_kind" name="clss_kind" style="min-width:20px; margin-left:5px;" value="2"  >一般班
	                            <input type="radio" id="clss_kind" name="clss_kind" style="min-width:20px; margin-left:5px;" value="3"  >代收款班
	                            <input type="radio" id="clss_kind" name="clss_kind" style="min-width:20px; margin-left:5px;" value="1" checked >全部
                            </div>
                        </div>

                        <button type="button" class="btn mr-2 btn-info" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="button" class="btn mr-3 btn-danger" onclick="submitform();" >轉帳</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="cancel_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content" >
                <div class="card mb-0">
                    <div class="card-header bg-primary">
                        <h3 class="card-title float-left text-white">執行沖銷轉帳</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>

                    <div class="modal-footer py-2 text-center"  >
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'cancel_form' , 'style'=>'width:100%;' ]) !!}


                        <div class="float-md mobile-100 row mr-1 mb-3" >
                            <div class="input-group col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">轉存日期</span>
                                </div>
                                <input type="text" id="date6" name="date6" class="form-control" autocomplete="off" value="" required="">
                                <span class="input-group-addon" style="cursor: pointer;" id="transfer_datepicker6"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>


                        <button type="button" class="btn mr-2 btn-info" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="button" class="btn mr-3 btn-danger" onclick="submitform3();" >確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="frmFile_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content" >
                <div class="card mb-0">
                    <div class="card-header bg-primary">
                        <h3 class="card-title float-left text-white">產生磁片</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>

                    <div class="modal-footer py-2 text-center"  >
                        {!! Form::open([ 'method'=>'post', 'url'=>'', 'id'=>'frmFile_form' , 'style'=>'width:100%;' ]) !!}


                        <div class="float-md mobile-100 row mr-1 mb-3" >
                            <div class="input-group col-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">轉存日期</span>
                                </div>
                                <input type="text" id="date5" name="date5" class="form-control" autocomplete="off" value="" required="">
                                <span class="input-group-addon" style="cursor: pointer;" id="transfer_datepicker5"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>


                        <button type="button" class="btn mr-2 btn-info" onclick="location.reload();" data-dismiss="modal">取消</button>
                        <button type="button" class="btn mr-3 btn-danger" onclick="submitform2();" >確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script type="text/javascript">
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
        $("#sdate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker3').click(function(){
            $("#sdate2").focus();
        });
        $("#edate2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker4').click(function(){
            $("#edate2").focus();
        });
        $("#sdate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#sdate3").focus();
        });
        $("#edate3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#edate3").focus();
        });
        $("#date1").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker1').click(function(){
            $("#date1").focus();
        });
        $("#date2").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker2').click(function(){
            $("#date2").focus();
        });
        $("#date3").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker3').click(function(){
            $("#date3").focus();
        });
        $("#date6").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker6').click(function(){
            $("#date6").focus();
        });
        $("#date5").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#transfer_datepicker5').click(function(){
            $("#date5").focus();
        });
    });
    function doClear(){
      var today = new Date();
      var year = today.getFullYear();
      document.all.yerly.value = year-1911;
      document.all.class.value = "";
      document.all.name.value = "";
      document.all.class_branch_name.value = "";
      document.all.term.value = "";
      document.all.sitebranch.value = "";
      $("#sitebranch").val('').trigger("change");
      document.all.branch.value = "";
      $("#branch").val('').trigger("change");
      document.all.process.value = "";
      $("#process").val('').trigger("change");
      document.all.sponsor.value = "";
      $("#sponsor").val('').trigger("change");
      document.all.traintype.value = "";
      $("#traintype").val('').trigger("change");
      document.all.type.value = "";
      $("#type").val('').trigger("change");
      document.all.categoryone.value = "";
      $("#categoryone").val('').trigger("change");
      document.all.sdate.value = "";
      document.all.edate.value = "";
      document.all.sdate2.value = "";
      document.all.edate2.value = "";
      document.all.sdate3.value = "";
      document.all.edate3.value = "";
    }

    function submitform(){
        if($("#date1").val() == ''){
            alert("上課日期錯誤");
            return;
        }
        if($("#date2").val() == ''){
            alert("上課日期錯誤");
            return;
        }
        if($("#date3").val() == ''){
            alert("轉存日期錯誤");
            return;
        }
        if($("#date1").val()>$("#date2").val()){
            alert("上課期間錯誤");
            return;
        }
        submitForm('#transfer_form');
   }
   function submitform2(){

        if($("#date5").val() == ''){
            alert("日期錯誤");
            return;
        }
        submitForm('#frmFile_form');
   }
   function submitform3(){

        if($("#date6").val() == ''){
            alert("日期錯誤");
            return;
        }
        submitForm('#cancel_form');
   }
    </script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
@endsection