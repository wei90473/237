@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'site_schedule';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">洽借場地班期排程維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/site_schedule" class="text-info">洽借場地班期排程處理</a></li>
                        <li class="active">洽借場地班期排程維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>洽借場地班期排程維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="">
                                        <form method="get" id="search_form">

                                                <!-- 班別 -->
                                                <div class="form-group row">
                                                    <label class="col-md-3 col-form-label text-md-right">班別</label>
                                                    <div class="col-md-7">
                                                        <select class="form-control select2" name="">
                                                           <option>請選擇</option>
                                                           <option>臺歐盟性別平權與人權系列培訓課程─性別主流化工作坊</option>
                                                           <option>108年行政院地方性平共識營</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- 期別 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">期別</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- 人數 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">人數</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input" value="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 開課日期 - 結束日期 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">開課日期 - 結束日期</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control date-range" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 教室 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">教室</label>
                                                        <div class="col-md-7">
                                                            <select class="form-control select2" name="">
                                                                <option>請選擇</option>
                                                                <option>B212</option>
                                                                <option>E202</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 辦班人員 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">辦班人員</label>
                                                        <div class="col-md-7">
                                                            <select class="form-control select2" name="">
                                                                <option>請選擇</option>
                                                                <option>may</option>
                                                                <option>rex</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 費用 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">費用</label>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input" value="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 部門 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">部門</label>
                                                        <div class="col-md-7">
                                                            <select class="form-control select2" name="">
                                                                <option>請選擇</option>
                                                                <option>訓導處</option>
                                                                <option>總務處</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 預約教室時段 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">預約教室時段</label>
                                                        <div class="col-md-7">
                                                            <input type="radio" name="time" value="早上">早上
                                                            <input type="radio" name="time" value="下午">下午
                                                            <input type="radio" name="time" value="晚間">晚間<br/>
                                                            <input type="radio" name="time" value="白天(早上、下午)">白天(早上、下午)
                                                            <input type="radio" name="time" value="全天(早上、下午、晚間)">全天(早上、下午、晚間)
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- 師資陣容 -->
                                                <div class="form-group row">
                                                    <div class="input-group">
                                                        <label class="col-md-3 col-form-label text-md-right">師資陣容</label>
                                                        <div class="col-md-7">
                                                            <input type="radio" name="teacher" value="單一老師">單一老師
                                                            <input type="radio" name="teacher" value="多位老師">多位老師
                                                        </div>
                                                    </div>
                                                </div>

                                                <div align="center">
                                                    <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                                                </div>
                                        </form>
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