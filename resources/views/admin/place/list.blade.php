@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'place';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地資料</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地資料列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地資料</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-6">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">場地編號</span>
                                                    </div>
                                                    <input type="text" class="form-control input-max" id="site" name="site" placeholder="場地編號" value="{{ old('site', (isset($queryData->site))? $queryData->site : '') }}" autocomplete="off"  maxlength="4" >
                                                </div >
                                                <div class="input-group col-6">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">院區</span>
                                                    </div>
                                                    <select id="branch" name="branch" class="select2 form-control select2-single input-max" >
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}"  {{ old('branch', (isset($queryData->branch))? $queryData->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 場地名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-6">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">場地名稱</span>
                                                    </div>
                                            
                                                    <input type="text" class="form-control input-max" id="name" name="name" value="{{ old('name', (isset($queryData->name))? $queryData->name : '') }}" autocomplete="off"  maxlength="255">
                                                </div>
                                            </div>
                                            <!-- 場地類型 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-8">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">場地類型</span>
                                                    </div>
                                                    <select id="type" name="type" class="select2 form-control select2-single input-max" >
                                                        <option value=""  >請選擇</option>
                                                    @foreach(config('app.place_type') as $key => $va)
                                                        <option value="{{ $key }}"  {{ old('type', (isset($queryData->type))? $queryData->type : '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        <!-- <input type="radio" name="type" value="{{ $key }}"  {{ old('type', (isset($queryData->type))? $queryData->type : 1) == $key? 'checked' : '' }}>{{ $va }} -->
                                                    @endforeach
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
                                        <!-- 新增 -->
                                        <a href="/admin/place/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">修改</th>
                                                <th>場地編號<i class="fa fa-sort" data-toggle="sort" data-sort-field="site"></i></th>
                                                <th>院區</th>
                                                <th>場地名稱</th>
                                                <th>場地類型</th>
                                                <th>是否外借</th>
                                                <th class="text-center" width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/place/{{ $va->site }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->site }}</td>
                                                    <td>{{ config('app.branch.'.$va->branch) }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ config('app.place_type.'.$va->type) }}</td>
                                                    <td>{{ config('app.yesorno.'.$va->open) }}</td>
                                                    
                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/place/{{ $va->site }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
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

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection