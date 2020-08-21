@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'class_material';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班別教材資料處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">班別教材資料處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>班別教材資料處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

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

                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
                                    </div>

                                    {!! Form::open([ 'method'=>'put', 'url'=>'/admin/class_material', 'id'=>'form']) !!}

                                        <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                        <input type="hidden" name="term" value="{{ $queryData['term'] }}">

                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>上課日期</th>
                                                    <th>課程名稱</th>
                                                    <th>講座姓名</th>
                                                    <th>教材名稱</th>
                                                    <th class="text-cetner" width="70">增加教材</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @foreach($data as $va)

                                                    <tr data-course="{{ $va->course }}">
                                                        <td>{{ $base->showDate($va->date) }}</td>
                                                        <td>{{ $va->course_name }}</td>
                                                        <td>{{ $va->teacher }}</td>
                                                        <td class="course_material_list">
                                                            @foreach($selectMaterial as $vb)
                                                                {{-- 屬於他的課程才是選擇的教材 --}}
                                                                @if($va->course == $vb->course)
                                                                    <div class="mt-1 p-2 bg-info text-white wft">
                                                                        <i class="fa fa-trash text-danger mr-2 pointer" onclick="$(this).parents('.wft').remove()"></i>
                                                                        <span>{{ $vb->handout }}</span>
                                                                        <input type="hidden" name="material[{{ $vb->course }}][]" value="{{ $vb->idno }}__{{ $vb->handoutno }}" id="material_{{ $vb->course }}_{{ $vb->idno }}_{{ $vb->handoutno }}">
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td class="text-center">
                                                            <i onclick="Aims=$(this).parents('tr')" class="fa fa-plus text-dangerpointer pointer" data-toggle="modal" data-target="#add_modol"></i>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

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

    <!-- 增加教材視窗 -->
    <div id="add_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">加入教材</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- 講座 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">講座<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select onchange="if($(this).val() != ''){$('#s2').val('');}" id="s1" name="s1" class="select2 form-control select2-single input-max">
                                    <option value="">請選擇</option>
                                    @foreach($teacher as $idno => $va)
                                        <option value="{{ $idno }}">{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- 名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">名稱<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input onchange="if($(this).val() != ''){$('#s1').val('');$('#s1').trigger('change')}" type="text" class="form-control input-max" id="s2" name="s2" autocomplete="off" required maxlength="255">
                            </div>
                        </div>

                        <div class="form-group row text-center">
                            <div class="col-sm-4 offset-sm-4">
                                <button onclick="getCourse()" type="button" class="btn w-100 mb-3 mb-md-0 bg-info"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>選項</th>
                                        <th>教材名稱</th>
                                        <th>版本日期</th>
                                    </tr>
                                </thead>
                                <tbody id="ajax_content">

                                </tbody>
                            </table>
                        </div>

                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn mr-3 btn-danger" onclick="addCheck()">確定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                url: '/admin/class_material/getterm',
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
        var Aims;

        // ajax取得教材
        function getCourse() {
            $.ajax({
                type: 'post',
                dataType: 'html',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url:"/admin/course_material/get_course_material",
                data: { s1: $('#s1').val(), s2: $('#s2').val()},
                success: function(data){
                    $('#ajax_content').html(data)
                },
                error: function() {
                    console.log('Ajax Error');
                }
            });
        }

        // 新增教材檢查
        function addCheck() {
            var handoutno = $('input[name=selected_material]:checked').data('handoutno');
            var handout = $('input[name=selected_material]:checked').data('handout');
            var idno = $('input[name=selected_material]:checked').data('idno');
            var course = $(Aims).data('course');
            var all_name = 'material_' + course + '_' + idno + '_' + handoutno;

            if (typeof(handoutno) == "undefined") {
                $('#add_modol').modal('hide');
                alert("請選擇要增加的教材");
                return;
            }

            if ($('#'+all_name).length) {
                $('#add_modol').modal('hide');
                alert('教材重複');
                return;
            }


            var html = '<div class="mt-1 p-2 bg-info text-white wft">' +
                '<i class="fa fa-trash text-danger mr-2 pointer" onclick="$(this).parents(\'.wft\').remove()"></i>' +
                '<span>'+handout+'</span>' +
                '<input type="hidden" name="material['+course+'][]" value="'+idno+'__'+handoutno+'" id="material_'+course+'_'+idno+'_'+handoutno+'">' +
                '</div>';

            $(Aims).find('.course_material_list').append(html);

            $('#add_modol').modal('hide');
        }
    </script>

@endsection