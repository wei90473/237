@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'classes_requirements';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地管理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">辦班需求(確認)處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>辦班需求(確認)處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">
                                            <!-- 年度 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-4">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="yerly">
                                                    @foreach($queryData['choices'] as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班號 -->
                                                <div class="input-group col-6">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">班號</span>
                                                    </div>
                                                    <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}">
                                                </div>
                                            </div>
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別名稱</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ $queryData['name'] }}">
                                                </div>
                                                <!-- **分班名稱 -->
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">分班名稱</span>
                                                    </div>
                                                    <input type="text" id="branchname" name="branchname" class="form-control" autocomplete="off" value="">
                                                </div>
                                            </div>
                                            <!-- 期別 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <input type="text" id="term" name="term" class="form-control" autocomplete="off" value="{{ $queryData['term'] }}">
                                                </div>
                                                <!-- 上課地點 -->

                                                <!-- 辦班院區 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班院區</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="branch">
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 班別類型 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別類型</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="process">
                                                        <option value="" >請選擇</option>
                                                        @foreach(config('app.process') as $key => $va)
                                                            <option onclick="ChangeDisabled({{ $key }}) value="{{ $key }}" {{ $queryData['process'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班務人員** -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班務人員</span>
                                                    </div>
                                                   <?php $list = $base->getSponsor(); ?>
                                                    <select id="sponsor" name="sponsor" class="browser-default custom-select">
                                                        <option value="">請選擇</option>
                                                        @foreach($list as $key => $va)
                                                            <option value="{{ $key }}" {{ old('sponsor', (isset($queryData['sponsor']))? $queryData['sponsor']: '') == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 訓練性質 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">訓練性質</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="traintype">
                                                        <option value="">請選擇</option>
                                                        @foreach(config('app.traintype') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['traintype'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- 班別性質 -->
                                                <?php $typeList = $base->getSystemCode('K')?>
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別性質</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="type">
                                                        <option value="">請選擇</option>
                                                        @foreach($typeList as $code => $va)
                                                            <option value="{{ $code }}" {{ $queryData['type'] == $code? 'selected' : '' }}>{{ $va['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- **類別1 -->
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">類別1</span>
                                                    </div>
                                                    <select class="browser-default custom-select" name="typeone">
                                                        <option value="">請選擇</option>
                                                        <option value="1A">1A</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- 開訓日期範圍 -->
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期範圍(起)</span>
                                                    </div>
                                                    <input type="text" id="sdate_begin" name="sdate_begin" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">開訓日期範圍(迄)</span>
                                                    </div>
                                                    <input type="text" id="sdate_end" name="sdate_end" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <!-- 結訓日期範圍 -->
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期範圍(起)</span>
                                                    </div>
                                                    <input type="text" id="edate_begin" name="edate_begin" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">結訓日期範圍(迄)</span>
                                                    </div>
                                                    <input type="text" id="edate_end" name="edate_end" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <!-- 在訓期間範圍 -->
                                            <div class="form-group row">
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間範圍(起)</span>
                                                    </div>
                                                    <input type="text" id="indate_begin" name="indate_begin" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">在訓期間範圍(迄)</span>
                                                    </div>
                                                    <input type="text" id="indate_end" name="indate_end" class="form-control" autocomplete="off" value="">
                                                    <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                            <!-- 重設條件 -->
                                            <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" type="button">重設條件</button>
                                            <!-- 更新單價 -->
                                            <button class="btn mobile-100 mb-3 mb-md-0" onclick="unitprice()" type="button">更新單價</button>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">功能</th>
                                                <th>班號</th>
                                                <th>辦班院區</th>
                                                <th>訓練班別</th>
                                                <th>期別</th>
                                                <th>分班名稱</th>
                                                <th>班別類型</th>
                                                <th>起迄期間</th>
                                                <th>班務人員</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))        
                                            @foreach($data as $va)
                                                <tr>
                                                <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/classes_requirements/edit/{{ $va->class.$va->term }}">
                                                            <button type="button" class="btn btn-primary btn-sm mb-3">詳細資料</button>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->class }}</td>
                                                    <td>{{ config('app.branch.'.$va->branch) }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->term }}</td>
                                                    <td>{{ $va->branchname }}</td>
                                                    <td>{{ config('app.process.'.$va->process) }}</td>
                                                    <td>{{ $va->sdate }}～{{ $va->edate }}</td>
                                                    <td>{{ $va->username }}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($data))    
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(isset($data))    
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 更新單價 -->
    <div class="modal fade" id="unitprice" role="dialog">
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/classes_requirements/unitprice', 'id'=>'form']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">批次增刪</h4>
                    </div>
                    <div class="modal-body">
                        <!-- 開訓日期範圍 -->
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">開訓日期範圍(起)</span>
                            </div>
                            <input type="text" id="unitprice_begin" name="unitprice_begin" class="form-control" autocomplete="off" value="">
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker7"><i class="fa fa-calendar"></i></span>
                        </div>
                        <br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">開訓日期範圍(迄)</span>
                            </div>
                            <input type="text" id="unitprice_end" name="unitprice_end" class="form-control" autocomplete="off" value="">
                            <span class="input-group-addon" style="cursor: pointer;" id="datepicker8"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkunitprice()">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $("#sdate_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker1').click(function(){
                $("#sdate_begin").focus();
            });
            $("#sdate_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker2').click(function(){
                $("#sdate_end").focus();
            });
            $("#edate_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker3').click(function(){
                $("#edate_begin").focus();
            });
            $("#edate_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker4').click(function(){
                $("#edate_end").focus();
            });
            $("#indate_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker5').click(function(){
                $("#indate_begin").focus();
            });
            $("#indate_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker6').click(function(){
                $("#indate_end").focus();
            });
            $("#unitprice_begin").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker7').click(function(){
                $("#unitprice_begin").focus();
            });
            $("#unitprice_end").datepicker({   
            format: "twymmdd",
            language: 'zh-TW'
        });
            $('#datepicker8').click(function(){
                $("#unitprice_end").focus();
            });
        });

    </script>
    <script>
        function doClear(){
          document.all.yerly.value = "";
          document.all.class.value = "";
          document.all.name.value = "";
          document.all.branchname.value = "";
          document.all.term.value = "";
          document.all.branch.value = "";
          document.all.process.value = "";
          document.all.sponsor.value = "";
          document.all.type.value = "";
          document.all.traintype.value = "";
          document.all.typeone.value = "";
          document.all.sdate_begin.value = "";
          document.all.sdate_end.value = "";
          document.all.edate_begin.value = "";
          document.all.sdate_end.value = "";
          document.all.indate_begin.value = "";
          document.all.indate_end.value = "";
        }
        
        function unitprice(){
            $('#unitprice').modal('show');
        }
        function checkunitprice(){
            if($("#unitprice_begin").val() > $("#unitprice_end").val() ){
                alert('日期錯誤!');
            }else{
                $("#form").submit();
            }
        }

    </script>
@endsection