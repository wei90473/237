@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'certification';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">認證上傳設定</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">認證上傳設定列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>認證上傳設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form certification="get" id="search_form">

                                            <!-- 班別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別</span>
                                                    </div>
                                                    <select class="form-control select2" id="class" name="class" onchange="classChange();">

                                                        @foreach($classList as $key => $va)
                                                            <option value="{{ $va->class }}" {{ $queryData['class'] == $va->class? 'selected' : '' }}>{{ $va->class }}{{ $va->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- 期別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <select class="form-control select2" id="term" name="term">
                                                        <option value="">請選擇</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    {!! Form::open([ 'method'=>'put', 'url'=>'/admin/certification', 'id'=>'form']) !!}

                                        <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                        <input type="hidden" name="term" value="{{ $queryData['term'] }}">



                                        @if($data)
                                            <div class="table-responsive">

                                                <hr class="bg-secondary">

                                                <!-- 上傳 -->
                                                <div class="form-group row">
                                                    <label class="col-md-2 col-form-label text-md-right">上傳</label>
                                                    <div class="col-md-10">
                                                        <select id="upload2" name="upload2" class="select2 form-control select2-single input-max" onchange="upload2Change();">
                                                            <option value="Y" {{ $data->upload2 == 'Y'? 'selected' : '' }}>是</option>
                                                            <option value="N" {{ $data->upload2 == 'N'? 'selected' : '' }}>否</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- 成績資料 -->
                                                <div class="form-group row">
                                                    <label class="col-md-2 col-form-label text-md-right"></label>
                                                    <div class="col-md-10">
                                                        <input type="checkbox" class="mx-2" id="grade" name="grade" value="Y" {{ $data->grade == 'Y'? 'checked' : '' }}><span>成績資料</span>
                                                    </div>
                                                </div>

                                                <!-- 請假資料 -->
                                                <div class="form-group row">
                                                    <label class="col-md-2 col-form-label text-md-right"></label>
                                                    <div class="col-md-10">
                                                        <input type="checkbox" class="mx-2" id="leave" name="leave" value="Y" {{ $data->leave == 'Y'? 'checked' : '' }}><span>請假資料</span>
                                                    </div>
                                                </div>

                                                <!-- 轉出狀況 -->
                                                <div class="form-group row">
                                                    <label class="col-md-2 col-form-label text-md-right">轉出狀況</label>
                                                    <div class="col-md-10">
                                                        <label class="col-form-label">{{ ($data->file5)? '已轉出' : '未轉出' }}</label>
                                                    </div>
                                                </div>



                                            </div>
                                        @endif
                                    {!! Form::close() !!}

                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            @if($data)
                                <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                            @endif
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection


@section('js')
    <script>
        // 取得期別
        function classChange()
        {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/certification/getterm',
                data: { classes: $('#class').val(), selected: '{{ $queryData['term'] }}'},
                success: function(data){
                    $('#term').html(data);
                    $("#term").trigger("change");
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
        }

        // 初始化
        classChange();
    </script>

    <script>
        function upload2Change() {
            if ($('#upload2').val() == 'Y') {


                $('#grade').attr('disabled', false);
                $('#leave').attr('disabled', false);

            } else {
                $('#grade').attr('disabled', true);
                $('#leave').attr('disabled', true);

                $('#grade').prop('checked', false);
                $('#leave').prop('checked', false);

            }
        }

        @if($data)
            // 有資料時初始化
            upload2Change();
        @endif
    </script>

@endsection