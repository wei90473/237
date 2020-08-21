@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student';?>
<style>
    .min_width_0{
        min-width: 0px;
    }

    .customBackground > td{
        background-color: red;
    }

    .panel-heading .accordion-toggle {
        display: inline-block;
        text-align: center;
        color: rgb(255, 255, 255);
        font-size: 16px;
        box-sizing: border-box;
        line-height: 1.8em;
        vertical-align: middle;
        font-family: 微軟正黑體, "Microsoft JhengHei", Arial, Helvetica, sans-serif !important;
        background: rgb(250, 160, 90);
        padding: 5px 20px;
        border-width: initial;
        border-style: none;
        border-color: initial;
        border-image: initial;
        margin: 2px 0px;
        border-radius: 6px;
        margin-bottom: 10px;
    }
    .panel-heading .accordion-toggle:hover {
        background-color: #f79448 !important;
        border: 1px solid #f79448 !important;
            -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
        opacity: 1;
    }
    .panel-heading .accordion-toggle::before {
        background-color: inherit; !important;
    }
    .panel-heading .accordion-toggle.collapsed::before {
        background-color: inherit; !important;
    }
    .search-float input,
    .search-float .select2-selection--single, .search-float select {
        min-width: initial;
    }

</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員基本資料登錄</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員基本資料登錄</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員基本資料登錄</h3>
                    </div>



                    <div class="card-body">
                        <div class="search-float" >
                            <form onsubmit="return checkQueryFill()">
                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                 <div class="form-group col-md-3">
                                    <div class="pull-left input-group-prepend">
                                    <div class="input-group-prepend">
                                                <label class="input-group-text">身分證號：</label>
                                    </div>
                                    <input type="text" class="form-control" name="idno" value="{{ $queryData['idno'] }}">
                                    </div>
                                </div>
                                    <div class="form-group col-md-3">
                                        <div class="pull-left input-group-prepend">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">姓名：</label>
                                            </div>
                                            <input type="text" class="form-control" name="cname" value="{{ $queryData['cname'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="pull-left input-group-prepend">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">人員身分：</label>
                                            </div>
                                            <select class="custom-select" name="identity">
                                                <option value="">不限</option>
                                                <option value="1" {{ ($queryData['identity'] == 1) ? 'selected' : '' }} >公務人員</option>
                                                <option value="2" {{ ($queryData['identity'] == 2) ? 'selected' : '' }} >一般民眾</option>
                                            </select>
                                        </div>
                                    </div>                                
                                </div>
                                </div>
                                <!-- 進階/簡易搜尋開始 -->
<div class="panel-group" id="accordion">
                                            <header class="panel-heading">

                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#search"> </a>

                                                </header>
                                                <footer id="search" class="panel-collapse collapse">
<!-- 進階/簡易搜尋開始 -->

                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">機關代碼</label>
                                            </div>
                                            <input type="text" class="form-control" name="enrollid" value="{{ $queryData['enrollid'] }}">
                                        </div>
                                    </div>                               
                          
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">官職等：</label>
                                            </div>

                                            <select class="custom-select" name="rank">
                                                <option value="">不限</option>
                                                @foreach ($m02tb_fields['rank'] as $value => $text)
                                                    <option value="{{ $value }}" {{ ($value == $queryData['rank']) ? 'selected' : '' }}>{{ $text }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">職稱：</label>
                                            </div>
                                            <input type="text" name="position" class="form-control" value="{{ $queryData['position'] }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">E-mail：</label>
                                            </div>
                                            <input type="text" name="email" class="form-control" value="{{ $queryData['email'] }}" >
                                        </div>
                                    </div>
                                </div>
<!-- 進階/簡易搜尋結束 -->
</footer>
                                            </div>
<!-- 進階/簡易搜尋結束 -->
                                <div class="form-group">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">人員註記：</label>
                                        <label class="form-check-label">
                                            <input type="checkbox" name="chief" class="form-check-input" style="min-width:0px" value="Y" {{ ($queryData['chief'] == "Y")? 'checked' : '' }} >主管
                                        </label>
                                    </div>

                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="personnel" class="form-check-input" style="min-width:0px"  value="Y" {{ ($queryData['personnel'] == "Y")? 'checked' : '' }}>人事人員
                                        </label>
                                    </div>   

                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="aborigine" class="form-check-input" style="min-width:0px"  value="Y" {{ ($queryData['aborigine'] == "Y")? 'checked' : '' }}>原住民
                                        </label>
                                    </div>                                                                  
                                </div>

                                <div class="form-group">
                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="handicap" class="form-check-input" style="min-width:0px"  value="Y" {{ ($queryData['handicap'] == "Y")? 'checked' : '' }}>身心障礙
                                        </label>
                                    </div>  

                                    <div class="form-check-inline">
                                        <label class="form-check-label">
                                            <input type="checkbox" name="special_situation" class="form-check-input" style="min-width:0px"  value="Y"  {{ ($queryData['special_situation'] == "Y")? 'checked' : '' }}>有特殊狀況註記
                                        </label>
                                    </div>                                      
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            </form>
                        </div>
                        <div class="table-responsive" style="padding-top: 10px;">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <th>功能</th>
                                    <th>人員身分</th>
                                    <th>姓名</th>
                                    <th>性別</th>
                                    <th>服務機關</th>
                                    <th>職稱</th>
                                    <th>官職等</th>
                                    <th>E-Mail</th>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr {{ ($student->handicap == 'Y' || trim($student->special_situation) !== "" ) ? "class=customBackground" : ''  }}>
                                        <td>
                                            <a href="/admin/student/edit/{{ $student->des_idno }}">
                                                <button class="btn btn-primary">編輯</button>                                        
                                            </a>
                                        </td>
                                        <td>
                                            @if (isset($m02tb_fields['identity'][$student->identity]))
                                                {{ $m02tb_fields['identity'][$student->identity] }}
                                            @else
                                                {{ $student->identity }}
                                            @endif 
                                        </td>
                                        <td>{{ $student->cname }}</td>
                                        <td>
                                            @if (isset($m02tb_fields['sex'][$student->sex]))
                                                {{ $m02tb_fields['sex'][$student->sex] }}
                                            @else
                                                {{ $student->sex }}
                                            @endif                                         
                                        </td>
                                        <td>
                                            @if (isset($student->m17tb))
                                            {{ $student->m17tb->enrollname }}
                                            @endif 
                                        </td>
                                        <td>{{ $student->position }}</td>
                                        <td>
                                        @if (isset($m02tb_fields['rank'][$student->rank]))
                                        {{ $m02tb_fields['rank'][$student->rank] }}
                                        @endif 
                                        </td>
                                        <td>{{ $student->email }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 
                        @if (!empty($students))
                            @include('admin/layouts/list/pagination', ['paginator' => $students, 'queryData' => $queryData])                   
                        @endif 
                    </div>
                    <div class="card-footer">
                        <a href="/admin/review_apply/class_list">
                        <button type="button" class="btn btn-sm btn-danger" onclick="location.href=document.referrer"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('js')
<script>
    // 檢查有無至少填寫
    function checkQueryFill()
    {
        let checkFiled = ['idno', 'cname', 'organ', 'position', 'email'];
        for(var i=0; i<checkFiled.length; i++){
            if ($('input[name=' + checkFiled[i] + ']').val() !== ""){
                return true
            }
        }

        alert('身分證號、姓名、機關代碼、職稱、email至少擇一填寫。');
        return false;
    }
</script>
@endsection