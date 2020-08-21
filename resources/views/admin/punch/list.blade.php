@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'punch';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">學費刷卡處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">學費刷卡處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>學費刷卡處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <!-- 班別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別</span>
                                                    </div>
                                                    <select class="form-control select2" id="class" name="class" onchange="classChange();">

                                                        @foreach($classList as $key => $va)
                                                            <option value="{{ $va->class }}" {{ $queryData['class'] == $va->class? 'selected' : '' }}>{{ $va->class }}{{ $va->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 期別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <select class="form-control select2" id="term" name="term">
                                                        <option value="">請選擇</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 日期區間 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">日期區間</span>
                                                    </div>
                                                    <input type="text" id="date" name="date" class="form-control date-range" autocomplete="off" value="{{ $queryData['date'] }}">
                                                </div>
                                            </div>

                                            <!-- 學號 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">學號</span>
                                                    </div>
                                                    <input type="text" id="no" name="no" class="form-control" autocomplete="off" value="{{ $queryData['no'] }}">
                                                </div>
                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                        <!-- 編輯 -->
                                        <button onclick="$('.show').hide();$('.edit').show();" type="button" class="btn btn-primary btn-sm mb-3 show"><i class="fa fa-pencil fa-lg pr-2"></i>編輯</button>

                                        <!-- 儲存 -->
                                        <button onclick="$('#form').submit()" type="button" style="display:none" class="btn btn-danger btn-sm mb-3 edit"><i class="fa fa-save fa-lg pr-2"></i>儲存</button>

                                    </div>

                                    {!! Form::open([ 'method'=>'put', 'url'=>'/admin/punch', 'id'=>'form']) !!}

                                        <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                        <input type="hidden" name="term" value="{{ $queryData['term'] }}">

                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th class="text-center" width="70">學號</th>
                                                    <th>姓名</th>
                                                    <th>刷卡日期</th>
                                                    <th>上午</th>
                                                    <th>下午</th>
                                                    <th>刷退</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($data as $va)
                                                    <tr>
                                                        <td>{{ $va->no }}</td>
                                                        <td>{{ $va->name }}</td>
                                                        <td>{{ $va->swipe_date }}</td>
                                                        <td>{{ $va->morning }}</td>
                                                        <td>{{ $va->afternoon }}</td>
                                                        <td>{{ $va->swipe_return }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
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
    <script>
        // 取得期別
        function classChange()
        {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/punch/getterm',
                data: { classes: $('#class').val(), selected: '{{ $queryData['term'] }}'},
                success: function(data){
                    $('#term').html(data);
                    $("#term").trigger("change");
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
        }

        // 初始化
        classChange();
    </script>

@endsection