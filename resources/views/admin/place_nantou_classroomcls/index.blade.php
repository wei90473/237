@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'place_nantou';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地資料(南投)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地資料(南投)列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地資料(南投)</h3>
                        </div>

                        <div class="card-body">
                            <div class="row" style="margin-bottom:20px;">
                                <div class="col-12">
                                    <a href="/admin/place_nantou_location"><button class="btn  mobile-100 mb-3 mb-md-0  mobile-100 mb-3 mb-md-0">區域別</button></a>
                                    <a href="/admin/place_nantou_classroomcls"><button class="btn  mobile-100 mb-3 mb-md-0 btn-primary">教室別</button></a>

                                    <a href="/admin/place_nantou"><button class="btn  mobile-100 mb-3 mb-md-0" >教室</button></a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="width:100%">
                                        <form method="get" id="search_form">
                                            <div class="form-row">

                                                <div class="form-group col-3">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">場地名稱</span>
                                                        </div>
                                                        <input type="text" name="croomclsname" class="form-control">
                                                    </div>
                                                </div>                                                
                                            </div>

                                            <div class="form-row">
                                            </div>


                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0">
                                                <i class="fa fa-search fa-lg pr-1"></i>搜尋
                                            </button>
                                            <a href="/admin/place_nantou_classroomcls">
                                                <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                    重設條件
                                                </button>
                                            </a>
                                            <a href="/admin/place_nantou_classroomcls/create">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0">
                                                新增教室別
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
                                                <th>場地名稱</th>
                                                <th>容納人數</th>
                                                <th>平日計費</th>
                                                <th>假日計費</th>
                                                <th>場地使用說明</th>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $eduClassRoomcls)
                                                <tr>
                                                  <td class="text-center"><a href="/admin/place_nantou_classroomcls/edit/{{ $eduClassRoomcls->croomclsno }}"><button class="btn btn-primary">編輯</button></a></td>
                                                  <td>{{ $eduClassRoomcls->croomclsname }}</td>
                                                  <td>{!! $eduClassRoomcls->summary2 !!}</td>
                                                  <td>
                                                    @foreach ($eduClassRoomcls->fees as $fee)
                                                      @if (isset(config('database_fields.edu_classroomcls')[$fee->timetype]))
                                                          {{ config('database_fields.edu_classroomcls')[$fee->timetype].' '.$fee->fee.'元' }}
                                                          @if (isset($fee->feetypeCode))
                                                            {{ ' / '.$fee->feetypeCode->name }}
                                                          @endif 
                                                          <br>
                                                      @endif 
                                                    @endforeach
                                                  </td>
                                                  <td>
                                                    @foreach ($eduClassRoomcls->fees as $fee)
                                                      @if (isset(config('database_fields.edu_classroomcls')[$fee->timetype]))
                                                        {{ config('database_fields.edu_classroomcls')[$fee->timetype].' '.$fee->holidayfee.'元' }}
                                                        @if (isset($fee->feetypeCode))
                                                          {{ ' / '.$fee->feetypeCode->name }}
                                                        @endif 
                                                        <br>
                                                      @endif 
                                                    @endforeach
                                                  </td>
                                                  <td>{{ $eduClassRoomcls->description }}</td>
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