@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'signup';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">線上報名設定</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">線上報名設定列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>線上報名設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form signup="get" id="search_form">

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

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    {!! Form::open([ 'method'=>'put', 'url'=>'/admin/signup', 'id'=>'form']) !!}

                                        <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                        <input type="hidden" name="term" value="{{ $queryData['term'] }}">

                                        @if($dateData)
                                            <div class="table-responsive">

                                                <hr class="bg-secondary">

                                                <!-- 參加聯合派訓 -->
                                                <div class="form-group row">
                                                    <label class="col-md-2 col-form-label text-md-right">參加聯合派訓</label>
                                                    <div class="col-md-10">
                                                        <select id="notice" name="notice" class="select2 form-control select2-single input-max">
                                                            <option value="Y" {{ $dateData->notice == 'Y'? 'selected' : '' }}>是</option>
                                                            <option value="N" {{ $dateData->notice == 'N'? 'selected' : '' }}>否</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- 報名開始日期 -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label text-md-right pt-2">報名開始日期<span class="text-danger">*</span></label>
                                                    <div class="col-sm-10">

                                                        <div class="input-group roc-date input-max">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">民國</span>
                                                            </div>

                                                            <input type="text" class="form-control roc-date-year" maxlength="3" name="sdate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($dateData->sdate))? mb_substr($dateData->sdate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">年</span>
                                                            </div>

                                                            <input type="text" class="form-control roc-sdate-month" maxlength="2" name="sdate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($dateData->sdate))? mb_substr($dateData->sdate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">月</span>
                                                            </div>

                                                            <input type="text" class="form-control roc-sdate-day" maxlength="2" name="sdate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($dateData->sdate))? mb_substr($dateData->sdate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">日</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- 報名結束日期 -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 control-label text-md-right pt-2">報名結束日期<span class="text-danger">*</span></label>
                                                    <div class="col-sm-10">

                                                        <div class="input-group roc-date input-max">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">民國</span>
                                                            </div>

                                                            <input type="text" class="form-control roc-date-year" maxlength="3" name="edate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($dateData->edate))? mb_substr($dateData->edate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">年</span>
                                                            </div>

                                                            <input type="text" class="form-control roc-edate-month" maxlength="2" name="edate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($dateData->edate))? mb_substr($dateData->edate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">月</span>
                                                            </div>

                                                            <input type="text" class="form-control roc-edate-day" maxlength="2" name="edate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($dateData->edate))? mb_substr($dateData->edate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">日</span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>



                                            </div>
                                        @endif

                                        @if($data)
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>機關代碼</th>
                                                    <th>機關名稱</th>
                                                    <th>年度分配人數</th>
                                                    <th>線上分配人數</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($data as $va)
                                                    <tr>

                                                        <td class="text-left">{{ $va->機關代碼 }}</td>
                                                        <td>{{ $va->機關名稱 }}</td>
                                                        <td>{{ $va->年度分配人數 }}</td>
                                                        <td><input type="text" name="value[{{ $va->機關代碼 }}]" value="{{ $va->線上分配人數 }}"></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            @if($dateData)
                                <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                            @endif
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
                url: '/admin/signup/getterm',
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