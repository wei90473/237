@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'poll';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">網路民調維護表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/poll" class="text-info">網路民調維護列表</a></li>
                        <li class="active">網路民調維護表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/poll/'.$data->serno, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/poll/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">網路民調維護表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 民調主題 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">民調主題<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="subject" name="subject" placeholder="請輸入民調主題" value="{{ old('subject', (isset($data->subject))? $data->subject : '') }}" autocomplete="off" maxlength="145" required>
                            </div>
                        </div>

                        <!-- 起始日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">起始日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="sdate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->sdate))? mb_substr($data->sdate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="sdate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->sdate))? mb_substr($data->sdate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="sdate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->sdate))? mb_substr($data->sdate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- 結束日期 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">結束日期<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <div class="input-group roc-date input-max">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">民國</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-year" maxlength="3" name="edate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($data->edate))? mb_substr($data->edate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">年</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-month" maxlength="2" name="edate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($data->edate))? mb_substr($data->edate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">月</span>
                                    </div>

                                    <input type="text" class="form-control roc-date-day" maxlength="2" name="edate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($data->edate))? mb_substr($data->edate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                    <div class="input-group-prepend">
                                        <span class="input-group-text">日</span>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <!-- 選項 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">選項<span class="text-danger">*</span></label>
                            <div class="col-sm-10">

                                <button type="button" onclick="add();" class="btn btn-sm btn-info mb-2">增加選項</button>

                                <div id="content_div">

                                    @if(isset($answersList))
                                        @foreach($answersList as $va)

                                            <div class="input-group group input-max my-2 data_row">

                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">內容</span>
                                                </div>

                                                <input type="text" class="form-control answers_input" autocomplete="off" name="answers[]"  value="{{ $va->answers }}" required>
                                                <input type="hidden" name="act[]" value="update" class="act_input">
                                                <input type="hidden" name="id[]" value="{{ $va->id }}">

                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">得票數：<span>{{ $va->checknum }}</span></span>
                                                </div>

                                                <div class="input-group-prepend">
                                                    <span class="input-group-text pointer" onclick="delEm=this" data-toggle="modal" data-target="#del_modol"><i class="fa fa-trash text-danger"></i></span>
                                                </div>

                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        </div>



                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/poll">
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

    <div id="model" style="display:none">
        <div class="input-group group input-max my-2 data_row">

            <div class="input-group-prepend">
                <span class="input-group-text">內容</span>
            </div>

            <input type="text" class="form-control answers_input" autocomplete="off" name="answers[]" required>
            <input type="hidden" name="act[]" value="create" class="act_input">
            <input type="hidden" name="id[]" value="0">

            <div class="input-group-prepend">
                <span class="input-group-text">得票數：<span>0</span></span>
            </div>

            <div class="input-group-prepend">
                <span class="input-group-text pointer" onclick="delEm=this" data-toggle="modal" data-target="#del_modol"><i class="fa fa-trash text-danger"></i></span>
            </div>

        </div>
    </div>



    <!-- 刪除確認視窗 -->
    <div id="del_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">警告</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">你確定要刪除嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                        <button type="button" class="btn mr-3 btn-danger" onclick="del();">確定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        function add() {
            html = $('#model').html();

            $('#content_div').append(html);
        }

        // 刪除
        var delEm;

        function del() {
            dataRow = $(delEm).parents('.data_row');
            // act 改為del
            $(dataRow).find('.act_input').val('delete');
            // 隱藏
            $(dataRow).hide();
            // 非必填
            $(dataRow).find('.answers_input').attr('required', false);
            // 關閉視窗
            $('#del_modol').modal('hide')
        }
    </script>

@endsection