@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'print';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座授課及教材資料查詢</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座授課及教材資料查詢</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座授課及教材資料查詢</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 查尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                                <!-- 教材名稱 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">教材名稱</span>
                                                        </div>
                                                        <select class="select2 form-control select2-single input-max" name="status">
                                                           
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- 講座姓名 -->
                                                <div class="pull-left mobile-100 mr-1 mb-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">講座姓名</span>
                                                        </div>
                                                        <select class="select2 form-control select2-single input-max" name="status">
                                                            
                                                        </select>
                                                    </div>
                                                </div>

                                            <!-- 每頁幾筆 -->
                                            {{-- <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}"> --}}

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查尋</button>

                                            <a href="/admin/print/new">
                                                <button type="button" class="btn mobile-100 mb-3 mb-md-0"><i class="fas fa-plus fa-lg pr-1"></i>新增</button>
                                            </a>
                                            
                                            
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70"></th>
                                                <th>教材名稱</th>
                                                <th>講座姓名</th>
                                                <th>版本日期</th>
                                                <th>著作授權同意書</th>
                                                <th class="text-center" width="70">修改</th>
                                                <th class="text-center" width="70">授權書上傳</th>
                                                <th class="text-center" width="70">授權書下載</th>
                                                <th class="text-center" width="70">教材上傳</th>
                                                <th class="text-center" width="70">教材下載</th>
                                                
                                            </tr>
                                            </thead>
                                            <tbody>

                                            {{-- @foreach($data as $va) --}}

                                                <tr>
                                                    {{-- <td class="text-center">{{ $va->no }}</td>
                                                    <td>{{ $va->status }}</td>
                                                    <td>{{ $va->unit }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->name}}</td> --}}
                                                    
                                                    <td class="text-center"></td>
                                                    <td>行政罰法</td>
                                                    <td>陳滄海</td>
                                                    <td>108315</td>
                                                    <td>1080325</td>
                                                    <!--修改-->
                                                    <td class="text-center">
                                                            {{-- <a href="/admin/funding/{{ $va->date }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> --}}
                                                            <a href="/admin/print/maintain" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                    </td>
                                                    <!--授權書上傳-->
                                                    <td class="text-center">
                                                            {{-- <a href="/admin/funding/{{ $va->date }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> --}}
                                                            
                                                            <input type="file" name="file" id="file" />
                                                            <input type="submit" name="submit" value="上傳檔案" />
                                                       
                                                    </td>
                                                    <!--授權書下載-->
                                                    <td class="text-center">
                                                            {{-- <a href="/admin/funding/{{ $va->date }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> --}}
                                                            <a href="/admin/funding/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                    </td>
                                                    <!--教材上傳-->
                                                    <td class="text-center">
                                                            {{-- <a href="/admin/funding/{{ $va->date }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> --}}
                                                            <input type="file" name="file" id="file" />
                                                            <input type="submit" name="submit" value="上傳檔案" />
                                                    </td>
                                                    <!--教材下載-->
                                                    <td class="text-center">
                                                            {{-- <a href="/admin/funding/{{ $va->date }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a> --}}
                                                            <a href="/admin/funding/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                    </td>
                                                   
                                                </tr>
                                            {{-- @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- 分頁 -->
                                    {{-- @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData]) --}}

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                        {{-- @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData]) --}}

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection