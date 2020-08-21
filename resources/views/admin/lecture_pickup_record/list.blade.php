@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_pickup_record';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">接送講座紀錄結算表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">接送講座紀錄結算表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>接送講座紀錄結算表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <form method="post" action="/admin/lecture_pickup_record/export" id="search_form">
                                        {{ csrf_field() }}
                                                <!--  variable declare  -->
                                                <?php $sdatetw =''; $edatetw =''; ?>
                                                <div class="form-group row align-items-center">
                                                    <!--  datepicker -->
                                                    <label class="col-1">起始日期<span class="text-danger"></span></label>
                                                    <input type="text" class="form-control col-2 mr=1" value="{{$sdatetw}}" id="sdatetw" name="sdatetw"  id="sdatetw" min="1" readonly autocomplete="off">
                                                    <label class="col-1">結束日期<span class="text-danger"></span></label>
                                                    <input type="text" class="form-control col-2 " value="{{$edatetw}}" id="edatetw" name="edatetw"  id="edatetw" min="1" readonly autocomplete="off">
                                                </div>

                                                <!--  variable declare  -->
                                                <div class="form-group row align-items-center">
                                                    <!--  datepicker -->
                                                    <label class="col-1">搜尋班期<span class="text-danger"></span></label>
                                                    <select id="class" name="class" class="form-control select2-single input-max" {{ (isset($data->idno))? 'disabled' : '' }} >
                                                        @if(isset($class_data))
                                                            <option value="{{ $class_data->idno }}"></option>
                                                        @endif
                                                    </select>

                                                </div>

                                                <label class="radio-inline col-2"><input type="radio" name="type" id="type" value="1" checked >依照日期匯出</label>
                                                <label class="radio-inline col-2"><input type="radio" name="type" id="type" value="2">依照班期匯出</label>

                                                <div class="form-group row col-6 align-items-center justify-content-center">
                                                    <button type="submit" class="btn mobile-100" ><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                                                    <label id="download"></label>
                                                </div>


                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')

<!--  src of datepicker  -->
<script src="/backend/assets/js/bootstrap-datepicker.js"></script>

<script>

//  call datepicker
    $( function() {

        $('#sdatetw').datepicker({
            format: "twy-mm-dd",
        });
        $('#edatetw').datepicker({
            format: "twy-mm-dd",
        });
    } );

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    $(function (){
        $("#class").select2({
            language: 'zh-TW',
            width: '100%',
            // 最多字元限制
            maximumInputLength: 5,
            // 最少字元才觸發尋找, 0 不指定
            minimumInputLength: 1,
            // 當找不到可以使用輸入的文字
            // tags: true,
            placeholder: '',
            // AJAX 相關操作
            ajax: {
                url: '/admin/lecture_pickup_record/getClass',
                type: 'get',
                // 要送出的資料
                data: function (params){
                    // 在伺服器會得到一個 POST 'search'
                    return {
                        search: params.search
                    };
                },
                processResults: function (data){
                    console.log(data)

                    // 一定要返回 results 物件
                    return {
                        results: data,
                        // 可以啟用無線捲軸做分頁
                        pagination: {
                            more: true
                        }
                    }
                }
            }
        });
    })

</script>
@endsection