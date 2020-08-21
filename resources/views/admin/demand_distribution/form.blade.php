@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_distribution';?>
    <?php $institutionList = $base->getDBList('M17tb', ['enrollorg', 'enrollname']);?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求分配表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_distribution" class="text-info">需求分配列表</a></li>
                        <li class="active">需求分配維護(1)</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_distribution/'.$data->class, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_distribution/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">需求分配處理</h3></div>
                    <div class="card-body pt-4">



                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">班號</label>
                            <label class="col-md-9 col-form-label text-md-left">{{ $data->class }}</label>
        
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">班別名稱</label>
                            <label class="col-md-9 col-form-label text-md-left">{{ $data->name }}</label>
     
                        </div>

                        <!-- 總需求人數 -->
                        {{-- <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">總需求人數</label>
                            <div class="col-sm-10">


                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="quotatot" name="quotatot" placeholder="請輸入總需求人數" value="{{ old('quotatot', (isset($data->quotatot))? $data->quotatot : 0) }}" min="0" autocomplete="off" maxlength="5" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">

                                    <!-- 加 -->
                                    <span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div> 
                        
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">分配</label>
                            <div class="col-sm-10">

                                <Button type="button" class="btn btn-warning btn-sm mb-3" data-toggle="modal" data-target="#exampleModall"><i class="fa fa-plus fa-lg pr-2"></i>調整總分配人數</Button>
                                 <button type="button" class="btn btn-warning btn-sm mb-3" onclick="allocation();"><i class="fa fa-plus fa-lg pr-2"></i>調整總分配人數</button> 
							     <button type="button" class="btn btn-warning btn-sm mb-3" onclick="allocation();"><i class="fa fa-plus fa-lg pr-2"></i>執行分配</button>
                                 <button type="button" class="btn btn-primary btn-sm mb-3" onclick="add();"><i class="fa fa-plus fa-lg pr-2"></i>新增</button> 
                            </div>
                        </div>
                        --}}
                        <!-- 分配 -->
                       
                        <div class="form-group row">

                            <div class="col-sm-12 offset-sm-2">

                                <div class="table-responsive input-max">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                        <tr>
                                            <th>機關代碼</th>
                                            <th>機關名稱</th>
                                            <th>需求人數</th>
                                            <th>已分配人數</th>
                                            <!-- <th class="text-center">刪除</th> -->
                                        </tr>
                                        </thead>
                                        <tbody id="data_content">
                                            

                                            <tr>
                                            
                                                <td>合計</td>
                                                <td>總分配人數：{{$data->quotatot}}</td>
                                                <!-- <td>總分配人數：<input type="text" id="totalPeople" class="form-control" name="total_applycnt" value="{{$data->quotatot}}" readonly></td> -->
                                                <td><input type="text" class="form-control" id="total_applycnt2" name="total_applycnt2" readonly></td>
                                                <td><input type="text" class="form-control" id="total_checkcnt" name="total_checkcnt" readonly></td>
                                                <!-- <td class="text-center"></td> -->
                                            </tr>


                                            @foreach($list2 as $demandQty)
                                                <tr>
                                                   
                                                        <td>            
                                                            {{$demandQty->organ}}
                                                        </td>
                                                  
                                                        <td>
                                                            {{$demandQty->names}}
                                                        </td>                                                  
                                                    
                                                    <td>
                                                       
                                                       <input id="need{{$loop->index+1}}" type="text" class="form-control applycnt" maxlength="5" autocomplete="off" name="applycnt[]"  value="{{ $demandQty->demand or 1 }}" required onchange="countTotal()" readonly>
                                                    </td>
                                                    <td>
                                                        <input id="have{{$loop->index+1}}" type="text" class="form-control checkcnt" maxlength="5" autocomplete="off" name="checkcnt[{{ $demandQty->organ }}]"  value="{{ $demandQty->quota or 0 }}" required onchange="countTotal()">
                                                    </td>
                                                    <!-- <td class="text-center">
                                                        <i class="fa fa-trash text-danger pointer" data-toggle="modal" data-target="#del_modol" onclick="delElement=this"></i>
                                                    </td> -->
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" tabIndex="0">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/demand_distribution">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>


    <!-- Modeal1 調整總分配人數 -->
    <form method="POST" action="/admin/demand_distribution/importdata" enctype="multipart/form-data" id="form1" name="form1">
        {{ csrf_field() }}
        <!-- Modal1 調整總分配人數 -->
        <div class="modal fade" id="exampleModall" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">調整總分配人數</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label>總分配人數</label>
                        <input type="text" id="changeTotalPeople" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="total();">確定</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </form>




    <!-- 模組 -->
    <table style="display:none">
        <tbody id="model">
            <tr>
                
                <td width="60%">
                    <select name="organ[]" class="autoselect2 form-control organ" required>
                        <option value="">請選擇</option>
                        @foreach($list2 as $va)
                            <option value="{{ $va->class }}">{{ $va->class }}</option>
                        @endforeach
                    </select>
                </td>
                <td width="60%">
                    <select name="organ[]" class="autoselect2 form-control organ" required>
                        <option value="">請選擇</option>
                        @foreach($list2 as $va)
                            <option value="{{ $va->names }}">{{ $va->names }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control applycnt" maxlength="5" autocomplete="off" name="applycnt[]"  value="0" required onchange="countTotal()">
                </td>
                <td>
                    <input type="text" class="form-control checkcnt" maxlength="5" autocomplete="off" name="checkcnt[]"  value="0" required onchange="countTotal()">
                </td>
                <td class="text-center">
                    <i class="fa fa-trash text-danger pointer" data-toggle="modal" data-target="#del_modol" onclick="delElement=this"></i>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 刪除確認視窗 -->
    <div id="del_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content p-0 b-0">
                <div class="card mb-0">
                    <div class="card-header bg-danger">
                        <h3 class="card-title float-left text-white">警告</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="text-white">&times;</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">你確定要刪除嗎？</p>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn mr-3 btn-danger" data-dismiss="modal" onclick="$(delElement).parents('tr').remove()">確定</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')
    <script>


        var delElement;
        // 新增一行
        function add() {
            var model = $('#model').html();
            $('#data_content').append(model);
            $('#data_content').find('.autoselect2').last().select2();
            $( ".card-footer" ).focus();

        }

        // 選擇機關,避免相同機關重複設定
        $('#data_content').on('change', '.organ',function(){

            var value = $(this).val();

            if (value) {

                if($('option:selected[value='+value+']').length > 1) {
                    swal('相同機關不可以重複選取');

                    $(this).val('').trigger("change");
                }
            }
        });

        // 計算總需求人數
        function countTotal() {

            var qtyRequireTotal = 0;
            var qtyQuotaTotal = 0;
            $.each($('#data_content').find('.applycnt'), function( key, va ) {

                if ($(va).val() == '') {
                    $(va).val(0)
                }
                qtyRequireTotal += parseInt($(va).val());
            });

            $.each($('#data_content').find('.checkcnt'), function( key, va ) {

                if ($(va).val() == '') {
                    $(va).val(0)
                }
                qtyQuotaTotal += parseInt($(va).val());
            });


            if(qtyQuotaTotal>{{$data->quotatot}}){
                // alert('已分配人數不得大於總分配人數');
                // location.reload();
            }else{
                $('#total_applycnt1').val(qtyRequireTotal);
                $('#total_applycnt2').val(qtyRequireTotal);
                $('#total_checkcnt').val(qtyQuotaTotal);
            }


       //更新前先計算已分配人數加總是否大於總分配人數
    //    if(){
    //         return back()->with('result', '0')->with('message', '已分配人數不得大於總分配人數!');
    //     }
        

        }

        // 初始化
        countTotal();


        //調整總分配人數

        function total(){
            
            //get text value
            let changeTotalPeople = document.getElementById('changeTotalPeople').value;
            console.log(changeTotalPeople);

            //Change totalPeople
            console.log(document.getElementById('totalPeople').value);
            document.getElementById('totalPeople').value = changeTotalPeople;
        }
		
		// 自動分配
        function allocation() {

            var insert = 1;

            $.each( $('.institution'), function( key, va ) {

                // 取得機關總數
                var organTotal = parseInt($(va).find('.organ_total').html());
                // 取得平均
                var average = parseInt(organTotal / row);
                // 已經計算的數量
                var organNowTotal = 0;
                // 將平均值寫入input
                $.each( $(va).find('input'), function( key, vb ) {

                    $(vb).val(average)

                    organNowTotal += average;
                });

                // 如果還有餘數
                if (organTotal > organNowTotal) {

                    for (i=organNowTotal; i<organTotal; i++) {

                        // 取得輪到誰+1
                        input = $(va).find('.term'+insert);
                        // 欄位值+1
                        $(input).val(parseInt($(input).val()) + 1);
                        // 換下一個欄位
                        insert = (insert + 1 >  row)? 1 : insert + 1;
                    }
                }

            });

            // 計算總數
            countTotal();
			
			alert('完成 , 要存入資料庫  請按下 [ 儲存 ] 鍵!!');
        }
    </script>

@endsection