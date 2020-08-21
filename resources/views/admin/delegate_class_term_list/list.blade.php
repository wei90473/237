@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'delegate_class_term_list';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">接受委訓班期訓期一覽表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">接受委訓班期訓期一覽表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>接受委訓班期訓期一覽表</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                <div class="card-body">
                                        <div class="card-header">
                                            <h4 class="card-title"></i>下列兩區條件請選其一</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <form method="post" action="/admin/delegate_class_term_list/export" id="search_form">
                                                {{ csrf_field() }}

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                <input class="col-sm-1 pt-2 float-right" type="radio" name="condition" value="yerly" checked>
                                                                <label class="col-sm-2">第一區條件</label>    
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-sm-1 text-right">年度</label>
                                                                <div class="col-sm-2">
                                                                    <select class="select2 form-control select2-single input-max" name="yerly">
                                                                        @for ($i = (int)date("Y")-1910 ; $i>=90; $i--)
                                                                        <option value={{$i}}>{{$i}}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                               
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-body">

                                                            <div class="form-group row">
                                                                <input class="col-sm-1 pt-2 float-right" type="radio" name="condition" value="sedate">
                                                                <label class="col-sm-2">第二區條件</label>    
                                                            </div>

                                                            <!--  variable declare  --> 
                                                            <div class="form-group row align-items-center">
                                                                <!--  datepicker -->
                                                                <label class="col-2">起始日期<span class="text-danger"></span></label>
                                                                <input type="text" class="form-control col-2 mr=1" value="" id="sdate" name="sdate"  id="sdatetw" min="1" readonly autocomplete="off">
                                                                <label class="col-2">結束日期<span class="text-danger"></span></label>
                                                                <input type="text" class="form-control col-2 " value="" id="edate" name="edate"  id="edatetw" min="1" readonly autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>

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
    
        $('#sdate').datepicker({
            format: "twymmdd",
        });
        $('#edate').datepicker({
            format: "twymmdd",
        });
    } );

</script>
@endsection