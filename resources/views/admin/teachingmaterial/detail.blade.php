@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teaching_material';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座授課及教材資料登錄</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座授課及教材資料登錄列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座授課及教材資料登錄</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">講座姓名： {{ $queryData['cname'] }}</span>

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">教材名稱</span>
                                                        <input type="text" id="keyword" name="keyword" class="form-control" autocomplete="off" value="{{ $queryData['keyword'] }}">
                                                        <input type="hidden" id="search" name="search" class="form-control" autocomplete="off" value="search">

                                                    </div>
                                                </div>

                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <a href="/admin/teaching_material/create?serno={{ $serno }}">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                            </a>
                                            <a href="javascript:history.go(-1)">
                 
                 <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
             </a>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>教材名稱</th>
                                                <th>檔案</th>
                                                <th>授權書</th>
                                                <th>是否上網</th>
                                                <th>上傳日期</th>
                                                <th>上傳人員</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($list as $row)
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/teaching_material/{{ $row['id'] }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $row['name'] }}</td>
                                                    <td>
                                                        <a target="_blank" href="/Uploads/teachingmaterial/{{ $row['filename'] }}">
                                                        {{ $row['filename'] }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a target="_blank" href="/Uploads/teachingmaterial/{{ $row['COA'] }}">
                                                        {{ $row['COA'] }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $row['online'] }}</td>
                                                    <td>{{ $row['addday'] }}</td>
                                                    <td>{{ $row['addid'] }}</td>

                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection