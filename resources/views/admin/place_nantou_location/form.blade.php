@extends('admin.layouts.layouts')
@section('content')
<style>
    .class_label{
        width:120px;
        text-align:center;
    }
</style>
    <?php $_menu = 'place_nantou';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">場地資料(南投)</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">場地資料(南投)列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>場地資料(南投)</h3>
                        </div>

                        <div class="card-body">
                            <div style="padding:20px;">
                                @if ($action == 'create')
                                    {{ Form::open(['method' => 'post', 'url' => "/admin/place_nantou_location"]) }}
                                @elseif ($action == "edit")
                                    {{ Form::model($classcode, ['method' => 'put', 'url' => "/admin/place_nantou_location/{$classcode->code}"]) }}
                                @endif 
                          
                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">代號</label>
                                  <div class="col-2">
                                   {{ Form::text('code', null, ['class' => "form-control", 'disabled' => ($action == "edit")]) }}
                                  </div>                                    
                                </div>                                 

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">名稱</label>
                                  <div class="col-2">
                                   {{ Form::text('name', null, ['class' => "form-control"]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">儲存</button>
                                        <a href='/admin/place_nantou_location'><button type="button" class="btn btn-danger">取消</button></a>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection