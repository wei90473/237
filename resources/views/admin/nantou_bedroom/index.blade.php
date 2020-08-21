@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'nantou_bedroom';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">寢室資料維護(南投)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">寢室資料維護(南投)列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>寢室資料維護(南投)</h3>
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
                                                            <span class="input-group-text">樓別代碼</span>
                                                        </div>
                                                        <select class="custom-select" name="floorno">
                                                            @foreach ($floors as $floorno => $floorname)
                                                            <option value="{{ $floorno }}" {{ ($floorno == $queryData['floorno']) ? 'selected' : null }} >{{ $floorname }}</option>
                                                            @endforeach
                                                        </select>
                                                        
                                                    </div>
                                                </div>                                                
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">寢室床位代碼</span>
                                                        </div>
                                                        <input type="text" name="bedno" class="form-control" value="{{ $queryData['bedno'] }}">
                                                    </div>
                                                </div>                                                  
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">是否使用</span>
                                                        </div>
                                                        
                                                        <select class="custom-select" name="isuse" >   
                                                            <option value="0" {{ (0 == $queryData['isuse']) ? 'selected' : null }}>否</option>>
                                                            <option value="1" {{ (1 == $queryData['isuse']) ? 'selected' : null }}>是</option>
                                                        </select>
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
                                            <a href="/admin/nantou_bedroom">
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                    重設條件
                                                </button>
                                            </a>
                                            <a href="/admin/nantou_bedroom/create">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                新增寢室
                                            </button>                                            
                                            </a>
                                            <a href="/admin/nantou_floor">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                樓別資料維護
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
                                                <th class="text-center">樓別名稱</th>
                                                <th class="text-center">寢室</th>
                                                <th class="text-center">床位</th>
                                                <th class="text-center">是否使用</th>
                                                <th class="text-center">寢室名稱</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $bedroom)
                                                    <tr>
                                                        <td class="text-center">
                                                            <a href="/admin/nantou_bedroom/edit/{{ $bedroom->id }}"
                                                                ><button class="btn btn-primary">編輯</button>
                                                            </a>
                                                        </td>
                                                        <td class="text-center">
                                                            @if (isset($floors[$bedroom->floorno]))
                                                            {{ $floors[$bedroom->floorno] }}
                                                            @else
                                                            {{ $bedroom->floorno }}
                                                            @endif 
                                                        </td>
                                                        <td class="text-center">{{ $bedroom->bedroom }}</td>
                                                        <td class="text-center">{{ substr($bedroom->bedno, -1) }}</td>
                                                        <td class="text-center">{{ ($bedroom->isuse == 0) ? '否' : '是'  }}</td>
                                                        <td class="text-center">{{ $bedroom->roomname }}</td>
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