@inject('base', 'App\Presenters\BasePresenter')
@extends('admin.layouts.layouts')
@section('content')
<style>
    .col-form-label{
        padding-left:20px;
    }
</style>
    <?php $_menu = 'signup';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">線上報名設定</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">線上報名設定列表</li>
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
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>線上報名設定</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-12" style="margin-bottom:10px;">
                                        <a href="#" id="c2" style="color: inherit;
                                        @if($t04tb->t01tb->process == 1)
                                        color:blue;
                                        @endif
                                        " onclick="changeClass(1)">非委訓班</a>
                                        <a href="#" id="c1" style="color: inherit;
                                        @if($t04tb->t01tb->process == 2)
                                        color:blue;
                                        @endif                                        
                                        " onclick="changeClass(2)">委訓班</a>   
                                    </div>  
                                    <div style="border: 1px solid #000; padding: 10px;margin-bottom:10px; ">
                                        訓練班別：{{ $t04tb->t01tb->name }}<br>
                                        期別：{{ $t04tb->term }}<br>
                                        分班名稱：<br>
                                        班別類型：{{ $t04tb->t01tb->s01tb->name }}<br>
                                        委訓機關：{{ $t04tb->client }}<br>
                                        起訖期間：{{ $t04tb->sdateformat." ~ ".$t04tb->edateformat }}<br>
                                        班務人員：
                                    </div>    
                                    @if($t04tb->t01tb->process == 2)
                                        <!-- 委訓班 -->
                                        {!! Form::open([ 'method'=>'put', 'url'=>"/admin/signup/process2/{$t04tb->class}/{$t04tb->term}", 'id'=>'form']) !!}
                                        <div>
                                            <div class="form-group row">
                                                <label class="col-form-label">報名代碼</label>
                                                <div class="col-md-2">
                                                    <input type="text" name="apply_code" class="form-control" value="{{ $t04tb->apply_code }}">
                                                </div>
                                                <label class="col-form-label">報名密碼</label>
                                                <div class="col-md-2">
                                                    <input type="text" name="apply_password" class="form-control" value="{{ $t04tb->apply_password }}">
                                                </div>                                                
                                            </div>          

                                            <div class="form-group row">
                                                <label class="col-form-label">報名身份</label>
                                                <div class="col-md-10">
                                                    <div class="form-check-inline">
                                                        <label class="col-form-label form-check-label">
                                                            <input type="radio" class="form-check-input" name="apply_limit" value="1" onchange="selectApplyLimit(this.value)"
                                                            @if($t04tb->apply_limit == 1)
                                                                checked
                                                            @endif
                                                            >僅公務人員
                                                        </label>
                                                    </div>
                                                    <div class="form-check-inline">
                                                        <label class="col-form-label form-check-label">
                                                            <input type="radio" class="form-check-input" name="apply_limit" value="2" onchange="selectApplyLimit(this.value)"
                                                            @if($t04tb->apply_limit == 2)
                                                                checked
                                                            @endif                                                            
                                                            >僅一般民眾
                                                        </label>
                                                    </div>
                                                    <div class="form-check-inline">
                                                        <label class="col-form-label form-check-label">
                                                            <input type="radio" class="form-check-input" name="apply_limit" value="3" onchange="selectApplyLimit(this.value)"
                                                            @if($t04tb->apply_limit == 3)
                                                                checked
                                                            @endif                                                            
                                                            >不限
                                                        </label>
                                                    </div>                                                    
                                                </div>                                            
                                            </div>  
                                            <div class="form-group row" style="font-size: 16px;">
                                                <label class="col-form-label">
                                                    報名開始時間
                                                </label>    
                                                <div class="input-group col-md-3">                            
                                                    <input type="text" id="sdate" name="pubsdate" class="form-control input-max" autocomplete="off" value="{{ old('train_start_date', (isset($t04tb)) ? $t04tb->pubsdate : '') }}">
                                                    <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <label class="col-form-label">
                                                    報名結束時間
                                                </label>
                                                <div class="input-group col-md-3"> 
                                                    <input type="text" id="edate" name="pubedate" class="form-control input-max" autocomplete="off" value="{{ old('train_end_date', (isset($t04tb)) ? $t04tb->pubedate : '') }}" >
                                                    <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div> 
                                                                                                  
                                            <div class="form-group row">
                                                <label class="col-form-label col-md-12">名額分配方式</label>                                              
                                            </div> 
                                            <div class="form-group row">
                                                <div class="form-check col-md-12" style="padding-left:40px;">
                                                    <label class="col-form-label form-check-label">
                                                        <input type="radio" class="form-check-input" name="assign_type" value="1"
                                                        @if(old('assign_type', $t04tb->assign_type) == 1)
                                                            checked
                                                        @endif                                                         
                                                        
                                                        >依總名額
                                                    </label>
                                                </div>
                                                <div class="form-group row" style="padding-left:50px;" >
                                                    <label class="col-form-label">正取名額</label>
                                                    <div class="col-md-2">
                                                        <input type="text" name="officially_enroll" value="{{ old('officially_enroll', $t04tb->officially_enroll) }}" class="form-control">
                                                    </div> 
                                                    <label class="col-form-label">候補名額</label>
                                                    <div class="col-md-2">
                                                        <input type="text" name="secondary_enroll" value="{{ old('secondary_enroll', $t04tb->secondary_enroll) }}" class="form-control">
                                                    </div> 
                                                </div>                                                                                                                                             
                                            </div>  
                                            <div class="form-group row">
                                                <div class="form-check" style="padding-left:40px;">
                                                    <label class="col-form-label form-check-label">
                                                        <input type="radio" class="form-check-input" name="assign_type" value="2"
                                                        @if(old('assign_type', $t04tb->assign_type) == 2)
                                                            checked
                                                        @endif 
                                                        >依機關分配
                                                    </label>
                                                </div>                                             
                                            </div> 
                                            
                                            <div class="form-group row">
                                                <div class="col-md-5" style="padding-left:60px;">分配人數</div>
                                                <div class="col-md-5 text-md-right">
                                                    <a href="/admin/signup_organ/create/{{ $t04tb->class }}/{{ $t04tb->term }}">
                                                        <button type="button" class="btn btn-primary">新增機關</button>
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                <div class="col-md-10" style="padding-left:60px;">
                                                    <table class="table table-bordered mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>功能</th>
                                                                <th>機關代碼</th>
                                                                <th>機關名稱</th>
                                                                <th>正取名額</th>
                                                                <th>候補名額</th>
                                                                <th>開放所屬報名</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($online_apply_organs as $online_apply_organ)
                                                            <tr>
                                                                <th>
                                                                    <a href="/admin/signup_organ/edit/{{$online_apply_organ->id}}">
                                                                        <button type="button" class="btn btn-primary">編輯</button>
                                                                    </a>
                                                                </th>
                                                                <th>{{ $online_apply_organ->enrollorg }}</th>
                                                                <th>
                                                                    @if(!empty($online_apply_organ->m17tb))
                                                                    {{ $online_apply_organ->m17tb->enrollname }}
                                                                    @endif
                                                                </th>
                                                                <th>{{ $online_apply_organ->officially_enroll }}</th>
                                                                <th>{{ $online_apply_organ->secondary_enroll }}</th>
                                                                <th>
                                                                    @if(empty($online_apply_organ->open_belong_apply))
                                                                        否
                                                                    @else
                                                                        是
                                                                    @endif
                                                                </th>
                                                            </tr>
                                                            @endforeach                                                                                                                                                                                 
                                                        </tbody>
                                                    </table>
                                                </div>   
                                            </div>                                            
                                        </div>
                                        <!-- 委訓班 -->
                                    @else
                                        <!-- 非委訓班 -->
                                        {!! Form::open([ 'method'=>'put', 'url'=>'/admin/signup', 'id'=>'form']) !!}

                                            <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                            <input type="hidden" name="term" value="{{ $queryData['term'] }}">
                                        
                                            @if($dateData)
                                                <div class="">
                                                    <hr class="bg-secondary">
                                                    <div class="form-group row">
                                                        <label class="col-md-2 col-form-label text-md-right">報名代碼</label>
                                                        <div class="col-md-2">
                                                            <input type="text" name="apply_code" class="form-control" value="{{ old('apply_code', $dateData->apply_code) }}">
                                                        </div>
                                                        <label class="col-form-label">報名密碼</label>
                                                        <div class="col-md-2">
                                                            <input type="text" name="apply_password" class="form-control" value="{{ old('apply_code', $dateData->apply_password) }}">
                                                        </div>                                                
                                                    </div>  
                                                    <!-- 參加聯合派訓 -->
                                                    <div class="form-group row">
                                                        <label class="col-md-2 col-form-label text-md-right">參加聯合派訓</label>
                                                        <div class="col-md-10">
                                                            <select id="notice" name="notice" class="select2 form-control select2-single input-max">
                                                                <option value="Y" {{ $dateData->notice == 'Y'? 'selected' : '' }}>是</option>
                                                                <option value="N" {{ $dateData->notice == 'N'? 'selected' : '' }}>否</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- 報名開始日期 -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label text-md-right pt-2">報名開始日期<span class="text-danger">*</span></label>

                                                        <div class="input-group col-md-3">                            
                                                            <input type="text" id="sdate" name="pubsdate" class="form-control input-max" autocomplete="off" value="{{ old('train_start_date', (isset($t04tb)) ? $t04tb->pubsdate : '') }}">
                                                            <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="datepicker1"><i class="fa fa-calendar"></i></span>
                                                        </div>                                                        
                                                        <!-- <div class="col-sm-10">

                                                            <div class="input-group roc-date input-max">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">民國</span>
                                                                </div>

                                                                <input type="text" class="form-control roc-date-year" maxlength="3" name="pubsdate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($dateData->pubsdate))? mb_substr($dateData->pubsdate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">年</span>
                                                                </div>

                                                                <input type="text" class="form-control roc-sdate-month" maxlength="2" name="pubsdate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($dateData->pubsdate))? mb_substr($dateData->pubsdate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">月</span>
                                                                </div>

                                                                <input type="text" class="form-control roc-sdate-day" maxlength="2" name="pubsdate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($dateData->pubsdate))? mb_substr($dateData->pubsdate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">日</span>
                                                                </div>
                                                            </div>

                                                        </div> -->
                                                    </div>

                                                    <!-- 報名結束日期 -->
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 control-label text-md-right pt-2">報名結束日期<span class="text-danger">*</span></label>
                                                        <div class="input-group col-md-3"> 
                                                            <input type="text" id="edate" name="pubedate" class="form-control input-max" autocomplete="off" value="{{ old('train_end_date', (isset($t04tb)) ? $t04tb->pubedate : '') }}" >
                                                            <span class="input-group-addon" style="cursor: pointer;height:calc(2.25rem + 2px);" id="datepicker2"><i class="fa fa-calendar"></i></span>
                                                        </div>                                                        
                                                        <!-- <div class="col-sm-10">

                                                            <div class="input-group roc-date input-max">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">民國</span>
                                                                </div>

                                                                <input type="text" class="form-control roc-date-year" maxlength="3" name="pubedate[year]" placeholder="請輸入年份" autocomplete="off" value="{{ (isset($dateData->pubedate))? mb_substr($dateData->pubedate, 0, 3) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">年</span>
                                                                </div>

                                                                <input type="text" class="form-control roc-edate-month" maxlength="2" name="pubedate[month]" placeholder="請輸入月份" autocomplete="off" value="{{ (isset($dateData->pubedate))? mb_substr($dateData->pubedate, 3, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">月</span>
                                                                </div>

                                                                <input type="text" class="form-control roc-edate-day" maxlength="2" name="pubedate[day]" placeholder="請輸入日期" autocomplete="off" value="{{ (isset($dateData->pubedate))? mb_substr($dateData->pubedate, 5, 2) : '' }}" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required>

                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">日</span>
                                                                </div>
                                                            </div>

                                                        </div> -->
                                                    </div>

                                                </div>
                                            @endif

                                            @if($data && $t04tb->t01tb->signin != 3)
                                            <div class="table-responsive">
                                                <table class="table table-bordered mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>機關代碼</th>
                                                        <th>機關名稱</th>
                                                        <th>年度分配人數</th>
                                                        <th>線上分配人數</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-left"></td>
                                                            <td>合計</td>
                                                            <td>{{ $data['sum']['年度分配人數'] }}</td>
                                                            <td>{{ $data['sum']['線上分配人數'] }}</td>
                                                        </tr>

                                                    @foreach($data['data'] as $va)
                                                        <tr>

                                                            <td class="text-left">{{ $va->機關代碼 }}</td>
                                                            <td>{{ $va->機關名稱 }}</td>
                                                            <td>{{ $va->年度分配人數 }}</td>
                                                            <td><input type="text" name="value[{{ $va->機關代碼 }}]" value="{{ $va->線上分配人數 }}"></td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        
                                            <!-- 非委訓班 -->                                       
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">                            
                            <button type="submit" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                            <a href="/admin/signup">
                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply pr-2"></i>取消</button>
                            </a>
                        </div>
                        {!! Form::close() !!}
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
        $(document).ready(function() {

            $("#sdate").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });

            $('#datepicker1').click(function(){
                $("#sdate").focus();
            });

            $("#edate").datepicker({
                format: "twymmdd",
                language: 'zh-TW'
            });

            $('#datepicker2').click(function(){
                $("#edate").focus();
            });

        });


        // 取得期別
        function classChange()
        {
            $.ajax({
                type: "post",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url: '/admin/signup/getterm',
                data: { classes: $('#class').val(), selected: '{{ $queryData['term'] }}'},
                success: function(data){
                    $('#term').html(data);
                    $("#term").trigger("change");
                },
                error: function() {
                    alert('Ajax Error');
                }
            });
        }

        function selectApplyLimit(apply_limit)
        {
            if (apply_limit == 1){
                $('input[name=assign_type][value=2]').attr('disabled', false);
            }else{
                $('input[name=assign_type][value=1]').prop('checked', true);
                $('input[name=assign_type][value=2]').attr('disabled', true);
            }
        }
        // 初始化
        // classChange();
    </script>

@endsection