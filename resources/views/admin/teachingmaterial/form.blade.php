@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'teaching_material';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座授課及教材資料表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/teaching_material" class="text-info">講座授課及教材資料列表</a></li>
                        <li class="active">講座授課及教材資料表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/teaching_material/'.$data->id,  'enctype'=>'multipart/form-data','id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/teaching_material/', 'enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座授課及教材資料表單</h3></div>
                    <div class="card-body pt-4">
                        <?php if(isset($serno)){?>
                        <input type="hidden" id="m01serno" name="m01serno" class="form-control" autocomplete="off" value="{{$serno}}">
                        <?php }?>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">教材名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入教材名稱" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" maxlength="30" required>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上傳檔案</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="filename" name="filename" readonly="readonly" value="{{ old('filename', (isset($data->filename))? $data->filename : '') }}" required >
                              <button type="button" OnClick='javascript:$("#upload1").click();'class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>選取檔案</button>
                              <?php if(isset($data->filename)){ ?>
                              <a target="_blank" href="/Uploads/teachingmaterial/{{ $data->filename }}">
                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" >下載檔案</button>
                              </a>
                              <?php } ?>
                              <input type="file" class="btn btn-sm btn-info" id="upload1" name="upload1" style="display:none;" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上傳授權書</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="COA" name="COA" readonly="readonly" value="{{ old('COA', (isset($data->COA))? $data->COA : '') }}" >
                              <button type="button" OnClick='javascript:$("#upload2").click();'class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>選取檔案</button>
                              <?php if(isset($data->COA)){ ?>
                              <a target="_blank" href="/Uploads/teachingmaterial/{{ $data->COA }}">
                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" >下載授權書</button>
                              </a>
                              <?php } ?>
                              <input type="file" class="btn btn-sm btn-info" id="upload2" name="upload2" style="display:none;" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2"></label>
                            <div class="col-sm-3">
                                <input type="checkbox" id="online" name="online" value="Y" {{ old('online', (isset($data->online))? $data->online : 1) == 'Y'? 'checked' : '' }} > 是否上網
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                
                         <a href="javascript:history.go(-1)">
                 
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回上一頁</button>
                        </a>
                        <?php if(isset($data->id)){?>
                        <span onclick="$('#del_form').attr('action', '/admin/teaching_material/{{ $data->id }}/from');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
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
<script>
  $(document).ready(function () {
	   function readURL1(input) {
              if (input.files && input.files[0]) {
                  $('#filename').val(input.files[0].name);
				  }
            }

        $("#upload1").change(function() {
          readURL1(this);
        });

        function readURL2(input) {
              if (input.files && input.files[0]) {
                  $('#COA').val(input.files[0].name);
                  }
            }

        $("#upload2").change(function() {
          readURL2(this);
        });
   });
  function submitform(){

        submitForm('#form');
   }

</script>
@include('admin/layouts/list/del_modol')
@endsection

