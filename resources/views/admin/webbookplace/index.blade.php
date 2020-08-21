@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
  

    <?php $_menu = 'webbookplace';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路預約場地審核處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">網路預約場地審核處理</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>網路預約場地審核處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="search-float" style="width:100%;">
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <a href="/admin/webbookplaceTaipei"><button class="btn" style="background-color:#317eeb;color:white;" type="button">臺北院區</button></a>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="input-group">
                                                    <a href="{{route('webbook.Nantou.get')}}"><button class="btn" style="background-color:#317eeb;color:white;" type="button">南投院區</button></a>
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
        </div>
    </div>
@endsection
