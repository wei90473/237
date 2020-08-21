@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'demand_distribution';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求分配處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">需求分配列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>需求分配處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12"> 

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist" id="myTab">
                                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">機關需求人數維護</a></li>
                                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">班別分配人數維護</a></li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="home">
                                    <div class="float-left search-float" >
                                        <!-- 查詢區塊開始-->
                                        <div class="float-md mobile-100 row mr-1 mb-3">
                                         
                                                <!-- 年度 -->  
                                                 <div class="input-group col-3">                                                                                                      
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                            <select class="form-control select2 " name="yerly1" id="yerly1" onchange="yearChange()">
                                                        @foreach($queryData['choices'] as $key => $va)
                                                                <option value="{{ $key }}" {{ $queryData['yerly'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                        </select>
                                                        </div>

                                                 </div>

                                                <!-- 辦班院區 -->
                                                <div class="input-group col-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">院區</span>
                                                        <select class="form-control select2 " name="branch1" id="branch1" onchange="branch()">
                                                        @foreach(config('app.branch') as $key => $va)
                                                            <option value="{{ $key }}" {{ $queryData['branch'] == $key? 'selected' : '' }}>{{ $va }}</option>
                                                        @endforeach
                                                    </select>  
                                                    </div>


                                                </div>
                                                       <!-- 第幾次調查 -->
                                                    <div class="input-group col-3">
                                                       <div class="input-group-prepend">
                                                            <span class="input-group-text">第幾次調查</span>
                                                            <select class="form-control select2 " name="times1" id="times1">
                                                            @foreach(config('app.demand_number') as $key => $va)
                                                                <option value="{{ $key }}">{{ $va }}</option>
                                                            @endforeach
                                                        </select>
                                                        </div>

                                                    </div>

                                                    <div class="input-group col-3">
                                                        
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">機關代碼與名稱</span>
                                                            <select class="form-control select2" id="organname" name="organname">
                                                                <?php $lname = $base->getDBList('M13tb', ['organ','lname','type']);?>                                                        
                                                                @foreach($lname as $key => $va)
                                                                    <option value="{{$va->organ}}" {{ $queryData['organ'] == $va->organ? 'selected' : '' }}>{{$va->organ}} {{$va->lname}}</option>
                                                                @endforeach
                                                        </select> 
                                                        </div>
  
                                                    </div>
                                                  
                                        </div>
                                    </div>

                                            


                                                <!-- 排序 -->
                                                <input type="hidden" id="tab" name="tab" value="1">
                                                <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                                <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                                <!-- 每頁幾筆 -->
                                                <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <!-- 查詢 -->
                                                        <button id="submit_organ" class="btn mobile-100 mb-3 mr-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                        <!-- 重設條件 -->
                                                        <button class="btn mobile-100 mb-3 mr-0" onclick="doClear()">重設條件</button>
                                                    </div>
                                                </div>
                                    
                                        <!-- 查詢區塊結束-->

                                  
                                        {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_distribution/demand_orga_list_update/', 'id'=>'form']) !!}
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>班號<i class="fa fa-sort" data-toggle="sort" data-sort-field="class"></i></th>
                                                    <th>班別名稱</th>
                                                    <th>需求人數</th>
                                                    <th>分配人數</th>
                                                </tr>
                                                </thead>
                                                <tbody id="demand_orga_list">

                                                </tbody>
                                                
                                            </table>
                                            <input type="submit" value="儲存"  class="btn mobile-100 mb-3 mr-0">
                                            <!-- <button id="submit_organ_edit" class="btn mobile-100 mb-3 mr-0">儲存</button> -->
                                        {!! Form::close() !!}


                                    </div>




                                    

                                    <div role="tabpanel" class="tab-pane" id="profile">
                                        <!-- 查詢區塊開始-->
                                        <div class="search-float">
                                              


                                                <div class="form-group">
                                                     <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班號</span>
                                                        </div>
                                                        <input type="text" id="class" name="class" class="form-control" autocomplete="off" value="{{ $queryData['class'] }}" style="min-width: 120px; flex:0 1 auto">

                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">班別名稱</span>
                                                        </div>
                                                        <input type="text" id="classes_name" name="classes_name" class="form-control" autocomplete="off" value="{{ $queryData['classes_name'] }}" style="min-width: 300px; flex:0 1 auto">
                                                        <!-- <?php $list = $base->getDBList('T01tb', ['class', 'name']);?>
                                                        <select class="form-control select2" id="classes_name" name="classes_name" onchange="">
                                                            <option value="empty"></option>
                                                            <option value="全部" {{ $queryData['classes_name'] == '全部'? 'selected' : '' }}>全部</option>
                                                                @foreach($list as $key => $va)
                                                                    <option value="<?php echo strlen($va->class)==5?'0'.$va->class:$va->class;?>" {{ $queryData['classes_name'] == $va->class? 'selected' : '' }}>
                                                                        <?php echo strlen($va->class)==5?'0'.$va->class:$va->class;?>{{ $va->name }}
                                                                    </option>
                                                                @endforeach
                                                        </select> -->
                                                    </div>
                                                </div>
 
                                        
                                                <!-- 排序 -->
                                                <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                                <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                                <!-- 每頁幾筆 -->
                                                <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <!-- 查詢 -->
                                                        <button id="submit_classes" class="btn mobile-100 mb-3 mr-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                                        <!-- 重設條件 -->
                                                        <button class="btn mobile-100 mb-3 mr-0" onclick="doClear()">重設條件</button>
                                                    </div>
                                                </div>


                                        </div>
                                        <!-- 查詢區塊結束-->

                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th>班號<i class="fa fa-sort" data-toggle="sort" data-sort-field="class"></i></th>
                                                <th>班別名稱</th>
                                                <th>總需求人數</th>
                                                <th>總分配人數</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="demand_classes_list">

                                            </tbody>
                                        </table>
                                        <button type="button" id="cmdTune_Click" class="btn btn-warning btn-sm mb-3" ><i class="fa fa-plus fa-lg pr-2"></i>調整總分配人數</button>
                             


                                    </div>
                                </div><!-- Tab end -->
                                
                                
                                </div>
                             </div>
                        </div>
                    </div>
                </div
                
                11
                >
            </div>
        </div>
    </div>
    <script language="javascript">
        function doClear(){
          document.all.yerly.value = "";
          document.all.branch.value = "";
          document.all.times.value = "";
          document.all.purpose.value = "";
        }
    </script>
    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
    <script>

        $("#submit_organ").click(function(){
            let tempHTML = "<input type='hidden' name='orga' value='"+$("#organname").val()+"'>";
            $("#demand_classes_list").html(tempHTML);
            $.ajax({
                type: 'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url:"/admin/demand_distribution/demand_orga",
                data: {                       
                    yerly     : $("#yerly1").val(),
                    branch    : $("#branch1").val(),
                    times     : $("#times1").val(),
                    organ     : $("#organ").val(),
                    organname : $("#organname").val(), 
                 }, 
                success: function(data){        
                    console.log(data);
                    let dataArr = JSON.parse(data);
                   
                    // let tempHTML = "";
                    for(let i=0; i<dataArr.length; i++) {
                        tempHTML += "  <tr> <td>"+dataArr[i].classno+"</td>\
                                       <td>"+dataArr[i].classname+"</td>\
                                       <td> <input type='text' name='class_quotatot["+dataArr[i].classno+"]' value='"+dataArr[i].demand+"' ></td>\
                                       <td>"+dataArr[i].quota+"</td>\  </tr>";
                    }

                    $("#demand_orga_list").html(tempHTML);
                },
                error: function(error_msg) {
            
                    console.log('Ajax Error'+error_msg);
                }
            });
    
        });

        $("#submit_classes").click(function(){
                let tempHTML = "";
                $("#demand_classes_list").html(tempHTML);
                $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: "html",
                    url:"/admin/demand_distribution/demand_classes",
                    data: {                       
                        class     : $("#class").val(),
                        classesname : $("#classes_name").val(), 
                    },

                    success: function(data){            
                        console.log(data);
                        let dataArr = JSON.parse(data);                        
                        for(let i=0; i<dataArr.length; i++) {
                            tempHTML += "  <tr>  <td id='class_number'>"+dataArr[i].classno+"</td>\
                                            <td>"+dataArr[i].classname+"</td>\
                                            <td>"+dataArr[i].demand+"</td>\
                                            <td> <input id='class_quotatot'  type='text' value='"+dataArr[i].quota+"' ></td>\
                                            <td><a href='/admin/demand_distribution/"+dataArr[i].classno+"/edit' data-placement='top' data-toggle='tooltip' data-original-title='分配明細'><i class='fa fa-pencil'>分配明細</i></a></td></tr>";
                        }
       
                        $("#demand_classes_list").html(tempHTML);
                    },
                    error: function(error_msg) {
                
                        console.log('Ajax Error'+error_msg);
                    }
                });

        });

        $("#submit_organ_edit").submit(function(){

            alert('機關需求人數維護已更新');

        });


              

        function yearChange()
		{
            var listHTML ='';
            $.ajax({
                url:"/admin/demand_distribution/getterm",  
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                type: 'get',
                data: {
                    year: $('#yerly1').val(),
                    branch: $('#branch1').val(),           
                },
                success: function(data){
                // Get select
                var times_select = document.getElementById('times1');
                $(times_select).html("");//清空下拉選單
                // Add options
                for (var i in data) {
                    if(data[i].times != null){
                        $(times_select).append('<option value=' + data[i].times + '>' + data[i].times + '</option>');
                    }
                }
                // Set selected value
                $(times_select).val(data[1]);
                }
            });
    
        };

        // 調整總分配人數
        $("#cmdTune_Click").click(function(){
            // var insert = 1;

            // $.each( $('.institution'), function( key, va ) {

            //     // 取得機關總數
            //     var organTotal = parseInt($(va).find('.organ_total').html());
            //     // 取得平均
            //     var average = parseInt(organTotal / row);
            //     // 已經計算的數量
            //     var organNowTotal = 0;
            //     // 將平均值寫入input
            //     $.each( $(va).find('input'), function( key, vb ) {

            //         $(vb).val(average)

            //         organNowTotal += average;
            //     });

            //     // 如果還有餘數
            //     if (organTotal > organNowTotal) {

            //         for (i=organNowTotal; i<organTotal; i++) {

            //             // 取得輪到誰+1
            //             input = $(va).find('.term'+insert);
            //             // 欄位值+1
            //             $(input).val(parseInt($(input).val()) + 1);
            //             // 換下一個欄位
            //             insert = (insert + 1 >  row)? 1 : insert + 1;
            //         }
            //     }

            // });
            // alert('將更改系統可分配人數上限	!');
            // 計算總數
            // countTotal();
            var class_id = '';
            $('#demand_classes_list tr').each(function() {
                 class_id = this.cells[0].innerHTML;  
            });          

            $.ajax({    
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                type: 'put',
                dataType: "html",
                url:"/admin/demand_distribution/set_tune_quotatot/"+class_id,
                data: {                       
                    quotatot    : $("#class_quotatot").val(),
                 },
                timeout:20000,
                success: function(data){
                      alert("儲存成功，目前總分配人數已變更為"+$("#class_quotatot").val()); 
                },
                error: function(error_msg) {
            
                    console.log('Ajax Error'+error_msg);
                }
            });

        });
         yearChange();
    </script>
@endsection