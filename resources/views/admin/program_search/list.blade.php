@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
    <style>
        .pl {
            overflow:hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2; 
            -webkit-box-orient: vertical;
            white-space: normal;
            width:10em;
        }
    </style>
    <?php $_menu = 'program_search';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">異動記錄查詢</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">異動記錄查詢</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>異動記錄查詢</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float search-float">
                                        <form method="get" id="search_form">
                                            <!-- 使用者名稱 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-8">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">使用者名稱</span>
                                                    </div>
                                                    <?php $useridList = $base->getDBList('M09tb')?>
                                                    <select id="userid" name="userid" class="browser-default custom-select">
                                                        <option value="">請選擇</option>
                                                        @foreach($useridList as $key => $va)
                                                            <option value="{{ $va['userid'] }}" {{ old('userid', (isset($queryData['userid']))? $queryData['userid']: '') == $va['userid']? 'selected' : '' }}>{{ $va['username'] }}  {{ $va['userid'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 功能名稱 -->
                                            <?php $progidList = $base->getDBList('M11tb')?>
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-8">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">功能名稱</span>
                                                    </div>
                                                    <select id="progid" name="progid" class="browser-default custom-select">
                                                        <option value="">請選擇</option>
                                                        @foreach($progidList as $key => $va)
                                                            <option value="{{ $va['progid'] }}" {{ old('progid', (isset($queryData['progid']))? $queryData['progid']: '') == $va['progid']? 'selected' : '' }}>{{ $va['progname'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 異動日期 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-8">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">異動日期</span>
                                                    </div>
                                                    <div>
                                                        <input class="date form-control" value="{{$queryData['logsdate']}}" type="text" id="logsdate" name="logsdate">
                                                    </div>～
                                                    <!-- <label class="control-label text-md pt-2">～</label> -->
                                                    <div>
                                                        <input class="date form-control" value="{{$queryData['logedate']}}" type="text" id="logedate" name="logedate">
                                                    </div>
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


                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>使用者名稱</th>
                                                <th>功能名稱</th>
                                                <th>異動日期</th>
                                                <th>異動時間</th>
                                                <th>異動類別</th>
                                                <th>異動主資料表</th>
                                                <th>紀錄內容</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if ( isset($data) )
                                            @foreach($data as $va)
                                                <tr>
                                                    <td>{{ $va->username }}</td>
                                                    <td>{{ $va->progname }}</td>
                                                    <td>{{ $va->date }}</td>
                                                    <td>{{ $va->logtime }}</td>
                                                    <td>{{ $va->type }}</td>
                                                    <td>{{ $va->logtable }}</td>
                                                    <td><div class="pl" value="{{$va->content}}" onclick="showcontent(this)"><a href="#">{{ substr($va->content,0,80) }}</a></div></td>
                                                </tr>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ( isset($data) )
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ( isset($data) )
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
    <!-- 詳細內容 modal -->
    <div class="modal fade bd-example-modal-lg contentmodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width:700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">詳細內容</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">紀錄內容</label>
                            <!--紀錄內容-->
                            <div class="col-sm-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="content" id="content" value="" ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">離開</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
$( function() {
    $('#logsdate').datepicker({   
        format: "twymmdd",
        language: 'zh-TW'
    });
    $('#logedate').datepicker({   
        format: "twymmdd",
        language: 'zh-TW'
    });
  } );
// 選擇上課方式 日期
    function showcontent(e) {
        $(".contentmodal").modal('show');
        $('textarea[name=content]').val($(e)[0].attributes[1].value);
    }
</script>
@endsection