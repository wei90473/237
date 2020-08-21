@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'teaching_material_statistics';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">教材交印統計表</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">教材交印統計表</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form method="post" action="/admin/teaching_material_statistics/export" id="search_form">
                        {{ csrf_field() }}

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="form-group row">
                                            <label class="col-2">日期(YYYMM)<span class="text-danger">*</span></label>
                                            
                                                <input type="text" class="form-control col-sm-1" id="startYear" name="startYear" min="1" value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" required>
                                                <p style="display: inline">年</p>
                                                <input type="text" class="form-control col-sm-1" id="startMonth" name="startMonth" min="1" 
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="2" required onchange="timeChange()">
                                                <p style="display: inline">月</p>
                                             
                                        </div>

                                    </div>
                                </div>
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
</script>
@endsection