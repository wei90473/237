@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <style>
        label {
            margin: 0px;
        }
        .item_con {
            display: flex;
            align-items: center;
        }
        .selectStyle > .select2-container {
            width: 150px !important;
            margin-right: 5px;
        }
        .select2 .select2-container .select2-container--default {
            width: 150px !important;
        }
        .display_inline {
            display: inline-block;
            margin-right: 5px;
        }
        .col-sm-10 {
            padding: 0px;
        }
    </style>

    <?php $_menu = 'teachlist';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">維護教法調查班別</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teachlist" class="text-info">教學教法運用彙總表</a></li>
                        <li class="active">維護教法調查班別</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>維護教法調查班別</h3>
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
                                                        <select class="form-control select2" name="">
                                                            <option>108</option>
                                                            <option>107</option>
                                                            <option>106</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            <!-- 每頁幾筆 -->

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th width="150">調查註記</th>
                                                <th width="300">班號</th>
                                                <th width="150">班別名稱</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Y</td>
                                                    <td>108</td>
                                                    <td>107年度推動員工協助方案成效</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br/>
                                    <div style="text-align:center">
                                        <a href="/admin/teachlist/maintain" class="col-md-3">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-pencil fa-lg pr-2"></i>修改</button>
                                        </a>
                                        <a href="/admin/teachlist/maintain" class="col-md-3">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-save fa-lg pr-2"></i>儲存</button>
                                        </a>
                                        <a href="/admin/teachlist/maintain" class="col-md-3">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-close fa-lg pr-2"></i>取消</button>
                                        </a>
                                        <a href="/admin/teachlist/maintain" class="col-md-3">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-reply fa-lg pr-2"></i>離開</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>


@endsection