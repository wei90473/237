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
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_process/update_job/'.$data['id'],'enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/class_process/store_job','enctype'=>'multipart/form-data', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">班務流程指引維護</h3></div>
                    <div class="card-body pt-4">
                    <input type="hidden" id="class_process_id" name="class_process_id" class="form-control" value="<?=(isset($class_process_id))?$class_process_id:'';?>">
                        <!-- 課程名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">工作項目名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入工作項目名稱" value="{{ old('name', (isset($data['name']))? $data['name'] : '') }}" autocomplete="off" required maxlength="50">
                            </div>

                            <label class="col-sm-2 control-label text-md-right pt-2">工作階段<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <select id="type" name="type" class="select2 form-control select2-single input-max" required>
                                    @foreach(config('app.work_type') as $key => $va)
                                        <option value="{{ $key }}" {{ old('type', (isset($data['type']))? $data['type'] : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">選擇系統功能</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" id="job_name" name="job_name" value="{{ old('job', (isset($data['job']))? config('app.job.'.$data['job']) : '') }}" autocomplete="off" readonly>
                                <button type="button" onclick="select_job();" class="btn btn-sm btn-info">選擇</button>
                            </div>
                            <input type="hidden" id="job" name="job" class="form-control" value="{{ old('job', (isset($data['job']))? $data['job'] : '') }}">
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">上傳說明文件</label>
                            <div class="col-sm-10">
                              <input type="text" class="form-control input-max" id="file" name="file" readonly="readonly" value="{{ old('file', (isset($data['file']))? $data['file'] : '') }}" >
                              <button type="button" OnClick='javascript:$("#upload").click();'class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>選取檔案</button>
                              <button type="button" onclick="submitform();" class="btn btn-sm btn-info">上傳</button>
                              <?php if(isset($data['file']) && !empty($data['file'])){ ?>
                              <a target="_blank" href="/admin/class_process/download_file/{{ $data['id'] }}">
                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0" >下載說明文件</button>
                              </a>
                              <input type="hidden" id="old_file" name="old_file" value="{{ $data['file'] }}" />
                              <?php } ?>
                              <input type="file" class="btn btn-sm btn-info" id="upload" name="upload" style="display:none;" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">工作期限</label>
                            <div class="col-sm-1">
                            	<input type="radio" id="deadline" name="deadline" <?=(isset($data['deadline']) && $data['deadline'] =='1')?'checked':'';?> <?=(!isset($data))?'checked':'';?> value="1">
                                <label>無期限</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2"></label>
                            <div class="col-sm-1">
                                <input type="radio" id="deadline" name="deadline" <?=(isset($data['deadline']) && $data['deadline'] =='2')?'checked':'';?> value="2">
                                <label>開課前</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="radio" id="deadline_type" name="deadline_type" <?=(isset($data['deadline_type']) && $data['deadline_type'] =='1')?'checked':'';?> value="1">
                                <input type="text" id="deadline_day_1" style="width:60px;" name="deadline_day_1" value="<?=(isset($data['deadline_day_1']))?$data['deadline_day_1']:'';?>">
                                <label>天</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="radio" id="deadline_type" name="deadline_type" <?=(isset($data['deadline_type']) && $data['deadline_type'] =='2')?'checked':'';?> value="2">
                                <label>上週星期</label>
                                <label style="width:100px;" >
	                                <select id="deadline_day_2" name="deadline_day_2" class="select2 form-control select2-single input-max">
	                                    @foreach(config('app.day_of_week') as $key => $va)
	                                        <option value="{{ $key }}" {{ old('deadline_day_2', (isset($data['deadline_day_2']))? $data['deadline_day_2'] : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
	                                    @endforeach
	                                </select>
	                            </label>
                            </div>
                            <div class="col-sm-2">
                                <input type="radio" id="deadline_type" name="deadline_type" <?=(isset($data['deadline_type']) && $data['deadline_type'] =='3')?'checked':'';?> value="3">
                                <label>上月</label>
                                <input type="text" id="deadline_day_3" style="width:60px;" name="deadline_day_3" value="<?=(isset($data['deadline_day_3']))?$data['deadline_day_3']:'';?>">
                                <label>號</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2"></label>
                            <div class="col-sm-1">
                                <input type="radio" id="deadline" name="deadline" <?=(isset($data['deadline']) && $data['deadline'] =='3')?'checked':'';?> value="3">
                                <label>結訓後</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="radio" id="deadline_type" name="deadline_type" <?=(isset($data['deadline_type']) && $data['deadline_type'] =='4')?'checked':'';?> value="4">
                                <input type="text" id="deadline_day_4" style="width:60px;" name="deadline_day_4" value="<?=(isset($data['deadline_day_4']))?$data['deadline_day_4']:'';?>">
                                <label>天</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="radio" id="deadline_type" name="deadline_type" <?=(isset($data['deadline_type']) && $data['deadline_type'] =='5')?'checked':'';?> value="5">
                                <label>下週星期</label>
                                <label style="width:100px;" >
	                                <select id="deadline_day_5" name="deadline_day_5" class="select2 form-control select2-single input-max">
	                                    @foreach(config('app.day_of_week') as $key => $va)
	                                        <option value="{{ $key }}" {{ old('deadline_day_5', (isset($data['deadline_day_5']))? $data['deadline_day_5'] : 0) == $key? 'selected' : '' }}>{{ $va }}</option>
	                                    @endforeach
	                                </select>
	                            </label>
                            </div>
                            <div class="col-sm-2">
                                <input type="radio" id="deadline_type" name="deadline_type" <?=(isset($data['deadline_type']) && $data['deadline_type'] =='6')?'checked':'';?> value="6">
                                <label>下月</label>
                                <input type="text" id="deadline_day_6" style="width:60px;" name="deadline_day_6" value="<?=(isset($data['deadline_day_6']))?$data['deadline_day_6']:'';?>">
                                <label>號</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">EMAIL提醒班務人員</label>
                            <div class="col-sm-1">
                            	<input type="radio" id="email" name="email" <?=(isset($data['email']) && $data['email'] =='Y')?'checked':'';?> value="Y">
                                <label>是</label>
                            </div>
                            <div class="col-sm-1">
                            	<input type="radio" id="email" name="email" <?=(isset($data['email']) && $data['email'] =='N')?'checked':'';?> <?=(!isset($data))?'checked':'';?> value="N">
                                <label>否</label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">凍結資料不可異動</label>
                            <div class="col-sm-1">
                            	<input type="radio" id="freeze" name="freeze" <?=(isset($data['freeze']) && $data['freeze'] =='Y')?'checked':'';?> value="Y">
                                <label>是</label>
                            </div>
                            <div class="col-sm-1">
                            	<input type="radio" id="freeze" name="freeze" <?=(isset($data['freeze']) && $data['freeze'] =='N')?'checked':'';?> <?=(!isset($data))?'checked':'';?> value="N">
                                <label>否</label>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitform();" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/class_process/detail/{{ $class_process_id }}">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                        <?php if(isset($data['id'])){?>
                        <span onclick="$('#del_form').attr('action', '/admin/class_process/job/{{ $data['id'] }}');" data-toggle="modal" data-target="#del_modol" >
                            <button type="button" class="btn btn-sm btn-danger"> 刪除</button>
                        </span>
                        <?php }?>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 系統功能 modal -->
	<div class="modal fade bd-example-modal-lg job" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-dialog_120" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">工作階段</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body" style="height: 60vh;overflow: auto;">
                    <?php for($i = 0; $i < sizeof($job); $i++) { ?>

                        <?php if(isset($job[$i+1]['lv'])) { ?>
                            <?php if($job[$i]['lv'] < $job[$i+1]['lv']) { ?>
                                <?php if($i == 0) { ?>
                                    <ul id="treeview" class="filetree">
                                <?php } ?>

                                <?php if($job[$i]['lv'] == 1) { ?>
                                        <li><span class="folder job_item"><?=$job[$i]['name'];?></span>
                                <?php } else { ?>
                                        <li><span class="folder job_item" <?php if(!empty($job[$i]['job'])){ ?>onclick="chooseJob('<?=$i?>', '<?=$job[$i]['job'];?>','<?=$job[$i]['name'];?>')" <?php } ?> ><?=$job[$i]['name'];?></span>
                                <?php } ?>
                                            <ul>
                            <?php } else if($job[$i]['lv'] == $job[$i+1]['lv']) { ?>
                                        <li><span class="file job_item" <?php if(!empty($job[$i]['job'])){ ?>onclick="chooseJob('<?=$i?>', '<?=$job[$i]['job'];?>','<?=$job[$i]['name'];?>')" <?php } ?> ><?=$job[$i]['name'];?></span></li>
                            <?php } else if($job[$i]['lv'] > $job[$i+1]['lv']) { ?>
                                                <li><span class="file job_item" <?php if(!empty($job[$i]['job'])){ ?>onclick="chooseJob('<?=$i?>', '<?=$job[$i]['job'];?>','<?=$job[$i]['name'];?>')" <?php } ?> ><?=$job[$i]['name'];?></span></li>
                                <?php for($j = 0; $j < $job[$i]['lv']-$job[$i+1]['lv']; $j++) { ?>
                                            </ul>
                                        </li>
                                <?php } ?>


                            <?php } ?>
                        <?php } else { ?>
                                        <li><span class="file job_item" <?php if($job[$i]['lv']=='3'){ ?>onclick="chooseJob('<?=$i?>', '<?=$job[$i]['job'];?>','<?=$job[$i]['name'];?>')" <?php } ?>  ><?=$job[$i]['name'];?></span></li>
                                    </ul>
                        <?php } ?>
                    <?php } ?>
                    </ul>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmJob()">確定</button>
			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
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

        $(document).ready(function () {
           function readURL(input) {
                  if (input.files && input.files[0]) {
                      $('#file').val(input.files[0].name);
                      }
                }

            $("#upload").change(function() {
              readURL(this);
            });
       });

    	let job = "";
	    let jobName = "";
	    function chooseJob(index, code,name) {
	        $('.job_item').css('background-color', '');
	        $('.job_item').eq(index).css('background-color', '#ffe4c4');
	        job = code;
	        jobName = name;
	    }

	    function confirmJob() {
	        $("#job_name").val(jobName);
	        $("#job").val(job);
	    }

    	function select_job() {
            $(".job").modal('show');
    	}

        function submitform(){
	        submitForm('#form');
	   }

       setTimeout(() => {
        $("#treeview").treeview({
            persist: "location",
            collapsed: true,
            unique: false,
            toggle: function() {
                // console.log("%s was toggled.", $(this).find(">span").text());
            }
        });
    }, 1000);

    </script>
    @endsection