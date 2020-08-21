@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'dataexport';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">資料匯出處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">資料匯出作業</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>資料匯出處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                    </div>
                                    <form method="post" action="{{$url}}" id="form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="col-md-12 row ">
                                            <label class="label">主旨:</label>
                                            <input type="text"  name="title">
                                        </div>
                                        <div class="col-md-12 row mt-4">
                                            <label class="label">內容:</label>
                                            <textarea name="content" id="content" cols="50" rows="5"></textarea>
                                            
                                        </div>

                                        <div class="col-md-12 row mt-4">
                                            <button type="submit" class="btn btn-info">寄送</button>
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
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="/backend/plugins/ckeditor/ckeditor.js"></script>
<script>
                                                // Replace the <textarea id="editor1"> with a CKEditor
                                                // instance, using default configuration.
                                                CKEDITOR.replace( 'content' );
                                            </script>
@endsection

