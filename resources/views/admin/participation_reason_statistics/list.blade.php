@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'participation_reason_statistics';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">參訓原因統計</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">參訓原因統計</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <form method="post" action="/admin/participation_reason_statistics/export" id="search_form">
                        {{ csrf_field() }}
                        <div class="card">
                            <div class="card-header">
                            
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <label class="col-sm-2 control-label text-md-right pt-2">年度<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group bootstrap-touchspin number_box">
                                                    <!-- 輸入欄位 -->
                                                    <input type="text" class="form-control input-max" id="yerly" name="yerly" min="1" placeholder="請輸入年度" required
                                                    value="" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="3" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>下列選項請點選其一</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- 全年 -->
                                        <div class="form-group row">
                                            <input class="col-sm-1 pt-2 float-right" type="radio" id="allYear" name="radioType" value="1" checked>
                                            <label class="col-sm-2">全年<span class="text-danger"></span></label>    
                                        </div>
                                        
                                        <!-- 季 -->
                                        <div class="form-group row">
                                            <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="quarter" name="radioType" value="2" >
                                            <label class="col-sm-1">季<span class="text-danger"></span></label>
                                            <div class="col-sm-2">
                                                <select class="select2 form-control select2-single input-max" name="selectquarter">
                                                    @for ($i = 1; $i<=4; $i++)
                                                        <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div> 
                                        <!-- 月份 -->
                                        <div class="form-group row">
                                            <input class="col-sm-1 control-label text-md-right pt-2" type="radio" id="month" name="radioType" value="3" >
                                            <label class="col-sm-1">月份<span class="text-danger"></span></label>
                                            <div class="col-sm-2">
                                                <select class="select2 form-control select2-single input-max" name="selectMonth">
                                                    @for ($i = 1; $i<=12; $i++)
                                                        <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>            
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></i>其他條件(Optional)</h4>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">                                        
                                        <div class="form-group row">
                                            <label class="col-sm-2 text-right">班別性質<span class="text-danger"></span></label>
                                            <div class="col-sm-10">
                                                <select class="select2 form-control select2-single input-max" name="classes">
                                                    <option value="0">請選擇班別性質</option>
                                                    @foreach ($class as $class)
                                                        <option value="{{$class->value}}">{{$class->text}}</option>
                                                    @endforeach
                                                </select>
                                            </div>   
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                            <div id="dvdoctype" class="form-group row  align-items-center">
                                <label class="col-2 text-right">請選檔案格式：</label>
                                <label class="mr-3"><input type="radio" id="doctype1" name="doctype" value="1" checked>MS-DOC</label>
                                <label><input type="radio" id="doctype2" name="doctype" value="2" >ODF</label>    
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