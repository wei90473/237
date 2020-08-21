@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'sponsor_agent';?>
<style>
    .search-float input{
        min-width: 1px;
    }
</style>
<div class="content">
    <div class="container-fluid">

        <!-- 頁面標題 -->
        <div class="row pc_show">
            <div class="col-sm-12">
                <h4 class="pull-left page-title">班期管理代理人員維護</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">班期管理代理人員維護</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>班期管理代理人員維護</h3>
                    </div>

                    <div class="card-body">
                        <div>
                            <form>     
                                <div class="search-float">
                                    <div class="form-row">      
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">姓名</label>
                                                </div>
                                                <input class="form-control" type="text" name="username" value="{{ $queryData['username'] }}">
                                            </div> 
                                        </div>

                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                                                                                                                                                                                                                                                                                     
                                                <a href="/admin/sponsor_agent/">
                                                    <button type="button" class="btn btn-primary">重設條件</button>
                                                </a>      
                                            </div> 
                                        </div>                                        
                                     
                                    </div>                                                                        
                                </div>                                
                            </form>                            
                        </div>  
                                              
                        <input type="hidden" name="prove" value="S">
                        <div class="table-responsive" style="height:800px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">功能</th>
                                        <th class="text-center" width="200">姓名</th>
                                        <th class="text-center">代理人</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $m09tb)
                                        <tr>
                                            <td><a href="/admin/sponsor_agent/edit/{{ $m09tb->userid }}"><button class="btn btn-primary">編輯</button></a></td>
                                            <td class="text-center">{{ $m09tb->username }}</td>
                                            <td>
                                                {{ join(", ", $data[0]->sponsorAgents->pluck('m09tb')->pluck('username')->toArray()) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
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
                {!! Form::open(['method' => 'post' , 'url' => "admin/student_apply/redirectCreateStudent"]) !!}
                <div class="search-float">
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">人員身份</label>
                        </div>
                        <select name="identity" class="form-control custom-select">
                            <option value="2">一般民眾</option>
                            <option value="1">公務人員</option>
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
                {!! Form::open(['method' => 'post' , 'url' => "admin/student_apply/importStudent", "enctype" => "multipart/form-data", "onsubmit" => "return checkImport()"]) !!}
                <!-- <input type="hidden" name="cover_insert" value=""> -->
                <div class="search-float">
                    <div class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">人員身份</label>
                        </div>
                        <select name="identity" class="form-control custom-select" onchange="changeVersion(this.value)">
                            <option value="1">公務人員</option>
                            <option value="2">一般民眾</option>
                        </select>                                                
                    </div>
                    <div id="version" class="input-group col-12" style="margin-bottom:10px;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">匯入版本</label>
                        </div>
                        <select name="version" class="form-control custom-select">
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

</script>
@endsection