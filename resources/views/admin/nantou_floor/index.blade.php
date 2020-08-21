@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'nantou_bedroom';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">樓別資料維護(南投)列表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">寢室資料維護(南投)列表</li>
                        <li class="active">樓別資料維護(南投)列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>樓別資料維護(南投)列表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="width:100%">
                                        <form method="get" id="search_form">
                                            

                                            <div class="form-row">
                                                <div class="form-group col-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">代碼</span>
                                                        </div>
                                                        <input type="text" name="floorno" class="form-control" value="{{ $queryData['floorno'] }}">
                                                    </div>
                                                </div>                                                  
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">名稱</span>
                                                        </div>
                                                        <input type="text" name="floorname" class="form-control" value="{{ $queryData['floorname'] }}">
                                                    </div>
                                                </div>                                                  
                                            </div>


                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0">
                                                <i class="fa fa-search fa-lg pr-1"></i>搜尋
                                            </button>
                                            <a href="/admin/nantou_floor">
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                    重設條件
                                                </button>
                                            </a>
                                            <a href="/admin/nantou_floor/create">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                新增樓別
                                            </button>  
                                            </a>                                        
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                                <th class="text-center" width="100">功能</th>
                                                <th class="text-center">代碼</th>
                                                <th class="text-center">樓別名稱</th>
                                                <th class="text-center">教室別</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $floor)
                                                    <tr>
                                                        <td class="text-center">
                                                            <a href="/admin/nantou_floor/edit/{{ $floor->id }}"
                                                                ><button class="btn btn-primary">編輯</button>
                                                            </a>
                                                        </td>
                                                        <td class="text-center">{{ $floor->floorno }}</td>
                                                        <td class="text-center">{{ $floor->floorname }}</td>
                                                        <td class="text-center">
                                                            @if (isset($cls[$floor->croomclsno]))
                                                                {{ $cls[$floor->croomclsno] }}
                                                            @else
                                                                {{ $floor->croomclsno }}                                                            
                                                            @endif 
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

@endsection