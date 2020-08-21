@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teachingmethod';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教學教法資料維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teachingmethod" class="text-info">教學教法資料維護</a></li>
                        <li class="active">教學教法資料編輯</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teachingmethod/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/teachingmethod/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    @if ( isset($data) )
                    <div class="card-header"><h3 class="card-title">教學教法資料編輯</h3></div>
                    @else
                    <div class="card-header"><h3 class="card-title">教學教法資料新增</h3></div>
                    @endif
                    <div class="card-body pt-4">
                        <!-- 教學教法名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">教學教法名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入教學教法名稱" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                        </div>
                        <!-- 狀態 -->
                        <div class="form-group row">    
                            <label class="col-sm-2 control-label text-md-right pt-2">狀態<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="mode" name="mode" class="browser-default custom-select">
                                    @foreach(config('app.active') as $key => $va)
                                        <option value="{{ $key }}"  {{ old('mode', (isset($data->mode))? $data->mode : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach 
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <input type="hidden" id="checksignin" value="{{ isset($data->registration)? $data->registration : 0 }}">
                            <button type="button" class="btn btn-sm btn-info" onclick="submitForm('#form');"><i class="fa fa-save pr-2"></i>儲存</button>
                            <a href="/admin/teachingmethod">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i>取消</button>
                            </a>
                        </div>
                    </figcaption> 
                </figure>
            </div>
            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection

@section('js')
<script>

</script>
@endsection