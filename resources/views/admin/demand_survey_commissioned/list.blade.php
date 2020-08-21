@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'demand_survey_commissioned';?>
    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">委訓班需求調查處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">需求調查列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>委訓班需求調查處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 年度 -->
                                        
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <!-- <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <input type="text" id="yerly" name="yerly" class="form-control" autocomplete="off" value="{{ $queryData['yerly'] }}" maxlength="3" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                                </div> -->
                                                <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                        </div>
                                                        <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  >
                                                            @foreach($queryData['choices'] as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
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
                                            
                               

                                            <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-remove fa-lg pr-1"></i>重設查詢條件</button>
                                            
                                            <a href="/admin/demand_survey_commissioned/create">                                
                                                 <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-plus fa-lg pr-1"></i>新增需求調查</button>
                                            </a>
                                                                                                                             
                               
                                        </form>

                             
                                         <!-- 複製需求填報網址 -->
                                         <div class="pull-left mobile-100 mr-1 mb-12">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">需求填報網址</span>
                                                </div>
                                                <input type="text"  style="min-width: 500px; flex:0 1 auto" id="UrlText" name="UrlText" class="form-control" autocomplete="off" value="http://service.hrd.gov.tw/IntrustUser">
                                                <button type="button" onclick="CopyUrlTextToClipboard('UrlText')" class="btn btn-primary btn-sm mb-10"><i class="fa fa-plus fa-lg pr-2"></i>複製</button>
                                            </div>
                                        </div> 
                                        <br>

                                    </div>
                              
                             

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="300">功能</th>
                                                <th>年度</th>
                                                <th>專碼</th>
                                                <th>填報期間</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <td class="text-center">
                                                    <!-- 編輯 -->
                                                    <a href="/admin/demand_survey_commissioned/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                    <button type="submit" class="btn btn-primary mb-3"><i class="fa fa-pencil fa-lg pr-2"></i>編輯</button>
                                                    </a>
                                                    <a href="/admin/demand_survey_commissioned/{{ $va->id }}/view" data-placement="top" data-toggle="tooltip" data-original-title="查看填報資料">
                                                    <button type="submit" class="btn btn-success mb-3"><i class="fa fa-eye fa-lg pr-2"></i>查看填報資料</button>
                                                    </a>
                                                  
                                                    </td>
                                                    <td>{{ $va->yerly }}</td>
                                                    <td>{{ $va->item_id }}</td>
                                                    <td>{{ $va->sdate }} ~ {{ $va->edate }}</td>
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

<script>
    function CopyUrlTextToClipboard(id) {
        var TextRange = document.createRange();
        TextRange.selectNode(document.getElementById(id));
        sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(TextRange);
        document.execCommand("copy");
        alert("複製完成！")  
    }
</script>