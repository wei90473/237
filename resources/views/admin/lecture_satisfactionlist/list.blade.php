@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'lecture_satisfactionlist';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座滿意度一覽表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座滿意度一覽表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form method="post" action="/admin/lecture_satisfactionlist/export" id="search_form">
                        {{ csrf_field() }}

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>輸入列印條件</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">講座姓名<span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <select class="select2 form-control select2-single input-max" name="lecturer" required >
                                                <option value="">請輸入講座姓名</option>
                                                @foreach ($classArr as $classArr)
                                                    <option value="{{$classArr->idno}}">{{$classArr->cname}}</option>
                                                @endforeach                                                
                                                </select>
                                            </div> 
                                        </div>                                    
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label text-md-right pt-2">期間</label>
                                            
                                                <input type="text" class="form-control col-sm-1" id="startYear" name="startYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                                <p style="display: inline">年</p>
                                                <input type="text" class="form-control col-sm-1" id="startMonth" name="startMonth" min="1" 
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2" onchange="timeChange()">
                                                <p style="display: inline">月</p>
                                            
                                            <div class="w-100"></div>
                                            <label class="col-sm-2 control-label text-md-right pt-2">~<span class="text-danger"></span></label>
                                            
                                                <input type="text" class="form-control col-sm-1" id="endYear" name="endYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3">
                                                <p style="display: inline">年</p>
                                                <input type="text" class="form-control col-sm-1" id="endMonth" name="endMonth" min="1" 
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2">
                                                <p style="display: inline">月</p>
                                            
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">

                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-3">
                                        <input type="checkbox" name="checkFlag" id="checkFlag" value="Y">含無滿意度調查課程
                                    </div>
                                </div>
                            </div>
                            <div id="dvdoctype" class="form-group row  align-items-center">
                                <label class="col-2 text-right">請選檔案格式：</label>
                                <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
                            </div>
                        </div>
                        <div align="center">
                            <button type="submit" class="btn mobile-100 mb-3 mr-1"><i class="fas fa-file-export fa-lg pr-1"></i>匯出</button>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>
    function timeChange(){
        $('#endYear').val($('#startYear').val());
        $('#endMonth').val($('#startMonth').val());
    }
</script>
@endsection