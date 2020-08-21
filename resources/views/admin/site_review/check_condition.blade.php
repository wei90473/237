@extends('admin.layouts.layouts')
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/form.css') }}" >
    <?php $_menu = 'site_review';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期選員處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">洽借場地班期選員處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>檢核資訊</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                     <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>姓名</th>
                                                <th>單位名稱</th>
                                                <th>檢核條件</th>
                                                <th>班別</th>
                                                <th>期別</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($check_result as $row)
                                                <tr>
                                                    <td>{{ $row->cname }}</td>
                                                    <td>{{ $row->dept }}</td>
                                                    <td>{{ $t39tb_fields[$row->condition] }}</td>
                                                    <td>{{ $row->class.' '.$row->name }}</td>
                                                    <td>{{ $row->term }}</td>
                                                </tr>
                                                @endforeach 
                                            </tbody>
                                        </table>
                                    </div>


                                    <!-- Modal1 批次增刪作業 -->
                                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">批次增刪作業</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="card-header"><h3 class="card-title">輸入批次作業年度</h3></div>
                                                        <label >年度：</label>
                                                        <input type="text">
                                                        <br/>
                                                    </div>    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success ml-auto" data-dismiss="modal">批次新增</button>
                                                    <button type="button" class="btn btn-danger mx-0" data-dismiss="modal">批次刪除</button>
                                                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">取消</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/admin/site_review/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')

@endsection