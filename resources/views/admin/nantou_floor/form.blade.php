@extends('admin.layouts.layouts')
@section('content')
<style>
    .class_label{
        width:120px;
        text-align:center;
    }
</style>
    <?php $_menu = 'nantou_floor';?>

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
                                    {{ Form::open(['method' => 'post', 'url' => "/admin/nantou_floor"]) }}
                                @elseif ($action == "edit")
                                    {{ Form::model($floor, ['method' => 'put', 'url' => "/admin/nantou_floor/{$floor->id}"]) }}
                                @endif 
                          
                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">代碼</label>
                                  <div class="col-2">
                                   {{ Form::text('floorno', null, ['class' => "form-control", 'disabled' => ($action == "edit")]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">樓別名稱</label>
                                  <div class="col-2">
                                   {{ Form::text('floorname', null, ['class' => "form-control"]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                  <label for="colFormLabelSm" class="col-form-label class_label">教室別代碼</label>
                                  <div class="col-2">
                                   {{ Form::select('croomclsno', $cls, null, ['class' => "form-control custom-select"]) }}
                                  </div>                                    
                                </div> 

                                <div class="form-group row">
                                    <div class="col-12">
                                        <button class="btn btn-primary">儲存</button>
                                        <a href='/admin/nantou_floor'><button type="button" class="btn btn-danger">取消</button></a>
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