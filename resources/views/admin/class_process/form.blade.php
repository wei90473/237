@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'class_process';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班務流程指引維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/class_process" class="text-info">班務流程指引維護列表</a></li>
                        <li class="active">班務流程指引維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_process/'.$data->id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/class_process/store', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">班務流程指引維護</h3></div>
                    <div class="card-body pt-4">

                        <!-- 課程名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">流程名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入流程名稱" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="50">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">套用院區</label>
                            <div class="col-sm-3">
                                <select id="branch" name="branch" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ old('branch', (isset($data->branch))? $data->branch : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 證號別 -->
                        <div class="form-group row">


                            <label class="col-sm-2 control-label text-md-right pt-2">套用班別類型</label>
                            <div class="col-sm-3">
                                <select id="process" name="process" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.process') as $key => $va)
                                        <option value="{{ $key }}" {{ old('process', (isset($data->process))? $data->process : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <label class="col-sm-1 control-label text-md-right pt-2"></label>
                            <div class="col-sm-3">
                                <input type="checkbox" id="preset" name="preset" style="min-width:20px; margin-left:5px;" <?=( isset($data->preset) && $data->preset=='Y')?'checked':'';?> value="Y" >
                                設定為該班別類型的預設流程
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/class_process">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/class_process/{{ $data->id }}');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}
            <div class="col-md-10 offset-md-1 p-0">
            <div class="card">
            	<?php if(isset($data)){ ?>
                <div class="card-header"><h3 class="card-title">工作項目列表</h3></div>
                <div class="card-body pt-4">
                    <div class="float-left">
                        <a href="/admin/class_process/create_job?class_process={{ $data->id }}">
                            <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>新增工作項目</button>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table id="data_table" class="table table-bordered mb-0">
                            <thead>
                            <tr>
                                <th class="text-center" width="70">功能</th>
                                <th>名稱</th>
                                <th>系統功能</th>
                                <th>工作期限</th>
                                <th>Email提醒</th>
                                <th>凍結資料</th>
                                <th>說明文件</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($sub_data as $va)
                                <tr>
                                    <!-- 修改 -->
                                    <td class="text-center">
                                        <a href="/admin/class_process/edit_job/{{ $va['id'] }}" data-original-title="修改">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </td>
                                    <td>{{ $va['name'] }}</td>
                                    <td>{{ config('app.job.'.$va['job']) }}</td>
                                    <td>{{ config('app.deadline.'.$va['deadline']) }}{{ $va['deadline_day'] }}</td>
                                    <td>
                                        <?php if($va['email'] == 'Y') {?>
                                        是
                                        <?php }else{ ?>
                                        否
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($va['freeze'] == 'Y') {?>
                                        是
                                        <?php }else{ ?>
                                        否
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if(isset($va['file']) && !empty($va['file'])){ ?>
                                        <a target="_blank" href="/admin/class_process/download_file/{{ $va['id'] }}">
                                        <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" >下載說明文件</button>
                                        </a>
                                        <?php } ?>
                                    </td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php } ?>
            </div>
            </div>

        </div>
    </div>


    <!-- 圖片 -->
    @include('admin/layouts/form/image')
    @include('admin/layouts/list/del_modol')

@endsection

	@section('js')
    <script type="text/javascript">

        function submitform(){
	        submitForm('#form');
	   }

    </script>
    @endsection