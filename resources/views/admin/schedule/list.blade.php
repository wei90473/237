@extends('admin.layouts.layouts')
@section('content')
    <?php $_menu = 'schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">訓練排程處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">訓練排程處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>訓練排程處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                            <!-- 年度 -->

                                            @include('gerneral.class_list')                             

                                            <!-- 排序 -->
                                            <!-- <input type="hidden" id="_sort_field" name="_sort_field" value=""> -->
                                            <!-- <input type="hidden" id="_sort_mode" name="_sort_mode" value=""> -->
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="">
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>  
                                            <a href="/admin/schedule/create">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增訓練排程</button>
                                            </a>
                                            <a href="/admin/schedule" ><button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">重設條件</button></a>
                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal1"><i class="fa fa-plus fa-lg pr-2"></i>批次增刪作業</button>
                                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#t04tb_import"><i class="fa fa-plus fa-lg pr-2"></i>匯入</button>
                                            <a href="/admin/schedule/details">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal3"><i class="fa fa-plus fa-lg pr-2"></i>排程明細</button>
                                            </a>
                                            <!--
                                            <a href="/admin/calendar">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal3"><i class="fa fa-pencil fa-lg pr-2"></i>調整行事曆</button>
                                            </a>
                                            -->                                            
                                        </form>
                                    </div>

                                    <div class="float-md-right">

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th width="100">功能</th>
                                                <th class="text-center" width="70">班號</th>
                                                <th width="500">班別名稱</th>
                                                <th>期別</th>
                                                <th>人數</th>
                                                <th>教室</th>
												<th>開課日期</th>
                                                <th>結束日期</th>
                                                <th>班務人員</th>
                                                <th class="text-center" width="150"></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $t04tb)
                                                <tr>
                                                    <td><a href="/admin/schedule/{{ $t04tb->class }}/{{ $t04tb->term }}/edit"><button class="btn btn-primary btn-sm mb-3 mb-md-0">編輯</button><a/></td>
                                                    <td class="text-center">
                                                        {{ $t04tb->class }}
                                                    </td>
                                                    <td>{{ $t04tb->t01tb_name }}</td>
                                                    <td>{{ $t04tb->term }}</td>
                                                    <td>{{ $t04tb->quota }} </td>
                                                    <td>
                                                        @if (!empty($t04tb->site_branch))
                                                        {{ config('database_fields.m14tb')['branch'][$t04tb->site_branch] }}
                                                        @endif 

                                                        @if ($t04tb->site_branch == 3)
                                                        {{ $t04tb->location }}
                                                        @else
                                                        {{ $t04tb->site }}
                                                        @endif 
                                                    </td>
													<td>{{ $t04tb->sdateformat }}</td>
													<td>{{ $t04tb->edateformat }}</td>
                                                    <td>{{ $t04tb->m09tb_username }}</td>
                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                    <a href="/admin/calendar?class={{$t04tb->class}}&term={{$t04tb->term}}">
                                                        <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" data-toggle="modal" data-target="#exampleModal3"><i class="fa fa-pencil fa-lg pr-2"></i>調整行事曆</button>
                                                    </a> 
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Modal1 批次增刪作業 -->
                                    <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            {!! Form::open(['method' => 'POST', 'url' => '/admin/schedule/batchOperate']) !!}
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">批次增刪作業</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="card-header"><h3 class="card-title">輸入批次作業年度</h3></div>
                                                        <label >年度：</label>
                                                        <input type="text" name="yerly">
                                                        <br/>
                                                    </div>    
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="operate" value="insert" class="btn btn-success ml-auto">批次新增</button>
                                                    <button type="submit" name="operate" value="delete" class="btn btn-danger mx-0" onclick="return confirm('確定要批次刪除嗎?')">批次刪除</button>
                                                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">取消</button>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                    <!--  匯入 -->
                                    <div class="modal fade" id="t04tb_import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            {!! Form::open(['method' => 'POST', 'url' => '/admin/schedule/import', 'enctype' => "multipart/form-data"]) !!}
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">訓練排程匯入</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="card-body pt-4 text-center">
                                                        <div class="card-header"><h3 class="card-title">訓練排程匯入</h3></div>
                                                        <label >匯入檔案：</label>
                                                        <input type="file" class="form-control" name="import_file">
                                                        <br/>
                                                    </div>    
                                                </div>
                                                <div>
                                                    <div>
                                                    說明：<br>
                                                    檔案內容第一列為欄位中文說明，請統一保留
                                                    </div>
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                            <th>欄位名稱</th>
                                                            <th>必填</th>
                                                            <th>說明</th>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-center">班號</td>
                                                                <td class="text-center">Y</td>
                                                                <td>請輸入完整班號共7碼，例：110999A</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">期別</td>
                                                                <td class="text-center">Y</td>
                                                                <td>請輸入期別，1~99</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">開課日期</td>
                                                                <td class="text-center">Y</td>
                                                                <td>YYYMMDD，例：1090325</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">主教室(台北)</td>
                                                                <td class="text-center"></td>
                                                                <td>從下拉式選單中選擇 (三項主教室可都不填，或擇一輸入)</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-center">主教室(南投)</td>
                                                                <td class="text-center"></td>
                                                                <td>從下拉式選單中選擇 (三項主教室可都不填，或擇一輸入)</td>
                                                            </tr>                                                                                                                                                                                                                                                
                                                            <tr>
                                                                <td class="text-center">主教室外地班</td>
                                                                <td class="text-center"></td>
                                                                <td>如果屬於外地班，請輸入上課地點 (三項主教室可都不填，或擇一輸入)</td>
                                                            </tr> 
                                                            <tr>
                                                                <td class="text-center">辦班人員</td>
                                                                <td class="text-center"></td>
                                                                <td>從下拉式選單中選擇</td>
                                                            </tr> 
                                                            <tr>
                                                                <td class="text-center">部門</td>
                                                                <td class="text-center"></td>
                                                                <td>從下拉式選單中選擇</td>
                                                            </tr>                                                                                                                         
                                                        </tbody>
                                                    </table>
                                                </div>                                                  
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success ml-auto">匯入</button>
                                                    <a href="/admin/schedule/importExample"><button type="button" class="btn btn-primary">下載範例</button></a>
                                                    <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">取消</button>
                                                </div>                                              
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>

                                    @if($data)
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($data)
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

@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function() {
        $("#sdate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker1').click(function(){
            $("#sdate").focus();
        });

        $("#edate").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker2').click(function(){
            $("#edate").focus();
        });


        $("#graduate_start_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker3').click(function(){
            $("#graduate_start_date").focus();
        });

        $("#graduate_end_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });

        $('#datepicker4').click(function(){
            $("#graduate_end_date").focus();
        });


        $("#training_start_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker5').click(function(){
            $("#training_start_date").focus();
        });

        $("#training_end_date").datepicker({
            format: "twymmdd",
            language: 'zh-TW'
        });
        $('#datepicker6').click(function(){
            $("#training_end_date").focus();
        });


    });
</script>
@endsection