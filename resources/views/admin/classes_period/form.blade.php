@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'demand_distribution';?>
    <?php $typeList = $base->getDBList('Institution', ['institution_id', 'code', 'name_full']);?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">需求分配表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/demand_distribution" class="text-info">需求分配列表</a></li>
                        <li class="active">需求分配表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/demand_distribution/'.$data->demand_distribution_id, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/demand_distribution/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">需求分配表單</h3></div>
                    <div class="card-body pt-4">



                        <!-- 班號 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班號</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-max" value="{{ $data->classes_number }} {{ $data->classes_name }}" disabled>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">分配</label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-primary btn-sm mb-3" onclick="add();"><i class="fa fa-plus fa-lg pr-2"></i>新增</button>
                            </div>
                        </div>

                        <!-- 分配 -->

                        <div class="form-group row">

                            <div class="col-sm-10 offset-sm-2">

                                <div class="table-responsive input-max">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                        <tr>
                                            <th>機關名稱</th>
                                            <th>需求人數</th>
                                            <th>已分配人數</th>
                                            <th class="text-center">刪除</th>
                                        </tr>
                                        </thead>
                                        <tbody id="data_content">


                                            <tr>
                                                <td width="60%">合計</td>
                                                <td><input type="text" class="form-control" id="total_qty_require" name="total_qty_require" readonly></td>
                                                <td><input type="text" class="form-control" id="total_qty_quota" name="total_qty_quota" readonly></td>
                                                <td class="text-center"></td>
                                            </tr>

                                            @foreach($data->demand_qty as $demandQty)
                                                <tr>
                                                    <td width="60%">
                                                        <select name="institution_id[]" class="select2 form-control institution_id" required>
                                                            <option value="">請選擇</option>
                                                            @foreach($typeList as $va)
                                                                <option value="{{ $va->institution_id }}" {{ ((isset($demandQty->institution_id))? $demandQty->institution_id : NULL) == $va->institution_id? 'selected' : '' }}>{{ $va->code }} {{ $va->name_full }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control qty_require" maxlength="5" autocomplete="off" name="qty_require[]"  value="{{ $demandQty->qty_require or 1 }}" required onchange="countTotal()">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control qty_quota" maxlength="5" autocomplete="off" name="qty_quota[]"  value="{{ $demandQty->qty_quota or 0 }}" required onchange="countTotal()">
                                                    </td>
                                                    <td class="text-center">
                                                        <i class="fa fa-trash text-danger pointer" data-toggle="modal" data-target="#del_modol" onclick="delElement=this"></i>
                                                    </td>
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
                        <a href="/admin/demand_distribution">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 模組 -->
    <table style="display:none">
        <tbody id="model">
            <tr>
                <td width="60%">
                    <select name="institution_id[]" class="autoselect2 form-control institution_id" required>
                        <option value="">請選擇</option>
                        @foreach($typeList as $va)
                            <option value="{{ $va->institution_id }}" {{ old('county', (isset($data->institution_id))? $data->institution_id : 1) == $va->institution_id? 'selected' : '' }}>{{ $va->code }} {{ $va->name_full }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control qty_require" maxlength="5" autocomplete="off" name="qty_require[]"  value="0" required onchange="countTotal()">
                </td>
                <td>
                    <input type="text" class="form-control qty_quota" maxlength="5" autocomplete="off" name="qty_quota[]"  value="0" required onchange="countTotal()">
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

        }

        // 選擇機關,避免相同機關重複設定
        $('#data_content').on('change', '.institution_id',function(){

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

            $.each($('#data_content').find('.qty_require'), function( key, va ) {

                if ($(va).val() == '') {
                    $(va).val(0)
                }
                qtyRequireTotal += parseInt($(va).val());
            });

            $.each($('#data_content').find('.qty_quota'), function( key, va ) {

                if ($(va).val() == '') {
                    $(va).val(0)
                }
                qtyQuotaTotal += parseInt($(va).val());
            });

            $('#total_qty_require').val(qtyRequireTotal);
            $('#total_qty_quota').val(qtyQuotaTotal);
        }

        // 初始化
        countTotal();
    </script>

@endsection