@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <style>
        .arrow_rank {
            display: flex;
            flex-direction: column;
        }
        .flex {
            display: flex;
        }
        .btnSpace {
            min-width: 70px;
            margin-bottom: 5px;
        }
    </style>

    <?php $_menu = 'site_manage';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期資料處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">洽借場地班期資料處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期資料處理列表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <!-- 班號 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <select class="form-control select2" name="status">
                                                        <option>108</option>
                                                        <option>107</option>
                                                        <option>106</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 課程分類 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">課程分類</span>
                                                    </div>
                                                    <select class="form-control select2" name="status">
                                                        <option>A 生活知能</option>
                                                        <option>B 藝術文化</option>
                                                        <option>C 健康休閒</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button><br>

                                            <!-- 批次新增或刪除 -->
                                            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="batchAddDel()">批次新增或刪除</button>

                                            <!-- 排序 -->
                                            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="order()">排序</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th width="150">班號</th>
                                                <th width="300">班別名稱</th>
                                                <th width="150">申請單位</th>
                                                <th width="70">上課方式</th>
                                                <th width="70">訓期</th>
                                                <th width="70">每期人數</th>
                                                <th width="70">每日上課時間</th>
                                                <th width="70">修改</th>
                                                <th width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>


                                                <tr>
                                                    <td>108G01</td>
                                                    <td>107年度推動員工協助方案成效</td>
                                                    <td>其他(每周五)</td>
                                                    <td>1天</td>
                                                    <td>0</td>
                                                    <td></td>
                                                    <td></td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/site_manage/id/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/site_manage/id');" data-toggle="modal" data-target="#del_modol">
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>108G01</td>
                                                    <td>107年度推動員工協助方案成效</td>
                                                    <td>其他(每周五)</td>
                                                    <td>1天</td>
                                                    <td>0</td>
                                                    <td></td>
                                                    <td></td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/site_manage/id/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/site_manage/id');" data-toggle="modal" data-target="#del_modol">
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
                                                    </td>
                                                </tr>
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


    <!-- 批次新增或刪除 modal -->
	<div class="modal fade bd-example-modal-lg batchAddDel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-dialog_80" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">批次新增或刪除</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    <label class="control-label text-md-right">年度</label>
                    <input type="text" class="form-control input-max" value="">
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">新增</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">刪除</button>
			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
		        </div>
		    </div>
		</div>
	</div>

    <!-- 排序 modal -->
	<div class="modal fade bd-example-modal-lg order" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document" style="max-width:700px;">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">排序</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body flex" style="position:relative;">
                    <table class="table table-bordered mb-0" style="margin-right:10px;">
                        <thead>
                            <tr>
                                <th width="150">班號</th>
                                <th width="300">班別</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>108G01</td>
                                <td>107年度推動員工協助方案成效</td>
                            </tr>
                            <tr>
                                <td>108G02</td>
                                <td>108年主計節頒獎典禮</td>
                            </tr>
                            <tr>
                                <td>108G03</td>
                                <td>行政院人事行政會議</td>
                            </tr>
                            <tr>
                                <td>108G01</td>
                                <td>107年度推動員工協助方案成效</td>
                            </tr>
                            <tr>
                                <td>108G02</td>
                                <td>108年主計節頒獎典禮</td>
                            </tr>
                            <tr>
                                <td>108G03</td>
                                <td>行政院人事行政會議</td>
                            </tr>
                            <tr>
                                <td>108G01</td>
                                <td>107年度推動員工協助方案成效</td>
                            </tr>
                            <tr>
                                <td>108G02</td>
                                <td>108年主計節頒獎典禮</td>
                            </tr>
                            <tr>
                                <td>108G03</td>
                                <td>行政院人事行政會議</td>
                            </tr>
                            <tr>
                                <td>108G01</td>
                                <td>107年度推動員工協助方案成效</td>
                            </tr>
                            <tr>
                                <td>108G02</td>
                                <td>108年主計節頒獎典禮</td>
                            </tr>
                            <tr>
                                <td>108G03</td>
                                <td>行政院人事行政會議</td>
                            </tr>
                            <tr>
                                <td>108G01</td>
                                <td>107年度推動員工協助方案成效</td>
                            </tr>
                            <tr>
                                <td>108G02</td>
                                <td>108年主計節頒獎典禮</td>
                            </tr>
                            <tr>
                                <td>108G03</td>
                                <td>行政院人事行政會議</td>
                            </tr>
                            <tr>
                                <td>108G01</td>
                                <td>107年度推動員工協助方案成效</td>
                            </tr>
                            <tr>
                                <td>108G02</td>
                                <td>108年主計節頒獎典禮</td>
                            </tr>
                            <tr>
                                <td>108G03</td>
                                <td>行政院人事行政會議</td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="position:relative;">
                        <div class="btnSpace"></div>
                        <div class="arrow_rank" style="position:fixed;">
                            <button class="btn btn-success btnSpace">最上筆</button>
                            <button class="btn btn-success btnSpace">上一筆</button>
                            <button class="btn btn-success btnSpace">下一筆</button>
                            <button class="btn btn-success btnSpace" style="margin-bottom:50px;">最下筆</button>

                            <button type="button" class="btn btn-primary btnSpace" data-dismiss="modal">儲存</button>
			                <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
                        </div>
                    </div>
		        </div>
		        <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">儲存</button>
			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
		        </div> -->
		    </div>
		</div>
	</div>

    <script>
        // 批次新增或刪除
        function batchAddDel() {
            $(".batchAddDel").modal('show');
        }

        // 排序
        function order() {
            $(".order").modal('show');
        }
    </script>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection