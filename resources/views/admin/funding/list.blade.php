@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'funding';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">經費概(結)算查詢</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">經費概(結)算查詢</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>經費概(結)算查詢</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                     <!-- 搜尋 -->
                                     <div class="float-left search-float">
                                            <form signup="get" id="search_form">
    
                                                <!-- 年度 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                        </div>
                                                        <select class="form-control select2" id="class" name="class">
                                                            
                                                        </select>
                                                    </div>
                                                </div>
    
                                                <!-- 種類 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">種類</span>
                                                        </div>
                                                                <input class="funding_radio" type="radio" name="location" value="概算" checked="checked">概算
                                                                <input class="funding_radio" type="radio" name="location" value="結算">結算
                                                                <input class="funding_radio" type="radio" name="location" value="全部">全部
                                                    </div>
                                                </div>
    
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>

                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal1"><i class="fa fa-plus fa-lg pr-2"></i>批次新增(概算)</button>

                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal2"><i class="fa fa-plus fa-lg pr-2"></i>產生結算</button>

                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal3"><i class="fa fa-plus fa-lg pr-2"></i>更新單價</button>

                                                
                                            </form>
                                        </div>
                                    
                                    {{-- <div class="float-md-right">
                                        <!-- 新增 -->
                                        <a href="/admin/funding/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                    </div> --}}

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">班別</th>
                                                <th>期別<i class="fa fa-sort" data-toggle="sort" data-sort-field="holiday"></i></th>
                                                <th>總類<i class="fa fa-sort" data-toggle="sort" data-sort-field="date"></i></th>
                                                <th class="text-center" width="70">合計</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                    <td>{{ $va->holiday }}</td>
                                                    <td>{{ $base->showDate($va->date) }}</td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        {{-- <a href="/admin/funding/{{ $va->date }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a> --}}
                                                        <a href="/admin/funding/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    {{-- <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/funding/{{ $va->date }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- Modal1 批次新增 -->
                                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">批次新增</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <label for="class">班別：</label>
                                                        <select class="select2 form-control select2-single input-max" name="class" id="class">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                        </select>
                                                        <br/>
                                                    </div>    
                                                </div>
                                                <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">確定</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal3 更新單價 -->
                                    <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabe3" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">更新單價</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="card-header"><h3 class="card-title">開課日期區間</h3></div>
                                                        <label for="class">開始：</label>
                                                        <select class="select2 form-control select2-single input-max" name="class" id="class">
                                                            <option value="1"></option>
                                                        </select>
                                                        <label for="class">結束：</label>
                                                        <select class="select2 form-control select2-single input-max" name="class" id="class">
                                                            <option value="1"></option>
                                                        </select>
                                                        <br/>
                                                    </div>    
                                                </div>
                                                <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">確定</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal2 產生結算 -->
                                    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModal2" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">產生結算</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="card-header"><h3 class="card-title">輸入條件</h3></div>
                                                        <label for="class">結業期間</label>
                                                        <select class="select2 form-control" name="class" id="class">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                        </select>
                                                        <p>|</p>
                                                        <select class="select2 form-control " name="class" id="class">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                        </select>
                                                        
                                                        <!-- 選項 -->
                                                        <label for="class">選項：</label>
                                                        <input type="radio" value="all">全選
                                                        <input type="radio" value="notall">全不選
                                                        <br/>
                                                        <button value="search">查詢</button>
                                                        <button value="produce">產生結算</button>
                                                        <button value="cancel">取消</button>
                                                        <button value="save">儲存</button>
                                                        <button value="exit">離開</button>
                                                        
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                        <div class="table-responsive">
                                                                <table class="table table-bordered mb-0">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="text-center" width="70">註記</th>
                                                                        <th>班別</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td></td>
                                                                            <td></td>
                                                                        <tr>
                                                                    </tbody>
                                                                </table>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
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

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection