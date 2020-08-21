@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'password_maintenance';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">機關個人密碼維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">機關個人密碼維護列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>機關個人密碼維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 機關代碼 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">機關代碼</span>
                                                    </div>
                                                    <input type="text" id="enrollorg" name="enrollorg" class="form-control" autocomplete="off" value="{{ $queryData['enrollorg'] }}">
                                                </div>
                                            </div>

                                            <!-- 身分證字號 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">身分證字號</span>
                                                    </div>
                                                    <input type="text" id="userid" name="userid" class="form-control" autocomplete="off" value="{{ $queryData['userid'] }}">
                                                </div>
                                            </div>

                                            <!-- 使用狀況 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">使用狀況</span>
                                                    </div>
                                                    <select class="form-control select2" name="status">
                                                        <option value="">全部</option>
                                                        <option value="Y" {{ $queryData['status'] == 'Y'? 'selected' : '' }}>啟用</option>
                                                        <option value="N" {{ $queryData['status'] == 'N'? 'selected' : '' }}>未啟用</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">編號</th>
                                                <th>使用狀態</th>
                                                <th>自訂帳號</th>
                                                <th>錯誤次數</th>
                                                <th>機關代碼</th>
                                                <th>機關名稱</th>
                                                <th>身分證字號</th>
                                                <th>姓名</th>
                                                <th class="text-center">使用設定</th>
                                                <th class="text-center">密碼重設</th>
                                                <th class="text-center">帳號重設</th>


                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr class="{{ ($va->m17status == 'N')? 'bg-danger text-white' : '' }}">
                                                    <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                    <td>{{ $va->m21status == 'Y'? '◎' : '' }}</td>
                                                    <td>{{ $va->account }}</td>
                                                    <td>{{ $va->pswerrcnt }}</td>
                                                    <td>{{ $va->enrollorg }}</td>
                                                    <td>{{ $va->enrollname }}</td>
                                                    <td>{{ $va->userid }}</td>
                                                    <td>{{ $va->username }}</td>

                                                    <!-- 修改 -->
                                                    {{--<td class="text-center">--}}
                                                        {{--<a href="/admin/password_maintenance/{{ $va->password_maintenance_id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">--}}
                                                            {{--<i class="fa fa-pencil"></i>--}}
                                                        {{--</a>--}}
                                                    {{--</td>--}}
                                                    <td class="text-center"><button type="button" class="btn btn-primary waves-effect waves-light m-b-5" data-toggle="modal" data-target="#modol_1" onclick="$('#model_1_userid').val('{{ $va->userid }}');$('#model_1_enrollorg').val('{{ $va->enrollorg }}');">使用設定</button></td>
                                                    <td class="text-center"><button type="button" class="btn btn-success waves-effect waves-light m-b-5" data-toggle="modal" data-target="#modol_2" onclick="$('#model_2_userid').val('{{ $va->userid }}');$('#model_2_enrollorg').val('{{ $va->enrollorg }}');">密碼重設</button></td>
                                                    <td class="text-center"><button type="button" class="btn btn-info waves-effect waves-light m-b-5" data-toggle="modal" data-target="#modol_3" onclick="$('#model_3_userid').val('{{ $va->userid }}');$('#model_3_enrollorg').val('{{ $va->enrollorg }}');">帳號重設</button></td>
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

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

    <!-- 使用者設定 -->
    <div id="modol_1" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">

                        <div class="card-header bg-danger">
                            <h3 class="card-title float-left text-white">編輯</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white">&times;</span>
                            </button>
                        </div>

                        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/password_maintenance/act1' ]) !!}
                            <div class="card-body">
                                <p class="mb-0">使用狀態：</p>
                                <select class="form-control mt-3" name="status">
                                    <option value="Y">啟用</option>
                                    <option value="N">未啟用</option>
                                </select>
                            </div>

                            <input type="hidden" name="userid" id="model_1_userid">
                            <input type="hidden" name="enrollorg" id="model_1_enrollorg">

                            <div class="modal-footer py-2">
                                <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                                <button type="submit" class="btn mr-3 btn-danger">儲存</button>
                            </div>
                        {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- 密碼重設 -->
    <div id="modol_2" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <p class="mb-0">你確定要重設密碼嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/password_maintenance/act2' ]) !!}
                            <input type="hidden" name="userid" id="model_2_userid">
                            <input type="hidden" name="enrollorg" id="model_2_enrollorg">

                            <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                            <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 帳號重設 -->
    <div id="modol_3" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <p class="mb-0">你確定要重設帳號嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/password_maintenance/act3' ]) !!}
                            <input type="hidden" name="userid" id="model_3_userid">
                            <input type="hidden" name="enrollorg" id="model_3_enrollorg">

                            <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                            <button type="submit" class="btn mr-3 btn-danger">確定</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection