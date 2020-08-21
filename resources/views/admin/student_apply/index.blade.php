@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'student_apply';?>
<style>
    .customBackground > td{
        background-color: red;
    }
    .form-check input{
        min-width: 10px;
    }
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">學員報名處理</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">學員報名處理</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>學員報名處理</h3>
                    </div>

                    <div class="card-body">
                        <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px;">
                            訓練班別：{{ $t04tb->t01tb->name }}<br>
                            期別：{{ $t04tb->term }}<br>
                            分班名稱：<br>
                            班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                            委訓機關：{{ $t04tb->client }}<br>
                            起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                            班務人員：
                        </div>
                        <div>
                        <form>     
                            <div class="search-float">
                                <div class="form-row">
                                    <div class='form-group col-3'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">姓名</label>
                                            </div>
                                            <input class="form-control" type="text" name="name" value="{{ $queryData['name'] }}">
                                        </div>    
                                    </div>

                                    <div class='form-group col-3'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">服務機關</label>
                                            </div>
                                            <input class="form-control" type="text" name="dept" value="{{ $queryData['dept'] }}">
                                        </div>
                                    </div>

                                    <div class='form-group col-3'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <label class="input-group-text">E-mail</label>
                                            </div>
                                            <input class="form-control" type="text" name="email" value="{{ $queryData['email'] }}">
                                        </div> 
                                    </div>

                                    <div class='form-group col-3'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <label class="input-group-text">主管機關</label>
                                            </div>
                                            <input class="form-control" type="text" name="organ_name" value="{{ $queryData['organ_name'] }}">
                                        </div>                                    
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class='form-group col-4'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text">人員身份</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="identity" value="1" style="min-width:0px;" checked>
                                                <label class="form-check-label" for="exampleRadios1" >
                                                    公務人員
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="identity" value="2" style="min-width:0px;"
                                                @if($queryData['identity'] == "2")
                                                    checked
                                                @endif 
                                                >
                                                <label class="form-check-label" for="exampleRadios1">
                                                    一般民眾 
                                                </label>
                                            </div>                                                                                
                                        </div>
                                    </div>
                                    <div class='form-group col-3'>
                                        <div class="input-group">
                                            <!-- <div class="input-group-prepend"> -->
                                            <label class="input-group-text">選項</label>
                                            <!-- </div> -->
                                            <div class="form-check  form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" value="" style="min-width:0px;" checked>
                                                <label class="form-check-label" for="exampleRadios1">
                                                    全部
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" value="2" style="min-width:0px;"
                                                @if($queryData['status'] == "2")
                                                    checked
                                                @endif 
                                                >
                                                <label class="form-check-label" for="exampleRadios1">
                                                    未報到
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status" value="3" style="min-width:0px;"
                                                @if($queryData['status'] == "3")
                                                    checked
                                                @endif                                             
                                                >
                                                <label class="form-check-label" for="exampleRadios1">
                                                    退訓
                                                </label>
                                            </div>                                                                                
                                        </div> 
                                    </div>                                    
                                </div>

                                <div class="float-md mobile-100 row mr-1 mb-3p">
                                    <div class="input-group col-12">
                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                               
                                        <a href="/admin/student_apply/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                            <button type="button" class="btn btn-primary">重設條件</button>
                                        </a>
                                    </div>
                                </div>
                                
                            </div>                                
                        </form>                            
                        </div>  
                                              
                        <div style="padding:10px;padding-left:0px;">
                            <div style="padding:10px;padding-left:0px;">
                                <button type="button" onclick="importStudent()" class="btn btn-primary">匯入名冊</button>
                            </div>
                            <button class="btn btn-primary" onclick="insertStudent()">新增學員</button>
                            <a href='/admin/student_apply/modifylogForAdmin/{{ $t04tb->class }}/{{ $t04tb->term }}'>
                                <button class="btn btn-primary">異動紀錄</button>
                             </a>    
                            <a href='/admin/student_apply/arrange_group/{{ $t04tb->class }}/{{ $t04tb->term }}'>
                                <button class="btn btn-primary">編組別</button>
                            </a>    
                            
                            <a href='/admin/student_apply/arrange_stno/{{ $t04tb->class }}/{{ $t04tb->term }}'>
                                <button class="btn btn-primary">序學號</button>
                            </a>  
                            <button class="btn btn-primary" onclick="askPublish()">公告學員名冊</button>   
                        </div>  

                        <input type="hidden" name="prove" value="S">
                        <div class="table-responsive" style="max-height:800px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">功能</th>
                                        <th class="text-center" width="100">學號</th>
                                        <th class="text-center" width="100">人員身份</th>
                                        <th width="100">姓名</th>
                                        <th>主管機關</th>
                                        <th>服務機關</th>
                                        <th>電話(公)</th>
                                        <th>電話(宅)</th>
                                        <th>行動電話</th>
                                        <th>Email</th>
                                        <th>學歷</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($t13tbs))
                                        @foreach($t13tbs as $t13tb)
                                        <tr {!! (isset($t13tb->m02tb) && empty($t13tb->m02tb->special_situation) && $t13tb->m02tb->handicap !== 'Y') ? '' : "class=customBackground" !!}>
                                            <td class="text-center"><a href="/admin/student_apply/edit/{{ $t13tb->class }}/{{ $t13tb->term }}/{{ $t13tb->des_idno }}"><button class="btn btn-primary">編輯</button></a></td>
                                            <td class="text-center">{{ $t13tb->no }}</td>
                                            <td class="text-center">
                                            @if (!empty($t13tb->m02tb) && !empty(config('database_fields.t13tb')['identity'][$t13tb->m02tb->identity]))
                                            {{ config('database_fields.t13tb')['identity'][$t13tb->m02tb->identity] }}
                                            @else
                                            身份異常
                                            @endif 
                                            </td>
                                            <td>
                                            @if (isset($t13tb->m02tb))
                                            {{ $t13tb->m02tb->cname }}
                                            @endif 
                                            </td>
                                            <td>
                                                @if(!empty($t13tb->m13tb))
                                                    {{ $t13tb->m13tb->lname }}
                                                @endif
                                            </td>
                                            <td>{{ $t13tb->dept }}</td>
                                            @if (isset($t13tb->m02tb))
                                            <td>{{ "(".$t13tb->m02tb->offtela1.") ".$t13tb->m02tb->offtelb1."(分機)".$t13tb->m02tb->offtelc1 }}</td>
                                            <td>{{ "(".$t13tb->m02tb->homtela.") ".$t13tb->m02tb->homtelb }}</td>
                                            <td>{{ $t13tb->m02tb->mobiltel }}</td>
                                            <td>{{ $t13tb->m02tb->email }}</td>
                                            <td>{{ $t13tb->m02tb->education }}</td>
                                            @else
                                            <td></td><td></td><td></td><td></td><td></td>
                                            @endif 
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div> 

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">

                        <a href="/admin/student_apply/class_list">
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>                     
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 公告學員名冊 -->
<div id="publish" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">公告學員名冊</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'put' , 'url' => "admin/student_apply/publishStudentList/{$t04tb->class}/{$t04tb->term}"]) !!}
                <div class="search-float">
                    <div class="row" stlye="margin-bottom:20px;">
                        班號：{{ $t04tb->class }}<br>
                        班別名稱：{{ $t04tb->t01tb->name }}<br>
                        學員名冊：{!! ($t04tb->publish1 == 'Y') ? '已公告' : '尚未公告' !!}
                    </div>
                    <div class="row">
                        <div class="col-6 text-center">   
                            @if($t04tb->publish1 == 'Y')  
                                <button type="submit" name="publish" value="N" class="btn btn-danger mobile-100 mb-3 mb-md-0">不公告</button>  
                            @else
                                <button type="submit" name="publish" value="Y" class="btn btn-primary mobile-100 mb-3 mb-md-0">公告</button>   
                            @endif                                                                                                                                                                                                                 
                        </div>
                                             
                        <div class="col-6 text-center">     
                            <button type="button" data-dismiss="modal" class="btn mobile-100 mb-3 mb-md-0">取消</button>                                                                                                                                                                                                                   
                        </div>                        
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>           
        </div>
    </div>
</div>
<!-- 公告學員名冊 -->

<!-- 新增學員 -->
<div id="insert_student" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">新增學員</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'post' , 'url' => "admin/student_apply/redirectCreateStudent/{$t04tb->class}/{$t04tb->term}"]) !!}
                <div class="search-float">
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">人員身份</label>
                        </div>
                        <select name="identity" class="form-control custom-select">
                            <option value="1">公務人員</option>
                            <option value="2">一般民眾</option>
                        </select>                                                
                    </div>
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">學員身分證</label>
                        </div>     
                        <input tpye="text" name="idno" class="form-control">                                         
                    </div>

                    <div class="row">
                        <div class="col-6 text-center">   
                            <button type="submit" name="publish" value="Y" class="btn btn-primary mobile-100 mb-3 mb-md-0">新增</button>                                                                                                                                                                                                                  
                        </div>
                                             
                        <div class="col-6 text-center">     
                            <button type="button" data-dismiss="modal" class="btn mobile-100 mb-3 mb-md-0 btn-danger">取消</button>                                                                                                                                                                                                                   
                        </div>                        
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>           
        </div>
    </div>
</div>
<!-- 新增學員 -->

<!-- 名冊匯入 -->
<div id="import_student" class="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">名冊匯入</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['method' => 'post' , 'url' => "admin/student_apply/importStudent/{$t04tb->class}/{$t04tb->term}", "enctype" => "multipart/form-data", "onsubmit" => "return checkImport()"]) !!}
                <!-- <input type="hidden" name="cover_insert" value=""> -->
                <div class="search-float">
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">人員身份</label>
                        </div>
                        <select name="import_identity" class="form-control custom-select" onchange="changeVersion(this.value)">
                            <option value="1">公務人員</option>
                            <option value="2">一般民眾</option>
                        </select>                                                
                    </div>
                    <div id="version" class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">匯入版本</label>
                        </div>
                        <select name="import_version" class="form-control custom-select">
                            <option value="easy">簡易版</option>
                            <option value="full">完整版</option>
                        </select>                                                
                    </div>                    
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">匯入檔案</label>
                        </div>     
                        <input type="file" name="import_file" class="form-control" style="width:300px;" accept=".xls, .xlsx" >                                       
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">   
                            <button type="button" onclick="downloadExample()" class="btn btn-primary">下載匯入範本</button>
                            <button type="submit" name="publish" value="Y" class="btn btn-primary mobile-100 mb-3 mb-md-0">匯入</button>                                                                                                                                                                                                                  
                            <button type="button" data-dismiss="modal" class="btn btn-danger mobile-100 mb-3 mb-md-0">取消</button> 
                        </div>                 
                    </div>
                </div>    
                {!! Form::close() !!}                            
            </div>           
        </div>
    </div>
</div>
<!-- 名冊匯入 -->

@endsection

@section('js')
<script>

function askPublish()
{
    $("#publish").modal('show');
}

function insertStudent()
{
    $("#insert_student").modal('show');
}

function importStudent()
{
    $("#import_student").modal('show');
}

function changeVersion(identity)
{
    console.log(identity);
    if (identity == 1){
        $("#version").css('display', "");
        $("#version").prop('disabled', false);
    }else if(identity == 2){
        $("#version").css('display', "none");
        $("#version").prop('disabled', true);
    }
}

function checkImport()
{
    if($("input[name=import_file]").val() == "")
    {
        alert("請選擇匯入檔案後，在進行匯入");
        return false;
    }

    /* 
        $.ajax({
            url: "/admin/student_apply/checkExsitT13tb/{{$t04tb->class}}/{{$t04tb->term}}",
            async: false
        }).done(function (response){
            console.log(response);
            if (response.exsit){
                if (confirm('此班級已有學員報名資料，覆蓋(確定)或附加(取消)')){
                    $("input[name=cover_insert]").val("cover");
                }else{
                    $("input[name=cover_insert]").val("insert");
                }
            }else{
                $("input[name=cover_insert]").val("insert");
            }
        }); 
    */

    return true;
}

function downloadExample()
{  
    if ($("select[name=import_identity]").val() == 1){
        if ($("select[name=import_version]").val() == 'full'){
            location.href = "/import_example/student_apply/學員報名表(公務人員完整版).zip?rand=" + Math.random;
        }else if ($("select[name=import_version]").val() == 'easy'){
            location.href = "/import_example/student_apply/學員報名表(公務人員簡易版).zip?rand=" + Math.random;
        }else{
            alert("請選擇版本");
        }
    }else if ($("select[name=import_identity]").val() == 2){
        location.href = "/import_example/student_apply/學員報名表(一般民眾).zip?rand=" + Math.random;
    }else{
        alert("請選擇身分");
    }
}

</script>
@endsection