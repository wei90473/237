@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'print';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座授課及教材資料維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/print">講座授課及教材資料查詢</a></li>
                        <li class="active">講座授課及教材資料維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座授課及教材資料維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <td>教材名稱：</td>
                                                <td><input type="text" class="form-control input-max"></td>
                                            </tr>
                                            <tr>
                                                <td>講座：</td>
                                                <td>
                                                    <select class="select2 form-control select2-single input-max" name="" id="">
                                                        <option value="123">123</option>
                                                        <option value="456">456</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>版本日期：</td>
                                                <td>
                                                    <input type="text" class="inputText">
                                                    <input type="text" class="inputText">
                                                    <input type="text" class="inputText">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>備註：</td>
                                                <td>
                                                    <input type="text" class="form-control input-max">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>資料上網：</td>
                                                <td>
                                                    <input type="radio" name="network" checked="checked">是
                                                    <input type="radio" name="network">否
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>數位典藏：</td>
                                                <td>
                                                    <input type="radio" name="digital" checked="checked">是
                                                    <input type="radio" name="digital">否
                                                </td>
                                            </tr>
                                            
                                        
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                                <a href="/admin/print">
                                <!--onclick="submitForm('#form');"-->
                                <button type="button"  class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                                </a>
                                <a href="/admin/print">
                                    <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                                </a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection