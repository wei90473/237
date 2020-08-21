@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')
    <style>
        .size-5{
            width:50px;
        }
    </style>
    <?php $_menu = 'periods';?>
    <?php $institutionList = $base->getDBList('M13tb', ['organ', 'lname']);?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">開班期數處理表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/periods" class="text-info">開班期數處理列表</a></li>
                        <li class="active">開班期數處理表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->

            {!! Form::open([ 'method'=>'put', 'url'=>'/admin/periods/'.$t01tb->class, 'id'=>'form']) !!}

            <div class="col-md-12 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">開班期數處理表單</h3></div>
                    <div class="card-body pt-4">


                        <!-- 班別 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班別</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-max" value="{{ $t01tb->class }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班別名稱</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-max" value="{{ $t01tb->name }}" disabled>
                            </div>
                        </div>
                        <!-- 舊分配功能
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">分配</label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-warning btn-sm mb-3" onclick="allocation();"><i class="fa fa-plus fa-lg pr-2"></i>重新分配</button>
                                <button type="button" class="btn btn-primary btn-sm mb-3" onclick="add();"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                            </div>
                        </div>
                        -->

                        <!-- 分配 -->

                        <div class="form-group row">

                            <div class="col-sm-12">

                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr id="title">
                                                <th>機關代碼</th>
                                                <th>機關名稱</th>
                                                <th>分配人數</th>
                                                @foreach($t03tbs as $term => $quotas)
                                                    <th>{{ '第 '.$term.' 期' }}</th>
                                                @endforeach
                                            </tr>
                                            <tr id="total">
                                                <th></th>
                                                <th>各期合計</th>
                                                <th>{{ $t01tb->t02tbs->sum('quota') }}</th>
                                                @foreach($t03tbs as $term => $quotas)
                                                    <th>{{ $quotas->sum() }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>                                      
                                            @foreach($t02tbs as $t02tb)
                                                <tr>
                                                    <td>{{ $t02tb->organ }}</td>
                                                    <td>{{ $t02tb->m13tb->lname }}</td>
                                                    <td>{{ $t02tb->quota }}</td>
                                                    @foreach($t03tbs as $term => $quotas)
                                                    <td>
                                                        <input type="text" class="form-control" name="quotas[{{ $t02tb->organ }}][{{ $term }}]" value="{{ old("quotas.{$t02tb->organ}.{$term}", isset($quotas[$t02tb->organ]) ? $quotas[$t02tb->organ] : 0) }}"
                                                        {{ ($online_updated_t03tbs[$term]) ? 'disabled' : '' }}
                                                        >
                                                    </td>
                                                    @endforeach                                                    
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/periods">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

@endsection

@section('js')
    <script>

        // 新增一期
        function add() {

            row ++;

            $('#title').append('<th>'+row+'</th>');

            $('#total').append('<th id="quota_total'+row+'">0</th>');

            $.each( $('.institution'), function( key, va ) {

                var enrollorg = $(va).data('enrollorg');

                html = '<td><input onchange="countTotal()" class="term'+row+' organ'+enrollorg+'" name="quota['+row+']['+enrollorg+']" value="0" required onkeyup="this.value=this.value.replace(/[^\\d]/g,\'\')"></td>';

                $(va).append(html)
            });
        }

        // 計算總數
        function countTotal() {

            for (i=1;i<=row;i++) {

                var total = 0;

                $.each( $('.term'+i), function( key, va ) {

                    total += parseInt($(va).val());
                });

                $('#quota_total'+i).html(total);
            }
        }

        // 初始化
        countTotal();

        // 送出時檢查分配人數
        function verification(result) {
            return true;
            $.each( $('.organ_total'), function( key, va ) {

                // 機關人數上限
                maxTotal = $(va).data('total');
                // 機關代號
                organ = $(va).data('organ');

                total = 0;

                $.each( $('.organ'+organ), function( key, vb ) {
                    total += parseInt($(vb).val());
                });

                // 超過上限
                if (total > maxTotal) {

                    institutionName = $(va).parents('tr').find('th').first().html();


                    swal(institutionName + '分配' + total + '人，超過總配分人數' + maxTotal + '人！');

                    result = false;
                }

                // 低於
                if (total < maxTotal) {

                    institutionName = $(va).parents('tr').find('th').first().html();


                    swal(institutionName + '總分配人數' + maxTotal +'人，已分配' + total + '人，' + (maxTotal - total) + '人未分配！');

                    result = false;
                }
            });

            return result;
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
                // console.log("organTotal : " + organTotal);
                // console.log("organNowTotal : " + organNowTotal);
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