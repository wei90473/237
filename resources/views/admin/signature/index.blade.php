@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<?php $_menu = 'signature';?>
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
                <h4 class="pull-left page-title">研習證明書電子章設定</h4>
                <ol class="breadcrumb pull-right">
                    <li><a href="/admin">首頁</a></li>
                    <li class="active">研習證明書電子章設定</li>
                </ol>
            </div>
        </div>

        <!-- 提示訊息 -->
        @include('admin/layouts/alert')

        <div class="container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa fa-list pr-2"></i>研習證明書電子章設定</h3>
                    </div>

                    <div class="card-body">
                        <div>
                            <form>     
                                <div class="search-float">
                                    <div class="form-row">      
                                        <div class="form-group col-md-3">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text">顯示名稱</label>
                                                </div>
                                                <input class="form-control" type="text" name="name" value="{{ $queryData['name'] }}">
                                            </div> 
                                        </div>

                                        <div class="form-group col-md-3">
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>                                                                                                                                                                                                                                                                                      
                                            <a href="/admin/signature/create">
                                                <button type="button" class="btn btn-primary">新增</button>                                                                                                                                                                                                                                                                                      
                                            </a>    
                                        </div>                                        

                                    </div>                                                                        
                                </div>                                
                            </form>                            
                        </div>  
                                              
                        <div class="table-responsive" style="height:800px;">
                            <table id="data_table" class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="100">功能</th>
                                        <th class="text-center" width="100">顯示順序</th>
                                        <th class="text-center" width="200">顯示名稱</th>
                                        <th class="text-center">電子章簽名</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $signature)
                                        <tr>
                                            <td><a href="/admin/signature/edit/{{ $signature->id }}"><button class="btn btn-primary">編輯</button></a></td>
                                            <td class="text-center">{{ $signature->sort }}</td>
                                            <td class="text-center">{{ $signature->name }}</td>
                                            <td class="text-center">
                                                <img style="width: 50%;" src="/Uploads/signatures/{{ $signature->img_path }}">
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
                        <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
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

</script>
@endsection