@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'demand_survey_commissioned';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">委訓班需求調查處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">委訓班需求調查處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>委訓班需求調查處理</h3>
                        </div>
                        <input type="hidden" id="commissioned_id" namse="commissioned_id" value="{{ $queryData['id'] }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <!-- 班別名稱 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                         <div class="input-group-prepend">
                                                            <!-- <span class="input-group-text">年度</span>
                                                        
                                                        <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            <option></option>
                                                            @for($i = (int)date("Y")-1911; $i >= 100 ; $i--)
                                                                <option value="{{$i}}">{{$i}}
                                                                
                                                                </option>
                                                            @endfor
                                                        </select>
                                                        </div> -->
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班別名稱</span>
                                                       
                                                        <input type="text" id="audit_status" name="audit_status" class="form-control" autocomplete="off" value="{{ $queryData['audit_status'] }}">                                                    
                                                        </div>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">委訓機關</span>
                                                       
                                                        <input type="text" id="audit_status" name="audit_status" class="form-control" autocomplete="off" value="{{ $queryData['audit_status'] }}">                                                    
                                                        </div>
                                                     </div>    
                                                     <div class="input-group"> 
                                                     </br>
                                                     </div>  

<!-- 
                                                     <div class="input-group">
                                                        <div class="input-group-prepend">

                                                        <?php
                                                            $sdate = isset($data->sdate)? $data->sdate : '';
                                                            $edate = isset($data->edate)? $data->edate : '';
                                                        ?>
                                                       
                                                        <span class="input-group-text">填報結束日期</span>
                                                        <input class="date form-control" value="{{$sdate}}" type="text" id="sdate" name="sdate">
                                                    
                                                    

                                                        <span class="input-group-text">填報結束日期</span>
                                                        <input class="date form-control" value="{{$edate}}" type="text" id="edate" name="edate">
                                                            

                                                            
                                                     

                                                        <span class="input-group-text">審核狀態</span>
                                                    
                                                             <select id="audit_status" name="audit_status" class="select2 form-control select2-single input-max" required>
                                                                <option value="">請選擇</option>
                                                                @foreach(config('app.demand_audit_status') as $key => $va)
                                                                    <option value="{{ $key }}" >{{ $va }}</option>
                                                                @endforeach
                                                             </select>

                                                        </div>
                                                    
                                                     </div>  -->
                                            </div>
                                               
                                            <div class="form-group">
                                                    <div class="input-group">
                                                        <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>&nbsp;&nbsp;
                                                        <button type="button" class="btn btn-sm btn-info"><i class="fa fa-remove fa-lg pr-1"></i>重設條件</button>&nbsp;&nbsp;

                                                        <!-- <a href="/admin/demand_survey_commissioned/{{$queryData['id']}}/import" >
                                                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-plus fa-lg pr-1"></i>轉入班別資料</button>&nbsp;&nbsp;
                                                        </a> -->
                                                        <button type="button" id="import_class_data"  class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-circle-o fa-lg pr-1"></i>批次轉入未開班班別資料</button>&nbsp;&nbsp;
                                                        <a href="/admin/demand_survey_commissioned/{{$queryData['id']}}/export_doc" >
                                                             <button type="button" class="btn btn-sm btn-success"><i class="fa fa-file-word-o fa-lg pr-1"></i>彙總表列印（word）</button>&nbsp;&nbsp;
                                                        </a>
                                                        <a href="/admin/demand_survey_commissioned/{{$queryData['id']}}/export_odf" >
                                                        <button type="button" class="btn btn-sm btn-success"><i class="fa fa-edit fa-lg pr-1"></i>彙總表列印（ODF）</button>
                                                        </a>
                                                    </div>
                               
                                            </div>

                                            <div class="form-group">
                                            <div class="input-group">
                                                        <span>批次審核&nbsp;</span>
                                                       
                                                            <button type="button" id="audit_ing"  class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-circle-o fa-lg pr-1"></i>審核中</button>&nbsp;&nbsp;
                                                            <button type="button" id="audit_accept"  class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-check-circle-o fa-lg pr-1"></i>審核通過</button>&nbsp;&nbsp;
                                                            <button type="button" id="audit_reject"  class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-times fa-lg pr-1"></i>審核不通過</button>
                
                                                 
                                                    </div>
                                            </div>

                                    </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <!-- <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button> -->
                                        </form>
                                    </div>

    

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                            
                                                <th class="text-center"><input type="checkbox" name="all" onclick="check_all(this,'audit_id')" /></th>
                                                <th class="text-center">委訓機關</th>
                                                <th class="text-center">班別</th>
                                                <th class="text-center">期數</th>
                                                <th class="text-center">每期人數</th>
                                                <th class="text-center">訓期（天）</th>
                                                <th class="text-center">建議辦理時間</th>
                                                <th class="text-center">審核狀態</th>
                                                <th class="text-center">已開班</th>
                                                <th class="text-center">功能</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <td class="text-center">
                                                    <input type="checkbox" class="mx-2" id="audit_id" name="audit_id" value="{{ $va->id }}">
                                                    </td>
                                                    <td>{{ $va->entrusting_orga }} {{ $va->entrusting_unit }} </td>
                                                    <td>{{ $va->class_name }} </td>
                                                    <td>{{ $va->periods }} </td>
                                                    <td>{{ $va->periods_people }} </td>
                                                    <td>{{ $va->training_days }} </td>
                                                    <td>
                                                    @foreach($dataDemandTransact as $va2)                                                  
                                                        @if( $va2->id ==  $va->id  )
                                                            第{{ $va2->demand_id }}次{{ $va2->sdate }}~{{ $va2->edate }}</br>
                                                        @endif
                                                    @endforeach
                                                    </td>
                                                    <td>{{ $va->sdate }} </td>
                                                    <td>{{ $va->audit_status }} </td>
                                                    <td>{{ $va->enable }} </td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/demand_survey_commissioned/{{ $va->id }}/audit_edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])

                                </div>
                            </div>
                        </div>

                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])

                    </div>
                    <div class="card-footer">
                    <button type="button" onclick="window.history.go(-1); return false;" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection


@section('js')
    <script language="Javascript">

    function check_all(obj,cName)
    {
        var checkboxs = document.getElementsByName(cName);
        for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;}
    }


    var btn_accept=document.getElementById("audit_accept");
    btn_accept.onclick=function(){
      var obj=document.getElementsByName("audit_id");
      var selected=[];
      for (var i=0; i<obj.length; i++) {
        if (obj[i].checked) {
          selected.push(obj[i].value);
          }
      }
      $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                url: '/admin/demand_survey_commissioned/audit_accept',
                data: { 
                    selected: selected},
                success: function(data){
                  alert("修改成功");
                  window.location.reload();
                },
                error: function() {
                    alert('Ajax Error'+ $('#commissioned_id').val());
                }
       });
    };

    var btn_reject=document.getElementById("audit_reject");
    btn_reject.onclick=function(){
      var obj=document.getElementsByName("audit_id");
      var selected=[];
      for (var i=0; i<obj.length; i++) {
        if (obj[i].checked) {
          selected.push(obj[i].value);
          }
        }
        $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                url: '/admin/demand_survey_commissioned/audit_reject',
                data: { 
                    selected: selected},
                success: function(data){
                  alert("修改成功");
                  window.location.reload();
                },
                error: function() {
                    alert('Ajax Error'+ $('#commissioned_id').val());
                }
       });
    };

    var btn_ing=document.getElementById("audit_ing");
    btn_ing.onclick=function(){
      var obj=document.getElementsByName("audit_id");
      var selected=[];
      for (var i=0; i<obj.length; i++) {
        if (obj[i].checked) {
          selected.push(obj[i].value);
          }
        }
        $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                url: '/admin/demand_survey_commissioned/audit_ing',
                data: { 
                    selected: selected},
                    success: function(data){
                    alert("修改成功");
                    window.location.reload();
                    },
                    error: function() {
                        alert('Ajax Error'+ $('#commissioned_id').val());
                    }
       });
    };
 

    var import_class_data=document.getElementById("import_class_data");
    import_class_data.onclick=function(){
      var obj=document.getElementsByName("audit_id");
      var selected=[];
      for (var i=0; i<obj.length; i++) {
        if (obj[i].checked) {
          selected.push(obj[i].value);
          }
        }

        $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                url: '/admin/demand_survey_commissioned/import_class_data/{{ $queryData['id'] }}',
                data: { 
                    selected: selected
                },
                success: function(data){
                alert("修改成功");
                window.location.reload();
                },
                error: function() {
                    alert('Ajax Error'+ data);
                }
       });
    };

      
  </script>
   @endsection
