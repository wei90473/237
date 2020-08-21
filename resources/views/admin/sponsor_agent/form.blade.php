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
                            班務人員： {{ $sponsor->username }}
                        </div>
                        <div>
                            {{ Form::open(['method' => 'post', 'url' => "/admin/sponsor_agent/{$sponsor->userid}"]) }}    
                                <div class="search-float">
                                    <div class="form-row">      
                                        <div class="form-group col-md-4">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">姓名</label>
                                                </div>

                                                <select class="select2" name="agent_userid">
                                                    <option></option>
                                                    @foreach ($m09tbs as $m09tb)
                                                        <option value="{{ $m09tb->userid }}">{{ $m09tb->username.' ('.$m09tb->userid.')' }}</option>
                                                    @endforeach 
                                                </select>
                                            </div> 
                                        </div>

                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <button type="submit" class="btn btn-primary">新增</button>                                                                                                                                                                                                                                                                                          
                                            </div> 
                                        </div>                                        
                                     
                                    </div>                                                                        
                                </div>                                
                            {{ Form::close() }}                      
                        </div>  
                                              
                        <input type="hidden" name="prove" value="S">
                        <div class="table-responsive" style="height:800px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">功能</th>
                                        <th class="text-center">代理人姓名</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sponsor->sponsorAgents as $agent)
                                    <tr>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger" onclick="deleteAgent('{{ $agent->id }}', '{{ $agent->m09tb->username }}')">
                                                <i class="fa fa-trash fa-lg pr-1"></i>
                                                刪除
                                            </button>
                                        </td>
                                        <td class="text-center">{{ $agent->m09tb->username.' ('.$agent->m09tb->userid.')' }}</td>
                                    <tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> 

                        <!-- include('admin/layouts/list/pagination', ['paginator' => $t27tbs, 'queryData' => $queryData])                    -->
                    </div>
                    <div class="card-footer">

                        <a href="/admin/sponsor_agent">
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>      
                    {{ Form::open(['method' => 'delete', 'id' => 'deleteAgentForm']) }}               
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function deleteAgent(id, username)
    {
        $("#deleteAgentForm").attr("action", "/admin/sponsor_agent/" + id);
        if (confirm("確定要刪除 " + username + " 代理人嗎？")){
            $("#deleteAgentForm").submit();
        }        
    }
</script>
@endsection