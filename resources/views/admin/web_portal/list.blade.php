@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'web_portal';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班別資料</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">入口網站代碼維護</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>入口網站代碼維護</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <!-- 搜尋條件 -->
                                    <div class="search-float">
                                        <form method="get" id="search_form">
                                            <!-- 學院代碼 -->
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <div class="input-group col-4">
                                                    <div class="pull-left input-group-prepend">
                                                        <span class="input-group-text">學院代碼</span>
                                                    </div>
                                                    <input type="text" id="code" name="code" class="form-control" autocomplete="off" value="{{ isset($queryData['code'])?$queryData['code']:'' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="float-md mobile-100 row mr-1 mb-3 ">
                                                <!-- 學院專長 -->
                                                <div class="input-group col-6">
                                                     <div class="input-group-prepend">
                                                        <span class="input-group-text">學院專長</span>
                                                    </div>
                                                    <input type="text" id="name" name="name" class="form-control" autocomplete="off" value="{{ isset($queryData['name'])?$queryData['name']:'' }}">
                                                </div>
                                            </div>
                                            <!-- 排序 -->
                                            <input type="hidden" id="_sort_field" name="_sort_field" value="{{isset($queryData['_sort_field'])?$queryData['_sort_field']:''  }}">
                                            <input type="hidden" id="_sort_mode" name="_sort_mode" value="{{isset($queryData['_sort_mode'])?$queryData['_sort_mode']:'' }}">
                                            <!-- 每頁幾筆 -->
                                            <input type="hidden" id="_paginate_qty" name="_paginate_qty" value="{{isset($queryData['_paginate_qty'])?$queryData['_paginate_qty']:''  }}">

                                        <div class="float-left">
                                            <!-- 查詢 -->
                                            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>查詢</button>
                                            <!-- 重設條件 -->
                                            <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="doClear()" >重設條件</button>
                                            <!-- Sort -->
                                            <!-- <button type="button" class="btn mobile-100 mb-3 mb-md-0" onclick="javascript:ChangeSort();">排序</button> -->
                                            <!-- 新增班別資料 -->
                                            <a href="/admin/web_portal/category">
                                                <button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0"><i class="fa fa-plus fa-lg pr-2"></i>班別類別/專長代碼維護</button>
                                            </a>
                                        </div>    
                                        </form>
                                    </div>

                                    

                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <thead>
                                            <tr>
                                                <th class="text-center" width="80">功能</th>
                                                <!--th class="text-center" width="70">編號</th-->
                                                <th>學院代碼</th>
                                                <th>學院專長</th>
                                                <th>入口網站代碼</th>
                                                <th>入口網站專長</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(isset($data))
                                            @foreach($data as $va)
                                            
                                                <?php $startNo = ($data->currentPage() > 1)? $data->currentPage() * $data->perPage() - $data->perPage() : 0;?>
                                                <tr>
                                                    <!-- 修改 -->
                                                    <td class="text-center" onclick="edit({{ $va->code }})">
                                                        <a href="/admin/web_portal/edit/{{ $va->code }}" data-placement="top" data-toggle="tooltip" data-original-title="編輯">
                                                            <i class="fa fa-pencil">編輯</i>
                                                        </a>
                                                    </td>
                                                    <td>{{ $va->code }}</td>
                                                    <td>{{ $va->name }}</td>
                                                    <td>{{ $va->category }}</td>
                                                    <td>{{ $va->categoryname }}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($data))
                                    <!-- 分頁 -->
                                    @include('admin/layouts/list/pagination', ['paginator' => $data, 'queryData' => $queryData])
                                    @endif

                                </div>
                            </div>
                        </div>
                        @if(isset($data))
                        <!-- 列表頁尾 -->
                        @include('admin/layouts/list/card_footer', ['paginator' => $data, 'queryData' => $queryData])
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="BatchModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">批次增刪</h4>
                    </div>
                    <div class="modal-body">
                         請輸入年度<input type="text" class="form-control number-input-max" id="year" name="year"></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="BatchAdd();">批次新增</button>
    					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="BatchDel();">批次刪除</button>
    				    <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="ClassIOModal" role="dialog">
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
    			 <!-- form start -->  
                    <div class="modal-header">
                        <h4 class="modal-title">班號匯出匯入</h4>
                    </div>
                    <div class="modal-body">
                         請輸入年度<input type="text" class="form-control number-input-max" id="year2" name="year2"></input>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"  onclick="ClassOutput();">匯出</button>
    				    <input type="file" class="btn btn-sm btn-info" id="upload" name="upload" style="display:none;" />
    					<button type="button" class="btn btn-primary"  onclick="ClassImport();">匯入</button>
    				    <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 資料排序 modal -->
    <div class="modal fade" id="SortModal" role="dialog" css="width:300px;">
        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/classes/rank', 'id'=>'form2']) !!}
        <div class="panel panel-primary list-panel" id="list-panel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">排序</h4>
                    </div>
                    <div class="modal-body" height="200px;">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkrank()">儲存</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                         <table class="table table-bordered mb-0" height="200px;" id="table_sort" name="table_sort">
                            <thead>
                            <tr>
							    <th>班號</th>
                                <th>班別名稱(中文)</th>    
								<th>排序</th>   											
                                </tr>
                            </thead>
                            <tbody>
                            @if(isset($ranklist)) 
                            <?php $rank= 1; ?>   
                            @foreach($ranklist as $va)

                                <tr draggable="true">												
                                    <td>{{ $va->class.$va->branchcode }}</td>
                                    <td>{{ $va->name }}</td>
									<td>
										<a href="#" class="up">上</a> <a href="#" class="down">下</a>
									</td>
                                     <input type="hidden" name="{{$va->class.$va->branchcode}}" value="{{ $va->rank==''?$rank:$va->rank }}">
                                </tr>
                            <?php $rank= $rank+1; ?>      
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkrank()">儲存</button>
    				    <button type="button" class="btn btn-danger" data-dismiss="modal">關閉</button>
                    </div>

                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

<!-- 維護課程簡介 modal -->
<div class="modal fade bd-example-modal-lg classIntro" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document" style="max-width:700px;">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">維護課程簡介</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    <div class="col-md-12">
                        <span class="col-md-3">班別</span>
                    
                        <select class="form-control select2" id="classIntro_classType" name="classIntro_classType" style="width:300px;display:inline-block;">
                            
                        </select>

                        <button type="button" class="btn btn-primary">查詢</button>
                    </div>
                    <hr>
                    <div class="col-md-12" style="height:300px; overflow:auto;">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th style="padding:5px;">課程名稱</th>
                                <th style="padding:5px;">課程大綱</th>
                                <th style="padding:5px;">修改</th>
                                <th style="padding:5px;">刪除</th>
                            </tr>
                            <tr>
                                <td style="padding:5px;">人力運用實務</td>
                                <td style="padding:5px;">課程內容...</td>
                                <td style="padding:5px;" class="text-center">
                                    <span class="waves-effect waves-light tooltips">
                                        <i class="fa fa-pencil text-primary"></i>
                                    </span>
                                </td>
                                <td style="padding:5px;" class="text-center">
                                    <span class="waves-effect waves-light tooltips">
                                        <i class="fa fa-trash text-danger"></i>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <hr>
                    <div class="card-body pt-4">
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">課程名稱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="idno" name="idno" placeholder="課程名稱" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">課程大綱</label>
                            <div class="col-sm-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" placeholder="課程大綱"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" align="center">
                        <button type="button" class="btn btn-primary">新增</button>
                        <button type="button" class="btn btn-primary">儲存</button>
                        <button type="button" class="btn btn-danger">取消</button>
                    </div>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-info" data-dismiss="modal">離開</button>
		        </div>
		    </div>
		</div>
	</div>

    <!-- 維護追蹤培訓班別 modal -->
	<div class="modal fade bd-example-modal-lg trainClass" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document" style="max-width:700px;">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">維護追蹤培訓班別</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <span>起訖班號： </span>
                            <input type="text" class="form-control input-max" id="sclass" style="width:130px;"> ～ 
                            <input type="text" class="form-control input-max" id="eclass" style="width:130px;">

                            <button type="button" class="btn btn-primary" style="margin-left:5px;">查詢</button>
                            <button type="button" class="btn btn-info" style="margin-left:5px;">列印</button>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12" style="height:400px; overflow:auto;">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th style="padding:5px;">課程名稱</th>
                                <th style="padding:5px;">課程大綱</th>
                            </tr>

                        </table>
                    </div>
		        </div>
		        <div class="modal-footer">
			        <button type="button" class="btn btn-info" data-dismiss="modal">離開</button>
		        </div>
		    </div>
		</div>
	</div>

 <!-- 維護跨區報名機關 modal -->
 <div class="modal fade bd-example-modal-lg crossArea" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document" style="max-width:700px;">
            <input type="hidden" id="starCross" name="starCross" value="0">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">維護跨區報名機關</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 control-label text-md-right pt-2">機關代碼</label>
                        <div class="col-sm-4">
                            <div class="input-group bootstrap-touchspin number_box">
                                <input type="text" id="organ_code" class="form-control number-input-max">
                            </div>
                        </div>

                        <label class="col-sm-2 control-label text-md-right pt-2">機關名稱</label>
                        <div class="col-sm-4">
                            <div class="input-group bootstrap-touchspin number_box">
                                <input type="text" id="organ_name" class="form-control number-input-max">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" style="display: flex;align-items: center;">
                        <label class="col-sm-2 control-label text-md-right pt-2">選項</label>
                        <div class="col-sm-4">
                            <div class="input-group bootstrap-touchspin number_box" style="display: flex;align-items: center;">
                                <input type="radio" name="crossRadio" value="ALL"><span style="margin-right:10px;">全部</span>
                                <input type="radio" name="crossRadio" value="Y" checked="checked"><span>可跨區機關</span>
                            </div>
                        </div>
                        
                        <div>
                            <button type="button" onclick="javascript:showCrossArea();" class="btn btn-primary">查詢</button>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12" style="height:300px; overflow:auto;">
                        <table class="table table-bordered mb-0">
                             <thead>
                                <tr>
                                <th style="padding:5px;">跨區註記</th>
                                <th style="padding:5px;">機關代碼</th>
                                <th style="padding:5px;">機關名稱</th>
                                </tr>
                            </thead>                           
                            <tbody id="crosslist">               
                            </tbody>            
                        </table>

                    </div>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="starCross()" id="starCrossedit" style="display: flex">修改</button>
                    <button type="button" class="btn btn-warning" onclick="starCross()" id="closeCrossedit" style="display: none">鎖定</button>
                    <!-- <button type="button" class="btn btn-primary">儲存</button> -->
			        <button type="button" class="btn btn-info" data-dismiss="modal">離開</button>
		        </div>
		    </div>
		</div>
	</div>
    
    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection
@section('js')
<script>
    function doClear(){
      document.all.yerly.value = "";
      document.all.class.value = "";
      document.all.branchname.value = "";
      document.all.branch.value = "";
      document.all.process.value = "";
      document.all.name.value = "";
      document.all.type.value = "";
      document.all.traintype.value = "";
      document.all.typeone.value = "";
    }
    //委訓班
    function showclient(){
        var title = $("select[name=process]").val()
        if(title =='2'){
            $('#clientclass').css('display','inline-flex'); 
        }else{
            $('#clientclass').css('display','none'); 
        }
    }

	$(document).ready(function(){
		$(".up,.down").click(function(){
			var row = $(this).parents("tr:first");
			if ($(this).is(".up")) {
                var classes = $(this).parent().prev().prev().html();
                var change = $(this).parent().parent().prev().children().html();
				row.insertBefore(row.prev());
                
                if(change){
                    var classesval = $("input[name="+classes+"]").val();
                    classesval = parseInt(classesval) -1;
                    $("input[name="+classes+"]").val(classesval);

                    var changeval = $("input[name="+change+"]").val();
                    changeval = parseInt(changeval) +1;
                    $("input[name="+change+"]").val(changeval);
                }
			} else {
                var classes = $(this).parent().prev().prev().html();
                var change = $(this).parent().parent().next().children().html();
				row.insertAfter(row.next());
                if(change){
                    var classesval = $("input[name="+classes+"]").val();
                    classesval = parseInt(classesval) +1;
                    $("input[name="+classes+"]").val(classesval);

                    var changeval = $("input[name="+change+"]").val();
                    changeval = parseInt(changeval) -1;
                    $("input[name="+change+"]").val(changeval);
                }
			}
		});
		
		function readURL(input) {
		  if (input.files && input.files[0]) {               
				    var file_data = $('#upload').prop('files')[0];   //取得上傳檔案屬性
					var form_data = new FormData();  //建構new FormData()
					form_data.append('file2', file_data);  //吧物件加到file後面
					form_data.append('yerly',$('#year2').val());
					
					$.ajax({
								url: '/admin/classes/classimport',
								headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
								cache: false,
								contentType: false,
								processData: false,
								data: form_data,     //data只能指定單一物件                 
								type: 'post',
							   success: function(data){
									alert(data);
								}
					 });
		}	};

		$("#upload").change(function() {
		  readURL(this);
		});
	});
	function checkrank(){
        $("#form2").submit();
    }	
    function starCross(){
        var star = $("#starCross").val();
        if(star =='1'){
            $("#starCross").val(0);
            $("#closeCrossedit").css('display','none');
            $("#starCrossedit").css('display','flex');
        }else{
            $("#starCross").val(1);
            $("#closeCrossedit").css('display','flex');
            $("#starCrossedit").css('display','none');
        }
    }
    function showCrossArea(){
        

        var crossRadio =$("input[name='crossRadio']:checked").val();
        $.ajax({
           	url:"/admin/classes/showCrossArea",  
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            type: 'get',
            data: {
                organName: $('#organ_name').val(),
                organCode: $('#organ_code').val(),
                crossRadio:crossRadio
            },
            success: function(data){
                console.log();
                let dataArr1 = JSON.parse(data);
                let dataArr = dataArr1[0];
                let tempHTML = "";
                for(let i=0; i<dataArr.length; i++) 
                {
                    tempHTML += "<tr>\
                        <td class='text-center' id='"+dataArr[i].enrollorg+"' onclick=tdclick('"+dataArr[i].enrollorg+"');>"+dataArr[i].crossarea+"</td>\
                        <td class='text-center'>"+dataArr[i].enrollorg+"</td>\
                        <td class='text-center'>"+dataArr[i].enrollname+"</td>\
                    </tr>";
                    
                };
                $("#crosslist").html(tempHTML);					
            },
            error: function(data) {
                console.log('Ajax Error'.data);
            }
        });
    }

          function tdclick(enrollorg) {
            if($("#starCross").val()==0){
                alert('點選修改，才能進行修改');
                return false;
            }
            $.ajax({
                url: '/admin/classes/crossOrg',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { 
                    enrollorg: enrollorg,
                },                     
                type: 'post',
                success: function(data){
                    $("#"+enrollorg).text(data);
                }
            });
          }
           
		  function BatchDelSelected()
		  {
			     $('#BatchModal').modal('show');
		  };
		  
		   function ClassIO()
		  {
			     $('#ClassIOModal').modal('show');
		  };
		  
		  function ClassImport()
		  {
			   if($('#year2').val()=='')
			   {
				   alert('請輸入年度 !!');
				   return ;
			   };
			  $("#upload").click();
		  };
		  
		  function ClassOutput()
		  { 
		      if($('#year2').val()=='')
			   {
				   alert('請輸入年度 !!');
				   return ;
			   };
			   var form_data = new FormData();  //建構new FormData()
			   form_data.append('yerly',$('#year2').val());
							
				$.ajax({
					url: '/admin/classes/classoutput',
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,     //data只能指定單一物件                 
					type: 'post',
				   success: function(data){
						alert(data);
					}
				});
		  };
		//排序  
	    function ChangeSort()
	    {
		    $('#SortModal').modal('show');
	    };
		  

        // 維護課程簡介
        function maintainClassIntro() {
            $('.classIntro').modal('show');
        }
        
        // 維護追蹤培訓班別
        function maintainTrainClass() {
            $('.trainClass').modal('show');
        }
        
        // 維護跨區報名機關
        function maintainCrossArea() {
            $('.crossArea').modal('show');
        }

        // 維護跨區報名機關
        // function maintainSortData() {
        //     $('.sortdata').modal('show');
        // }
</script>
@endsection