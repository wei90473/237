@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'recommend_user';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">機關個人帳號</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/recommend_user" class="text-info">機關個人帳號列表</a></li>
                        <li class="active">設定聯絡窗口</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>設定聯絡窗口</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <div class="form-group row">
                                                <div class="input-group col-10">
                                                    <!-- 薦送機關代碼 -->
                                                    <?php $list = $base->getDBList('M17tb', ['enrollorg', 'enrollname']);?>
                                                    <div class="pull-left mobile-100 mr-1 mb-3">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">薦送機關代碼</span>
                                                            </div>
                                                            <select id="enrollorg" name="enrollorg" class="browser-default custom-select">
                                                                @foreach($list as $va)
                                                                    <option value="{{ $va->enrollorg }}" {{ old('enrollorg', (isset($queryData['enrollorg']))? $queryData['enrollorg'] : '') == $va->enrollorg? 'selected' : '' }}>{{ $va->enrollorg }} {{ $va->enrollname }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        @if ( isset($data) )
                                        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/recommend_user/'.$queryData['enrollorg'].'/active', 'id'=>'form2']) !!}
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>薦送機關名稱</th>
                                                <th>身分證號</th>
                                                <th>姓名</th>
                                                <th>聯絡窗口</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($data as $va)
                                                <tr>
                                                    <td>{{ $va->enrollname }}</td>
                                                    <td>{{ $va->userid }}</td>
                                                    <td>{{ $va->username }}</td>
                                                    <td><input type="checkbox" name="keyman{{ $va->userid }}" value="Y" {{ old('keyman', ($va->keyman == 'Y')? 'checked' : '' )}} > 聯絡窗口</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        {!! Form::close() !!}
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" onclick="submitForm('#form2');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                                        <a href="/admin/recommend_user">
                                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                                        </a>
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

<script>
    function doClear(){
      document.all.enrollorg.value = "";
      document.all.enrollname.value = "";
      document.all.email.value = "";
      document.all.userid.value = "";
    }
</script>