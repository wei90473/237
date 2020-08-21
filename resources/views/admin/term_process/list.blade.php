@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'term_process';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班務流程指引維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">班務流程指引維護列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>班務流程指引維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                        <form method="get" id="search_form">
                                        <input type="hidden" id="search" name="search" class="form-control" value="search">

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">年度</span>
                                                    </div>
                                                    <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                        <option></option>
                                                        @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                                                            <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <div class="input-group col-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">辦班完成狀態</span>
                                                    </div>
                                                    <select class="form-control select2" name="process_complete">
                                                        @foreach(config('app.class_complete') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['process_complete'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="float-md mobile-100 row mr-1 mb-3">
                                                <div class="input-group col-4">
                                                    <input type="checkbox" id="job_complete" name="job_complete" style="min-width:20px; margin-left:5px;" <?=($queryData['job_complete']=='N')?'checked':'';?> value="N" >
                                                    <span>僅顯示未完成的工作</span>
                                                </div>
                                            </div>

                                            <div class="float-left">
                                                <!-- 查詢 -->
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                <!-- 重設條件 -->
                                                <!-- <button class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()">重設條件</button> -->

                                            </div>
                                        </form>
                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <div class="table-responsive">
                                        <div class="accordion" id="accordionExample">
                                        @foreach($data as $va)
                                          <div class="card">
                                            <div class="card-header" id="heading">
                                              <h2 class="mb-0">
                                                <button style="font-size: 16px;" class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{ $va['class'].$va['term'] }}" aria-expanded="true" aria-controls="collapse">
                                                  {{ $va['class'].$va['name'] }}第{{ $va['term'] }}期 &emsp; 課程起訖日期 {{ $va['sdate'] }} ~ {{ $va['edate'] }}
                                                </button>
                                              </h2>
                                            </div>

                                            <div id="collapse{{ $va['class'].$va['term'] }}" class="collapse" aria-labelledby="heading" data-parent="#accordionExample">
                                              <div class="card-body">
                                                <table id="data_table" class="table table-bordered mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center" width="70">功能</th>
                                                        <th>工作項目</th>
                                                        <th>工作期限</th>
                                                        <th>工作完成狀態</th>
                                                        <th>資料凍結</th>
                                                        <th>說明文件</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach($va['job_data'] as $job_data)
                                                        <tr>
                                                            <!-- 修改 -->
                                                            <td class="text-center">
                                                                <a href="{{ $job_data['url'] }}" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                                    前往
                                                                </a>
                                                            </td>
                                                            <td>{{ $job_data['name'] }}</td>
                                                            <td>{{ $job_data['deadline'] }}</td>
                                                            <td>
                                                                <select class="form-control select2" name="complete" onchange="completeChange('{{ $job_data['class_process_id'] }}', '{{ $job_data['id'] }}', '{{ $va['class'] }}', '{{ $va['term'] }}')">
                                                                    @foreach(config('app.job_complete') as $key => $job_complete)
                                                                        <option value="{{ $key }}" {{ $job_data['complete'] == $key? 'selected' : '' }}>{{ $job_complete }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <?php if($job_data['freeze'] == 'Y'){?>
                                                                是
                                                                <?php }else{ ?>
                                                                否
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php if(isset($job_data['file']) && !empty($job_data['file'])){ ?>
                                                                <a target="_blank" href="/admin/term_process/download_file/{{ $job_data['id'] }}">
                                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" >下載說明文件</button>
                                                                </a>
                                                                <?php } ?>
                                                            </td>

                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                              </div>
                                            </div>
                                          </div>
                                        @endforeach
                                        </div>

                                    </div>

                                </div>
                            </div>
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
    <script type="text/javascript">
    function doClear(){
      document.all.name.value = "";
      document.all.branch.value = "";
    }

    function completeChange(class_process_id, class_process_job_id, classva, termva) {

        $.ajax({
            type: "post",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url: '/admin/term_process/completeChange',
            data: { class_process_id: class_process_id, class_process_job_id: class_process_job_id, class: classva, term: termva},
            success: function(data){
                alert(data);
            },
            error: function() {
                alert('更新失敗');
            }
        });
    }
    </script>
@endsection