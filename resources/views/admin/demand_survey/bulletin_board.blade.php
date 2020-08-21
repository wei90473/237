@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_survey';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求調查處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_survey" class="text-info">需求調查處理</a></li>
                        <li class="active">公告文字維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_survey/bulletin_board', 'id'=>'form']) !!}
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">公告文字維護</h3></div>
                    <div class="card-body pt-4">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md">公告文字</label>
                            <textarea class="form-control" id="content" name="content">{{$data->announcement}}</textarea>
                        </div>
                        <div class="card-footer">
                            <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>更新</button>
                            <a href="/admin/demand_survey">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i>取消</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                
        
            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection
@section('js')
<!-- CK編輯器 -->
<script>
$(function() {
    CKEDITOR.replace('content', {
        language: 'zh',
        uiColor: '#AADBCB',
    });

});
function processData(){
   // getting data
   var data = CKEDITOR.instances.content.getData()
   alert(data);
  }
</script>
@endsection