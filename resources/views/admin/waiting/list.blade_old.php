@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')

    <?php $_menu = 'waiting';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">講座擬聘處理</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">講座擬聘處理列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>講座擬聘處理</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">

                                    <!-- 搜尋 -->
                                    <div class="float-left search-float">
                                        <form method="get" id="search_form">

                                            <!-- 年度 -->

                                            <div class="float-md mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">年度</span>
                                                        </div>
                                                        <select type="text" id="yerly" name="yerly" class="browser-default custom-select"  value="{{ $queryData['yerly'] }}" style="min-width: 80px; flex:0 1 auto">
                                                            <option></option>
                                                            @for($i = (int)date("Y")-1911; $i >= 100 ; $i--)
                                                                <option value="{{$i}}" {{ $queryData['yerly'] == $i? 'selected' : '' }} >{{$i}}

                                                                </option>
                                                            @endfor
                                                        </select>
                                                </div>
                                            </div>

                                            <?php $list = $base->getDBList('T01tb', ['class', 'name']);?>
                                            <!-- 班別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">班別</span>
                                                    </div>
												
                                                    <select class="form-control select2" id="class" name="class" onchange="getTerm()">
													    <option value="empty"></option>
                                                        <option value="全部" {{ $queryData['class'] == '全部'? 'selected' : '' }}>全部</option>
                                                        @foreach($list as $key => $va)
                                                            <option value="<?php echo strlen($va->class)==5?'0'.$va->class:$va->class;?>" {{ $queryData['class'] == $va->class? 'selected' : '' }}><?php echo strlen($va->class)==5?'0'.$va->class:$va->class;?>{{ $va->name }}</option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>

                                            <!-- 期別 -->
                                            <div class="pull-left mobile-100 mr-1 mb-3">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">期別</span>
                                                    </div>
                                                    <select class="form-control select2"  id="term" name="term">
													    <option value="empty"></option>
                                                    </select>
                                                    <input id="sterm" type="text" value="{{$queryData['class']}}" hidden>
                                                </div>
                                            </div>

                                            <!-- 遴聘與否 -->
                                            <div class="pull-left mobile-100 mr-1 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend" style="display:flex; align-items:center;">
                                                        <span class="input-group-text">遴聘與否</span>														
                                                    @foreach(config('app.yorn') as $key => $va)
                                                        <input type="radio" id="hire" name="hire" style="min-width:20px; margin-left:5px;" value="{{ $key }}" {{ old('hire', (isset($data->hire))? $data->hire : 1) == $key? 'checked' : '' }}>{{ $va }}
                                                    @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{ $queryData['_sort_field'] }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{ $queryData['_sort_mode'] }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{ $queryData['_paginate_qty'] }}">

                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                                        </form>
                                    </div>

                                    <div class="float-md-right">
										<button type="button" id="SMS" name="SMS" class="btn btn-primary btn-sm mb-3" onclick="javascript:SMS();"> <i class="fa fa-plus fa-plus"></i>寄送簡訊</button>
										<button id="mark1" type="button" class="btn btn-primary btn-sm mb-3" onclick="javascript:cmdMark(0);"> <i class="fa fa-plus fa-lg pr-2"></i>遴聘註記(有料)</button>
									    <button id="mark2" type="button" class="btn btn-primary btn-sm mb-3" onclick="javascript:cmdMark(1);"> <i class="fa fa-plus fa-lg pr-2"></i>聘註記(無料)</button>
											
                                        <!-- 新增 -->
                                        <a href="/admin/waiting/create">
                                            <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="data_table" class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="70">編號</th>
                                                <th class="text-center">遴聘與否</th>
                                                <th>課程名稱</th>
                                                <th>講座姓名</th>
                                                <th>服務機關</th>
                                                <th>現職</th>
                                                <th>聯絡人</th>
                                                <th>電話(公一)</th>
                                                <th>傳真(公)</th>
                                                <th class="text-center" width="70">修改</th>
                                                <th class="text-center" width="70">刪除</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($data as $va)
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <td class="text-center">{{ $startNo + $loop->iteration }}</td>
                                                    <td class="text-center">{{ $va->hire }}</td>
                                                    <td>{{ $va->name }}({{ $va->term }}/{{ $va->course }})</td>
                                                    <td>{{ $va->cname }}</td>
                                                    <td>{{ $va->dept }}</td>
                                                    <td>{{ $va->position }}</td>
                                                    <td>{{ $va->liaison }}</td>
                                                    <td><?php echo $va->offtela1!=''?'(':'';?>{{ $va->offtela1 }}<?php echo $va->offtela1!=''?')':'';?>{{ $va->offtelb1 }}</td>
                                                    <td><?php echo $va->offfaxa!=''?'(':'';?>{{ $va->offfaxa }}<?php echo $va->offfaxa!=''?')':'';?>{{ $va->offfaxb }}</td>

                                                    <!-- 修改 -->
                                                    <td class="text-center">
                                                        <a href="/admin/waiting/{{ $va->id }}/edit" data-placement="top" data-toggle="tooltip" data-original-title="修改">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>

                                                    <!-- 刪除 -->
                                                    <td class="text-center">
                                                        <span onclick="$('#del_form').attr('action', '/admin/waiting/{{ $va->id }}');" data-toggle="modal" data-target="#del_modol" >
                                                            <span class="waves-effect waves-light tooltips" data-placement="top" data-toggle="tooltip" data-original-title="刪除">
                                                                <i class="fa fa-trash text-danger"></i>
                                                            </span>
                                                        </span>
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
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')
@endsection
@section('js')
<script>
			 $(document).ready(function () {
				 var rows = document.getElementById("data_table").getElementsByTagName("tr").length;			

				 if(rows<2)
				 {
					 document.getElementById("SMS").disabled = true;
					 document.getElementById("mark1").disabled = true;
					 document.getElementById("mark2").disabled = true;
				 }
				 else
				 {
					 document.getElementById("SMS").disabled = false;
					 document.getElementById("mark1").disabled = false;
					 document.getElementById("mark2").disabled = false;
				 }

                 if($("#sterm").val() != "" || $("#class").val() != "") {
                    getTerm();
                 }
			 });
	 
			  function SMS()
			  {
				  if($('#class').val()=="")
				  {
					  alert('請選擇班別!!');
					  return;					  
				  }
				  if($('#term').val()=="")
				  {
					  alert('請選擇期別!!');
					  return;					  
				  }
				  $.ajax({
						type: 'post',
						 headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						dataType: "html",
						url:"/admin/waiting/sms",
						data: { class: $('#class').val(), term: $('#term').val()},
						success: function(data){
							 if (confirm(data)) {
									SendSMS();
							};					
						},
						error: function() {
							console.log('Ajax Error');
						}
					});
			  };
			  
			  function SendSMS()
			  {
				  $.ajax({
						type: 'post',
						 headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						dataType: "html",
						url:"/admin/waiting/sendsms",
						data: { class: $('#class').val(), term: $('#term').val()},
						success: function(data){
							 alert(data);		
						},
						error: function() {
							console.log('Ajax Error');
						}
					});
              };
              
              function getTerm()
			  {
                $.ajax({
                    type: 'post',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    dataType: "html",
                    url:"/admin/waiting/getterm",
                    data: { class: $('#class').val()},
                    success: function(data){
                        let dataArr = JSON.parse(data);
                        let tempHTML = "";
                        for(let i=0; i<dataArr.length; i++) {
                            tempHTML += "<option value='"+dataArr[i].term+"' "+($('#sterm').val()==dataArr[i].term?'selected':'')+">"+dataArr[i].term+"</option>";
                        }
                        $("#term").html(tempHTML);	
                    },
                    error: function() {
                        console.log('Ajax Error');
                    }
                });
			  };
			  
			  function cmdMark(selIndex)
			  {
                    let hire = '';
                    if($('input[name=hire]:checked').val() == undefined) {
                        hire = '';
                    }
                    else {
                        hire = $('input[name=hire]:checked').val();
                    }

                    $.ajax({
                        type: 'post',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        dataType: "html",
                        url:"/admin/waiting/mark",
                        data: { class: $('#class').val(), term: $('#term').val(), hire: hire, mark:selIndex},
                        success: function(data){
                            if (confirm(data)) {
                                window.reload();       
                            }					
                        },
                        error: function() {
                            console.log('Ajax Error');
                        }
                    });
		     	};
</script>
@endsection