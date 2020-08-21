@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'lecture';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座資料匯入</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/lecture" class="text-info">講座資料維護列表</a></li>
                        <li class="active">講座資料匯入</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->

            {!! Form::open([ 'method'=>'post', 'url'=>'/admin/lecture/import','enctype'=>'multipart/form-data', 'id'=>'form']) !!}


            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">講座資料匯入</h3></div>
                    <div class="card-body pt-4">

                        <!-- 上傳個資授權書 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">講座資料匯入</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="Certificate" name="Certificate" readonly="readonly" value="" >
                              <button type="button" OnClick='javascript:$("#upload").click();'class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>選取檔案</button>
                              <input type="file" class="btn btn-sm btn-info" id="upload" name="aCSV" style="display:none;" />
                              <?php if(env('APP_URL') == 'http://172.16.10.18/'){ ?>
                              <a target="_blank" href="{{substr(env('APP_URL'), 0, -1)}}:8080/teacher_csv.php">
                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">填寫基本資料下載CSV</button>
                              </a>
                              <?php }else{ ?>
                              <a target="_blank" href="{{substr(env('WEB_URL'), 0, -1)}}:8080/teacher_csv.php">
                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">填寫基本資料下載CSV</button>
                              </a>
                              <?php } ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">是否覆蓋資料</label>
                            <div class="col-sm-10">
                                <select id="update" name="update" class=" form-control input-max">
                                    <option value="N" >否</option>
                                    <option value="Y" >是</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <?php if(!empty($import)){ ?>
                            <?=$import;?>
                            <?php } ?>
                        </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>匯入</button>
                        <a href="/admin/lecture">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
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
	   function readURL(input) {
              if (input.files && input.files[0]) {
                  $('#Certificate').val(input.files[0].name);
				  }
            }

        $("#upload").change(function() {
          readURL(this);
        });
   });

</script>
@endsection
