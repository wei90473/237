@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'classes';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">批次增刪班別</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes" class="text-info">班別資料查詢</a></li>
                        <li class="active">批次增刪班別</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">批次增刪班別</h3></div>
                    <div class="card-body pt-4">
                    <fieldset style="border:groove; padding: inherit">
                        <legend>批次新增或刪除</legend>
                        <?php $list = $base->SQL('SELECT DISTINCT category,name FROM s03tb WHERE alias = "Y" ORDER BY category')?>
                        <!-- 年度 -->
                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                <div class="pull-left input-group-prepend">
                                    <span class="input-group-text">年度</span>
                                </div>
                                <div class="input-group col-4">
                                    <select class="browser-default custom-select" name="yerly" id="yerly" >
                                    @foreach($queryData['choices'] as $key => $va)
                                        <option value="{{ $key }}" >{{ $va }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                        <!--**入口網站開班方式 -->
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" onclick="BatchAdd()">新增</button>
                            <button type="button" class="btn btn-primary" onclick="BatchDel()">刪除</button>
                        </div>
                    </fieldset>


                    <fieldset style="border:groove; padding: inherit">
                                <legend>csv檔匯出及匯入</legend>
                                <?php $list = $base->SQL('SELECT DISTINCT category,name FROM s03tb WHERE alias = "Y" ORDER BY category')?>
                                <!-- 年度 -->
                                <div class="float-md mobile-100 row mr-1 mb-3 ">
                                    <div class="pull-left input-group-prepend">
                                        <span class="input-group-text">年度</span>
                                    </div>
                                    <div class="input-group col-4">
                                        <select class="browser-default custom-select" name="yerlycsv" id="yerlycsv" >
                                        @foreach($queryData['choices'] as $key => $va)
                                            <option value="{{ $key }}" >{{ $va }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary mr-1 " data-dismiss="modal" onclick="exportclass()">挑選匯出班別</button>
                                    <input type="hidden" name="export" id="export" value="0">
                                    <input type="hidden" name="times" id="times" value="0">
                                    <input type="hidden" name="yerlyhide" id="yerlyhide" value="0">
                                    <button type="button" class="btn btn-primary mr-1 " data-dismiss="modal" onclick="ClassOutput()">匯出CSV檔</button>
                                </div>

                                <!-- 匯入 -->
                                <div class="form-group float-md mobile-100 row mr-1 mb-3 ">
                                    <div class="pull-left input-group-prepend">
                                        <span class="input-group-text mr-2">匯入CSV檔</span>
                                    </div>
                                    <!-- 上傳 -->
                                       {!! Form::open(['method' => 'post', 'enctype'=>'multipart/form-data','url'=>'/admin/classes/batchimport' ,'id' => 'form' ])  !!}
                                        <input type="file" name="csv_file" value="點選上傳檔案">
                                        <!-- 匯入 -->
                                        <button type="button" class="btn btn-primary mr-1 " data-dismiss="modal" onclick="checkfile()">開始匯入</button>
                                        {!! Form::close() !!}
                                </div>
                    </fieldset>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <!-- 來源:../example/batch.xlsx -->
                            <tbody>
                                <tr height="73" style="mso-height-source:userset;height:54.75pt">
                                    <td colspan="7" height="73" class="xl76" width="1492" style="height:54.75pt;
                          width:1119pt">說明：<br>
                            1.檔案請存成CSV(逗號分隔)<br>
                            2.檔案內容第一列為欄位中文說明，請統一保留</td>
                                </tr>
                                 <tr height="44" style="height:33.0pt">
                                  <td height="44" class="xl72" style="height:33.0pt">欄位名稱</td>
                                  <td class="xl73" style="border-left:none">必填</td>
                                  <td class="xl73" style="border-left:none">格式</td>
                                  <td class="xl74" width="157" style="border-left:none;width:118pt">年度訓練計畫班期<br>
                                    年度臨時增開班期</td>
                                  <td class="xl73" style="border-left:none">開放自由報名班期</td>
                                  <td class="xl73" style="border-left:none">委訓班期</td>
                                  <td class="xl72" style="border-left:none">說明</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">班號</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">總共7碼，前6碼數字，最後一碼A或B表示辦班院區(A:臺北院區、B:南投院區)</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">班別名稱</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">班別性質</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">23:領導力發展<span style="mso-spacerun:yes">&nbsp; </span>24:政策能力訓練<span style="mso-spacerun:yes">&nbsp; </span>25:部會業務知能訓練<span style="mso-spacerun:yes">&nbsp; </span>26:自我成長及其他</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">上課方式</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:密集式<span style="mso-spacerun:yes">&nbsp; </span>2:分散式(每周一、三、五上課)<span style="mso-spacerun:yes">&nbsp; </span>3:分散式(每週二、四上課)<span style="mso-spacerun:yes">&nbsp; </span>4:其他</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">官等區分</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:特任<span style="mso-spacerun:yes">&nbsp; </span>2:簡任<span style="mso-spacerun:yes">&nbsp; </span>3:簡任或薦任<span style="mso-spacerun:yes">&nbsp; </span>4:薦任<span style="mso-spacerun:yes">&nbsp; </span>5:薦任或委任<span style="mso-spacerun:yes">&nbsp; </span>6:委任<span style="mso-spacerun:yes">&nbsp; </span>7:一般(不限官等)</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">班別類型</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:自辦班<span style="mso-spacerun:yes">&nbsp; </span>2:委訓班<span style="mso-spacerun:yes">&nbsp; </span>3:合作辦理<span style="mso-spacerun:yes">&nbsp; </span>4:外地班<span style="mso-spacerun:yes">&nbsp; </span>5:巡迴研習</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">訓練性質</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:中高階公務人員訓練<span style="mso-spacerun:yes">&nbsp; </span>2:人事人員專業訓練<span style="mso-spacerun:yes">&nbsp; </span>3:一般公務人員訓練</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">是否住宿</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">Y:因課程需求，得申請住宿<span style="mso-spacerun:yes">&nbsp; </span>X:不提供住宿<span style="mso-spacerun:yes">&nbsp; </span>N:惟符合條件者得申請</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">訓期類型</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:週<span style="mso-spacerun:yes">&nbsp; </span>2:天<span style="mso-spacerun:yes">&nbsp; </span>3:小時</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">訓期</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">每期人數</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl70" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">每日上課時數</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">網頁公告</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">Y:是<span style="mso-spacerun:yes">&nbsp; </span>N:否</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週一上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周一要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週二上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周二要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週三上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周三要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週四上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周四要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週五上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周五要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週六上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周六要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">週日上課</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，周日要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">含國定假日</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">上課方式是其他的時候，國定假日要上課填Y，其餘情況皆填N</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">訓練總天數</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">班別類別</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">請參照終身學習入口網站班別類別的代碼</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">入口網站開班方式</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">Y:已開班<span style="mso-spacerun:yes">&nbsp; </span>N:未開班</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">學習性質</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:數位<span style="mso-spacerun:yes">&nbsp; </span>2:實體<span style="mso-spacerun:yes">&nbsp; </span>3:混成</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">數位時數</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl70" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">實體時數</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl70" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">訓練績效計算方式</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:由學員名冊計算<span style="mso-spacerun:yes">&nbsp; </span>2:由承辦人員補登</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">報名方式</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:年度訓練計畫班期<span style="mso-spacerun:yes">&nbsp; </span>2:年度臨時增開班期<span style="mso-spacerun:yes">&nbsp; </span>3:開放自由報名班期</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">第一組主管</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">空白:不限<span style="mso-spacerun:yes">&nbsp; </span>Y:主管<span style="mso-spacerun:yes">&nbsp; </span>N:非主管</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">第一組人事人員</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">空白:不限<span style="mso-spacerun:yes">&nbsp; </span>Y:人事人員<span style="mso-spacerun:yes">&nbsp; </span>N:非人事人員</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">第一組職等</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">職等代碼請用逗號隔開</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">第二組主管</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">空白:不限<span style="mso-spacerun:yes">&nbsp; </span>Y:主管<span style="mso-spacerun:yes">&nbsp; </span>N:非主管</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">第二組人事人員</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">空白:不限<span style="mso-spacerun:yes">&nbsp; </span>Y:人事人員<span style="mso-spacerun:yes">&nbsp; </span>N:非人事人員</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">第二組職等</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">職等代碼請用逗號隔開</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">參訓機關</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl71" style="border-top:none;border-left:none">0:全部<span style="mso-spacerun:yes">&nbsp; </span>1:中央<span style="mso-spacerun:yes">&nbsp; </span>2:地方<span style="mso-spacerun:yes">&nbsp; </span>3:限定</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">英文班別名稱</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">備註</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">參加對象</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">研習目標</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">研習方式</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl71" style="height:16.5pt;border-top:none">講座審查</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">Y:是<span style="mso-spacerun:yes">&nbsp; </span>N:否</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl71" style="height:16.5pt;border-top:none">辦班院區</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">  </td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl71" style="border-top:none;border-left:none">1:臺北院區<span style="mso-spacerun:yes">&nbsp; </span>2:南投院區</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl71" style="height:16.5pt;border-top:none">機關分區</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl75" style="border-top:none;border-left:none">開放報名對象為一般民眾及不限時，不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl71" style="border-top:none;border-left:none">1:苗栗以北(含花東離島)<span style="mso-spacerun:yes">&nbsp; </span>2:台中以南<span style="mso-spacerun:yes">&nbsp; </span>3:不分區</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl71" style="height:16.5pt;border-top:none">分班名稱</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl71" style="height:16.5pt;border-top:none">委訓機關代碼</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">班別類型是委訓班，則必填</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">類別1</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">請參照類別1代碼</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">公告備註</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">文數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl68" style="border-top:none;border-left:none">　</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">正取名額</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">開放自由報名班期 適用</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">後補名額</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">開放自由報名班期 適用</td>
                                 </tr>
                                 <tr height="22" style="height:16.5pt">
                                  <td height="22" class="xl68" style="height:16.5pt;border-top:none">開放報名對象</td>
                                  <td class="xl69" style="border-top:none;border-left:none">Y</td>
                                  <td class="xl69" style="border-top:none;border-left:none">數字</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl69" style="border-top:none;border-left:none">　</td>
                                  <td class="xl69" style="border-top:none;border-left:none">不須填寫</td>
                                  <td class="xl68" style="border-top:none;border-left:none">1:公務人員<span style="mso-spacerun:yes">&nbsp; </span>2:一般民眾<span style="mso-spacerun:yes">&nbsp; </span>3:不限</td>
                                 </tr>
                                 <!--[if supportMisalignedColumns]-->
                                 <tr height="0" style="display:none">
                                  <td width="147" style="width:110pt"></td>
                                  <td width="72" style="width:54pt"></td>
                                  <td width="72" style="width:54pt"></td>
                                  <td width="157" style="width:118pt"></td>
                                  <td width="360" style="width:270pt"></td>
                                  <td width="111" style="width:83pt"></td>
                                  <td width="573" style="width:430pt"></td>
                                 </tr>
                                 <!--[endif]-->
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="/admin/classes">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上頁</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    <!-- 挑選匯出班別選擇 modal -->
    <div class="modal fade bd-example-modal-lg exportclass" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog_80" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">挑選匯出班別</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="exportlist">
                
                    </table>
                </div>
                <div class="modal-footer">
                    <input type="checkbox" onclick="checkall(this)" checked>全選
                    <button type="button" class="btn btn-primary" onclick="setTimes()">確定</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script>
    function BatchAdd(){
        if($('#yerly').val()==''){
            alert('請輸入年度 !!');
            return ;
        };
        $.ajax( {
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            url:"/admin/classes/batchadd",
            data: { yerly: $('#yerly').val()},
            success: function(data){
                alert(data.msg);
            },
            error: function() {
                alert('Error');
            }
        });
    };
          
    function BatchDel(){
        if($('#yerly').val()==''){
            alert('請輸入年度 !!');
            return ;
        };
        Swal.fire({
            title: '確定要刪除?',
            text: "您將會刪除這個年度的全部資料",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '確定'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'get',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: "json",
                    url:"/admin/classes/batchdel",
                    data: { yerly: $('#yerly').val()},
                    success: function(data){
                         alert(data.msg);       
                    },
                    error: function() {
                        console.log('Ajax Error');
                    }
                });  
            }
        })
    };

    //匯出
    function ClassOutput() {
        // 確認有無挑選
        var exportlist = $('#export').val();
        if($('#export').val()!='0'){ //有挑選
            var yerly = $('#yerlyhide').val();
            window.location.href='/admin/classes/ClassOutput?yerly='+yerly+'&export='+exportlist;
        }else{//無挑選
            if($('#yerlycsv').val()==''){
                alert('請輸入年度 !!');
                return ;
            };
            var yerly = $('#yerlycsv').val();
            window.location.href='/admin/classes/ClassOutput?yerly='+yerly;
        }
    }    

    //挑選匯出班別
    function exportclass() {
        if($('#yerlycsv').val()==''){
            alert('請輸入年度 !!');
            return ;
        }
        var yerlyhide = $('#yerlycsv').val();
        var listHTML ='';
        var times = 0;
        $('#yerlyhide').val(yerlyhide);
        $.ajax( {
            type: 'get',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: 'json',
            url:"/admin/classes/exportclass",
            data: { yerly: $('#yerlycsv').val()},
            success: function(data){
                console.log(data);
                if(data.status ==2){
                    alert(data.msg);
                    return false;
                }else{
                   // listHTML += '<tr>';
                    $.each(data, function (key, value) {
                        
                        listHTML += '<tr><td><input type="checkbox" name="export" value="'+key+'" autocomplete="off" checked>'+value.class+value.name+'</td></tr>';
                        times++;
                        // if(times%5==0){
                        //     listHTML += '</tr><tr>';
                        // }
                    })
                    //listHTML += '</tr>';
                    $('#times').val(times);        
                    $("#exportlist").html(listHTML);
                    $(".exportclass").modal('show');
                }
            },
            error: function() {
                alert('Error');
            }
        });
    }

    // 設定勾選
    function setTimes() {
        var times = $('#times').val();  
        var class_str = '';
        for (i=0;i<times;i++){
            if($('input[name=export]')[i+1].checked) { 
                class_str+="1" ; 
            }else{
                class_str+="0" ;
            }
        }
        if(class_str!=''){
            class_str=class_str.substring(0,class_str.length-1);//去尾數
        }
        $("#export").val(class_str);//勾選list
        $(".exportclass").modal('hide');
    }
    //檢查檔案
    function checkfile(){
        if($("input[name=csv_file]").val()==''){
            alert("尚未選擇檔案");
            return false;
        }else{
            $("#form").submit();
        }
    }
    //全選
    function checkall(e)
    {
        for(i=0; i<$("input[type=checkbox]").length; i++ ){
             $("input[type=checkbox]")[i].checked = e.checked;
        }
    }
</script>
@endsection