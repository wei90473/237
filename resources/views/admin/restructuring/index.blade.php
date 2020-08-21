@extends('admin.layouts.layouts')
@section('content')
<style>
    .th25{
        /*width:25%;*/
    }
    .restructuring th{
        vertical-align:middle !important;
        text-align: center;
    }
</style>
    <?php $_menu = 'restructuring';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">組織改制對照表維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">組織改制對照表維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>組織改制對照表維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-left search-float">
                                        <form>
                                            <div class="form-row">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">機關代碼</span>
                                                        </div>
                                                        <input type="text" name="enrollorg" class="form-control" value="{{ $queryData['restructuring_detail']['enrollorg'] }}">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">機關名稱</span>
                                                        </div>
                                                        <input type="text" name="enrollname" class="form-control" value="{{ $queryData['m17tb']['enrollname'] }}">
                                                    </div>
 
                                                </div>
                                            </div>
                                                                                  
                                            <button class="btn">搜尋</button>
                                            <a href="/admin/restructuring"><button class="btn btn-primary">重置條件</button></a>
                                            <a href="/admin/restructuring/create">
                                                <button type="button" class="btn btn-primary"><i class="fa fa-plus fa-lg pr-2"></i>新增改制資料</button>
                                            </a>
                                        </form>

                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0 restructuring">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="th25">功能</th>
                                                    <th colspan="2" class="th25">改制前</th>
                                                    <th colspan="2" class="th25">改制後</th>
                                                    <th rowspan="2" class="th25">資料建立時間</th>
                                                </tr>
                                                <tr>
                                                    <th>機關代碼</th>
                                                    <th>機關名稱</th>
                                                    <th>機關代碼</th>
                                                    <th>機關名稱</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $restructuring)
                                                    <?php 
                                                        $len = ($restructuring->before->count() > $restructuring->after->count()) ? 
                                                            $restructuring->before->count() : $restructuring->after->count();
                                                    ?>
                                                    
                                                    <tr>
                                                        <td rowspan="{{ $len }}" class="text-center">
                                                            <a href="/admin/restructuring/edit/{{ $restructuring->id }}"><button class="btn btn-primary">編輯</button></a>
                                                        </td>
                                                        @if (isset($restructuring->before[0]))
                                                            <td class="text-center">{{ $restructuring->before[0]->enrollorg }}</td>
                                                            <td class="text-center">{{ $restructuring->before[0]->m17tb->enrollname }}</td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif

                                                        @if (isset($restructuring->after[0]))
                                                            <td class="text-center">{{ $restructuring->after[0]->enrollorg }}</td>
                                                            <td class="text-center">{{ $restructuring->after[0]->m17tb->enrollname }}</td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif  
                                                        <td rowspan="{{ $len }}" class="text-center">
                                                            {{ $restructuring->created_at_date }}
                                                        </td>
                                                    </tr>
                                                    @for ($i=1; $i<$len; $i++)
                                                    <tr>
                                                        @if (isset($restructuring->before[$i]))
                                                            <td class="text-center">{{ $restructuring->before[$i]->enrollorg }}</td>
                                                            <td class="text-center">{{ $restructuring->before[$i]->m17tb->enrollname }}</td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif

                                                        @if (isset($restructuring->after[$i]))
                                                            <td class="text-center">{{ $restructuring->after[$i]->enrollorg }}</td>
                                                            <td class="text-center">{{ $restructuring->after[$i]->m17tb->enrollname }}</td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif                                                        
                                                    </tr>
                                                    @endfor 
                                                    
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>                                    
                                </div>
                            </div>
                                    @if($data)
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif
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
<script>

</script>
@endsection