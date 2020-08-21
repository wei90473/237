@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <style>
        /* .item_con {
            display: flex;
            align-items: center;
        } */
        .display_inline {
            display: inline-block;
            margin-right: 5px;
        }
        .halfArea {
            padding: 5px;
            border: 1px solid #d2d6de;
            border-radius: 5px;
        }
        .arrow_con {
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0px 5px;
        }
        .item_con label {
            cursor: pointer;
        }
        .item_check.active, .item_uncheck.active {
            background-color: #d2f1ff;
        }
        .arrow_rank {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .paginationjs{line-height:1.6;font-family:Marmelad,"Lucida Grande",Arial,"Hiragino Sans GB",Georgia,sans-serif;font-size:14px;box-sizing:initial}.paginationjs:after{display:table;content:" ";clear:both}.paginationjs .paginationjs-pages{float:left}.paginationjs .paginationjs-pages ul{float:left;margin:0;padding:0}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input,.paginationjs .paginationjs-nav{float:left;margin-left:10px;font-size:14px}.paginationjs .paginationjs-pages li{float:left;border:1px solid #aaa;border-right:none;list-style:none}.paginationjs .paginationjs-pages li>a{min-width:30px;height:28px;line-height:28px;display:block;background:#fff;font-size:14px;color:#333;text-decoration:none;text-align:center}.paginationjs .paginationjs-pages li>a:hover{background:#eee}.paginationjs .paginationjs-pages li.active{border:none}.paginationjs .paginationjs-pages li.active>a{height:30px;line-height:30px;background:#aaa;color:#fff}.paginationjs .paginationjs-pages li.disabled>a{opacity:.3}.paginationjs .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs .paginationjs-pages li:first-child,.paginationjs .paginationjs-pages li:first-child>a{border-radius:3px 0 0 3px}.paginationjs .paginationjs-pages li:last-child{border-right:1px solid #aaa;border-radius:0 3px 3px 0}.paginationjs .paginationjs-pages li:last-child>a{border-radius:0 3px 3px 0}.paginationjs .paginationjs-go-input>input[type=text]{width:30px;height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;padding:0;font-size:14px;text-align:center;vertical-align:baseline;outline:0;box-shadow:none;box-sizing:initial}.paginationjs .paginationjs-go-button>input[type=button]{min-width:40px;height:30px;line-height:28px;background:#fff;border-radius:3px;border:1px solid #aaa;text-align:center;padding:0 8px;font-size:14px;vertical-align:baseline;outline:0;box-shadow:none;color:#333;cursor:pointer;vertical-align:middle\9}.paginationjs.paginationjs-theme-blue .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-blue .paginationjs-pages li{border-color:#289de9}.paginationjs .paginationjs-go-button>input[type=button]:hover{background-color:#f8f8f8}.paginationjs .paginationjs-nav{height:30px;line-height:30px}.paginationjs .paginationjs-go-button,.paginationjs .paginationjs-go-input{margin-left:5px\9}.paginationjs.paginationjs-small{font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li>a{min-width:26px;height:24px;line-height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-pages li.active>a{height:26px;line-height:26px}.paginationjs.paginationjs-small .paginationjs-go-input{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-input>input[type=text]{width:26px;height:24px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button{font-size:12px}.paginationjs.paginationjs-small .paginationjs-go-button>input[type=button]{min-width:30px;height:26px;line-height:24px;padding:0 6px;font-size:12px}.paginationjs.paginationjs-small .paginationjs-nav{height:26px;line-height:26px;font-size:12px}.paginationjs.paginationjs-big{font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li>a{min-width:36px;height:34px;line-height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-pages li.active>a{height:36px;line-height:36px}.paginationjs.paginationjs-big .paginationjs-go-input{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{width:36px;height:34px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button{font-size:16px}.paginationjs.paginationjs-big .paginationjs-go-button>input[type=button]{min-width:50px;height:36px;line-height:34px;padding:0 12px;font-size:16px}.paginationjs.paginationjs-big .paginationjs-nav{height:36px;line-height:36px;font-size:16px}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a{color:#289de9}.paginationjs.paginationjs-theme-blue .paginationjs-pages li>a:hover{background:#e9f4fc}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.active>a{background:#289de9;color:#fff}.paginationjs.paginationjs-theme-blue .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]{background:#289de9;border-color:#289de9;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-green .paginationjs-pages li{border-color:#449d44}.paginationjs.paginationjs-theme-blue .paginationjs-go-button>input[type=button]:hover{background-color:#3ca5ea}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a{color:#449d44}.paginationjs.paginationjs-theme-green .paginationjs-pages li>a:hover{background:#ebf4eb}.paginationjs.paginationjs-theme-green .paginationjs-pages li.active>a{background:#449d44;color:#fff}.paginationjs.paginationjs-theme-green .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]{background:#449d44;border-color:#449d44;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-yellow .paginationjs-pages li{border-color:#ec971f}.paginationjs.paginationjs-theme-green .paginationjs-go-button>input[type=button]:hover{background-color:#55a555}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a{color:#ec971f}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li>a:hover{background:#fdf5e9}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.active>a{background:#ec971f;color:#fff}.paginationjs.paginationjs-theme-yellow .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]{background:#ec971f;border-color:#ec971f;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-input>input[type=text],.paginationjs.paginationjs-theme-red .paginationjs-pages li{border-color:#c9302c}.paginationjs.paginationjs-theme-yellow .paginationjs-go-button>input[type=button]:hover{background-color:#eea135}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a{color:#c9302c}.paginationjs.paginationjs-theme-red .paginationjs-pages li>a:hover{background:#faeaea}.paginationjs.paginationjs-theme-red .paginationjs-pages li.active>a{background:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-pages li.disabled>a:hover{background:0 0}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]{background:#c9302c;border-color:#c9302c;color:#fff}.paginationjs.paginationjs-theme-red .paginationjs-go-button>input[type=button]:hover{background-color:#ce4541}.paginationjs .paginationjs-pages li.paginationjs-next{border-right:1px solid #aaa\9}.paginationjs .paginationjs-go-input>input[type=text]{line-height:28px\9;vertical-align:middle\9}.paginationjs.paginationjs-big .paginationjs-pages li>a{line-height:36px\9}.paginationjs.paginationjs-big .paginationjs-go-input>input[type=text]{height:36px\9;line-height:36px\9}
    </style>
    <?php $_menu = 'classes';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">班別資料維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/classes" class="text-info">班別資料查詢</a></li>
                        <li class="active">班別資料維護</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/classes/'.$data->class, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/classes/', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">班別資料維護</h3></div>
                    <div class="card-body pt-4">
                        @if(!isset($data))
                        <!-- 班號年份 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                            <div class="col-md-2">
                                <input type="text" class="form-control input-max" id="class" name="class" placeholder="請輸入班別" value="{{ (date('Y')-1911).($base->getMaxClass()) }}" autocomplete="off" required maxlength="6" >
                            </div >
                            <!-- 院區代號 -->
                            <div class="col-md-1">
                                <input type="text" class="form-control input-max" id="branchcode" name="branchcode" value="{{ old('branch', (isset($data->branch))? (($data->branch ==1)?'A':'B') : 'A')  }}" autocomplete="off"  maxlength="1" readonly>
                            </div>
                            <label class="col-form-label text-md" >辦班院區<span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                <select id="branch" name="branch" class="browser-default custom-select" onchange="getbranchcode()">
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}"  {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <a href="#example1">
                                <button type="button" class="btn btn-primary mr-1" id="regist" >報名方式設定</button>
                            </a>
                            
                        </div>

                        <!--div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班號年份<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <!--span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <!--input type="text" class="form-control number-input-max" id="year" name="year" min="1" placeholder="請輸入班號年份" value="{{ old('year', (isset($data->number))? mb_substr($data->number, 0, 3) : date('Y')-1911 ) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required maxlength="3" {{ (isset($data))? 'disabled' : '' }}>

                                    <!-- 加 -->
                                    <!--span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 流水號 -->
                        <!--div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">流水號<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <!--span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span>

                                    <!-- 輸入欄位 -->
                                    <!--input type="text" class="form-control number-input-max" id="serial_number" name="serial_number" min="1" placeholder="請輸入班號年份" value="{{ old('serial_number', (isset($data->number))? mb_substr($data->number, 3, 3) : $base->getMaxClass() ) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required maxlength="3" onchange="locationChange();" {{ (isset($data))? 'disabled' : '' }}>
                                    
                                    <!-- 加 -->
                                    <!--span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span>
                                </div>
                            </div>
                            <label class="col-md-1 col-form-label text-md-right" style="min-width:100px;">辦班院區<span class="text-danger">*</span></label>
                            <div class="col-md-4">
                                <select id="branch" name="branch" class="browser-default custom-select">
                                    @foreach(config('app.branch') as $key => $va)
                                        <option value="{{ $key }}" {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div-->
                        @else
                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control input-max" id="class" name="class" value="{{ $data->class }}" readonly>
                                </div>
                                <!-- 院區代號 -->
                                <input type="hidden" name="branchcode" value="{{ old('branchcode', (isset($data->branchcode))?$data->branchcode  : '')  }}" readonly>
                                <label class="col-form-label text-md" >辦班院區<span class="text-danger">*</span></label>
                                <div class="col-md-3">
                                    <select id="branch" name="branch" class="browser-default custom-select" onchange="getbranchcode()" disabled>
                                        @foreach(config('app.branch') as $key => $va)
                                            <option value="{{ $key }}" {{ old('branch', (isset($data->branch))? $data->branch : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <a href="#example1">
                                    <button type="button" class="btn btn-primary mr-1" id="regist" >報名方式設定</button>
                                </a>
                            </div>
                        @endif

                        <!-- v班別名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">班別<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-max" id="name" name="name" placeholder="請輸入班別名稱(中文)" value="{{ old('name', (isset($data->name))? $data->name : '') }}" autocomplete="off" required maxlength="255">
                            </div>
                            <label class="control-label text-md-right pt-2">分班名稱</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control input-max" id="branchname" name="branchname" placeholder="請輸入分班名稱" value="" autocomplete="off" maxlength="255">
                            </div>
                        </div>

                        <!-- 流程名稱 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">流程名稱</label>
                            <div class="col-sm-3">
                                <?php $processList = $base->getDBList('Class_process')?>
                                <input type="hidden" name="base_class_process" value="{{ old('class_process', (isset($data->class_process))? $data->class_process : '' )}}">
                                <select id="class_process" name="class_process" class="browser-default custom-select">
                                    <!-- <option value="">不套用流程</option>
                                    @foreach($processList as $key => $va)
                                        <option value="{{ $key }}" {{ old('class_process', (isset($data->class_process))? $data->class_process : 0) == $key? 'selected' : '' }}>{{ $va['name'] }}</option>
                                    @endforeach -->
                                </select>
                            </div>
                            
                        </div>
                        <!-- v班別性質 -->
                        <?php $typeList = $base->getSystemCode('K')?>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">班別性質</label>
                            <div class="col-md-3">
                                <select id="type" name="type" class="browser-default custom-select">
                                    @foreach($typeList as $code => $va)
                                        @if($va['code'] != '13') 
                                        <option value="{{ $va['code'] }}" {{ old('type', (isset($data->type))? $data->type : 1) == $va['code']? 'selected' : '' }}>{{ $code }}{{ $va['name'] }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <!-- v上課方式 -->
                            <label class="col-form-label text-md-right">上課方式</label>
                            <div class="col-md-2">
                                <select id="style" name="style" class="browser-default custom-select" onchange="gettype()">
                                    @foreach(config('app.style') as $key => $va)
                                        <option value="{{ $key }}" {{ old('style', (isset($data->style))? $data->style : 1) == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                                  
                            </div>
                            <!-- 新增上課方式-->
                            <div class="form-group">
                                <button id="newtype_class" class="btn btn-number pt-1" onclick="chooseClassDay()" type="button">+</button>
                            </div>
                            <div class="" style="display: none">
                                <input type="checkbox" name="time1" value="Y" autocomplete="off" {{ old('time1', (isset($data->time1))? $data->time1 : '') == 'Y'? 'checked' : '' }}><label>週一</label> 
                                <input type="checkbox" name="time2" value="Y" autocomplete="off" {{ old('time2', (isset($data->time2))? $data->time2 : '') == 'Y'? 'checked' : '' }}><label>週二</label>
                                <input type="checkbox" name="time3" value="Y" autocomplete="off" {{ old('time3', (isset($data->time3))? $data->time3 : '') == 'Y'? 'checked' : '' }}><label>週三</label>
                                <input type="checkbox" name="time4" value="Y" autocomplete="off" {{ old('time4', (isset($data->time4))? $data->time4 : '') == 'Y'? 'checked' : '' }}><label>週四</label>
                                <input type="checkbox" name="time5" value="Y" autocomplete="off" {{ old('time5', (isset($data->time5))? $data->time5 : '') == 'Y'? 'checked' : '' }}><label>週五</label>
                                <input type="checkbox" name="time6" value="Y" autocomplete="off" {{ old('time6', (isset($data->time6))? $data->time6 : '') == 'Y'? 'checked' : '' }}><label>週六</label>
                                <input type="checkbox" name="time7" value="Y" autocomplete="off" {{ old('time7', (isset($data->time7))? $data->time7 : '') == 'Y'? 'checked' : '' }}><label>週日</label>
                                <input type="checkbox" name="holiday" value="Y" autocomplete="off" {{ old('holiday', (isset($data->holiday))? $data->holiday : '') == 'Y'? 'checked' : '' }}><label>含國定假日</label>
                            </div>
                            <div class="col-md-3">
                            <div class="form-group row">
                                    <input type="text" class="form-control input-max" id="newstyle" name="newstyle" placeholder="新增上課方式"  value="" autocomplete="off"  maxlength="255" readonly>
                                    </div>
                            </div>
                        </div>
                        <!-- v官等區分 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">官等區分</label>
                            <div class="col-md-3">
                                <select id="post" name="post" class="browser-default custom-select">
                                    @foreach(config('app.position_level') as $key => $va)
                                        <option value="{{ $key }}" {{ old('post', (isset($data->post))? $data->post : 7) == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- *班別類型 -->
                            <label class="col-form-label text-md-right">班別類型</label>
                            <div class="col-md-5">
                                <select id="process" name="process" class="browser-default custom-select" onchange="getprocess()">
                                    @foreach(config('app.process') as $key => $va)
                                        <option  value="{{ $key }}" {{ old('process', (isset($data->process))? $data->process : 1) == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- *委訓機關 -->
                        <div class="form-group row" style="display: none;" id ="clientclass">
                            <label class="col-md-2 col-form-label text-md-right">委訓機關代碼</label>
                            <div class="col-sm-3">
                                <!-- <input type="hidden" id="commission" name="commission" placeholder="請挑選委訓機關" value="" autocomplete="off"> -->
                                <input type="text" class="form-control input-max" id="commission" name="commission" placeholder="請挑選委訓機關" value="{{ old('commission', (isset($data->commission))? $data->commission : '') }}" autocomplete="off"  readonly>
                            </div>
                            <label class="col-form-label">機關名稱</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-max" id="enrollname" name="enrollname" placeholder="請挑選委訓機關" value="{{ old('enrollname', (isset($data->enrollname))? $data->enrollname : '') }}" autocomplete="off"  readonly>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="showOrgan()">挑選機關</button>
                        </div>
                        <!-- v訓練性質 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">訓練性質</label>
                            <div class="col-md-3">
                                <select id="traintype" name="traintype" class="browser-default custom-select">
                                    @foreach(config('app.traintype') as $key => $va)
                                        <option value="{{ $key }}" {{ old('traintype', (isset($data->traintype))? $data->traintype : 3) == $key? 'selected' : '' }}>{{ $key.$va }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- **類別1 -->
                            <label class="col-form-label text-md">類別1</label>
                            <div class="col-md-5">
                                <?php $categoryoneList = $base->getSystemCode('M')?>
                                <select id="categoryone" name="categoryone" class="browser-default custom-select">
                                <option value="" selected>請選擇</option>
                                @foreach($categoryoneList as $code => $va)
                                    <option value="{{ $va['code'] }}" {{ old('categoryone', (isset($data->categoryone))? $data->categoryone : '') == $va['code']? 'selected' : '' }}>{{ $va['name'] }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- 是否住宿 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right ">是否住宿</label>
                            <div class="col-md-10 pt-2">
                                <!-- <select id="board" name="board" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.board') as $key => $va)
                                        <option value="{{ $key }}" {{ old('board', (isset($data->board))? $data->board : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.board') as $key => $va)
                                    <input type="radio" id="board" name="board" value="{{ $key }}" {{ old('board', (isset($data->board))? $data->board : 'N') == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>

                        <!-- v每日上課時數 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">每日上課時數</label>
                            <div class="col-sm-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 減 -->
                                    <!--span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-down number-less" type="button">-</button>
                                    </span-->

                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="dayhour" name="dayhour" min="1" placeholder="請輸入每日上課時數" value="{{ old('dayhour', (isset($data->dayhour))? $data->dayhour : 6) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" maxlength="6" onchange="countTotalDays();">

                                    <!-- 加 -->
                                    <!--span class="input-group-btn">
                                        <button class="btn btn-number bootstrap-touchspin-up number-plus" type="button">+</button>
                                    </span-->
                                </div>
                            </div>
                            <!-- v訓期 -->
                            <label class="control-label text-md pt-2">訓期</label>
                            <div class="col-sm-2">
                                <div class="input-group bootstrap-touchspin number_box" >
                                    <input type="text" class="form-control number-input-max" id="period" name="period" min="1" placeholder="請輸入訓期" value="{{ old('period', (isset($data->period))? $data->period : 1) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d.]/g,'')" maxlength="255" onchange="kindChange();countTotalDays();">
                                </div>
                            </div>
                            <!-- v訓期單位 -->
                            <div class="col-md-3 pt-2" onchange="$('input[name=period]').val('1');countTotalDays();">
                                @foreach(config('app.kind') as $key => $va)
                                    @if($key=="1")
                                       <span style="display:inline" id="week"><input type="radio" id="kind" name="kind" value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'checked' : '' }}>{{ $va }}</span> 
                                    @else
                                       <input type="radio" id="kind" name="kind" value="{{ $key }}" {{ old('kind', (isset($data->kind))? $data->kind : 1) == $key? 'checked' : '' }}>{{ $va }}
                                    @endif 
                                  
                                @endforeach
                            </div>
                            <!-- v每期人數 -->
                            <label class="control-label text-md pt-2">每期人數</label>
                            <div class="col-sm-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="quota" name="quota" min="1" placeholder="請輸入每期人數" value="{{ old('quota', (isset($data->quota))? $data->quota : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>
                            </div>
                        </div>
                        <!-- 訓練總天數 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">訓練總天數</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control input-max" id="trainday" name="trainday" value="{{ old('trainday', (isset($data->trainday))? $data->trainday : NULL) }}" autocomplete="off" >
                            </div>
                            <!-- 學習性質 -->
                            <label class="col-form-label text-md">學習性質</label>
                            <div class="col-md-4 pt-2">
                                <!-- <select id="classified" name="classified" class="select2 form-control select2-single input-max" onchange="countTotalDays();">
                                    @foreach(config('app.classified') as $key => $va)
                                        <option value="{{ $key }}" {{ old('classified', (isset($data->classified))? $data->classified : 2) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.classified') as $key => $va)
                                    <input type="radio" id="classified" name="classified" onclick="getclassified({{ $key }});hoursChange()" value="{{ $key }}" {{ old('classified', (isset($data->classified))? $data->classified : 2) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>
                        <!-- 認證時數 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">認證時數</label>
                            <!-- v數位時數 -->
                            <label class="control-label text-md pt-2">數位時數</label>
                            <div class="col-sm-1">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="elearnhr" name="elearnhr" min="0" placeholder="請輸入數位時數" value="{{ old('elearnhr', (isset($data->elearnhr))? $data->elearnhr : '0') }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onchange="hoursChange();">
                                </div>
                            </div>
                            <!-- v實體時數 -->
                            <label class="control-label text-md pt-2">實體時數</label>
                            <div class="col-sm-2">
                                <div class="input-group bootstrap-touchspin number_box">
                                   <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="classhr" name="classhr" min="0" placeholder="請輸入實體時數" value="{{ old('classhr', (isset($data->classhr))? $data->classhr : '0') }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" onchange="hoursChange();">
                                </div>
                            </div>
                            <!-- v訓練總時數 -->
                            <label class="control-label text-md pt-2">訓練時數</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control number-input-max" id="trainhour" name="trainhour" value="{{ old('trainhour', (isset($data->trainhour))? $data->trainhour : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" readonly>
                            </div>
                        </div>
                        <!-- 參加對象 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">參加對象</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="target" id="target">{{ old('target', (isset($data->target))? $data->target : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 研習目標 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">研習目標</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="object" id="object">{{ old('object', (isset($data->object))? $data->object : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 研習方式 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">研習方式</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="content" id="content" maxlength="255">{{ old('content', (isset($data->content))? $data->content : '') }}</textarea>
                            </div>
                        </div>

                        <!-- 備註 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">備註</label>
                            <div class="col-md-10">
                                <textarea class="form-control input-max" rows="5" maxlength="1000" name="remark" id="remark">{{ old('remark', (isset($data->remark))? $data->remark : '') }}</textarea>
                            </div>
                        </div>

                        <!-- v英文班別名稱 -->
                        <div class="form-group row">
                            <label class="col-sm-2 control-label text-md-right pt-2">英文班別名稱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-max" id="english" name="english" placeholder="英文班別名稱" value="{{ old('english', (isset($data->english))? $data->english : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                        <!-- <form> -->
                          <fieldset style="border:groove; padding: inherit">
                            <legend>入口網站設定</legend>
                            <?php $list = $base->SQL('SELECT DISTINCT category,name FROM s03tb WHERE alias = "Y" ORDER BY category')?>
                            <div class="form-group row">
                                <!-- **班別類別 -->
                                <!-- <label class="col-sm-2 control-label text-md pt-2">班別類別</label>
                                <div class="col-md-5">
                                    <select id="category" name="category" class="select2 form-control select2-single input-max">
                                        @foreach($list as $key => $va)
                                            <option value="{{ $va->category }}" {{ old('category', (isset($data->category))? $data->category : 1) == $va->category? 'selected' : '' }}>{{ $va->name }}</option>
                                        @endforeach
                                    </select>
                                </div> -->
                                <label class="col-md-2 col-form-label text-md-right">班別類別<span class="text-danger">*</span></label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control number-input-max" id="category2"  value="{{ old('category', (isset($data->category))? $data->category : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required readonly>
                                    <input type="hidden" id="category" name="category"value="{{ old('category', (isset($data->category))? $data->category : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required readonly>
                                    <button class="btn btn-number" type="button" onclick="chooseClassType()">...</button>
                                </div>
      
                                <!-- **公務人員必讀課程 -->
                                <span class="col-sm-4">
                                    <input type="checkbox" id="is_must_read" name="is_must_read" value="1" {{ old('is_must_read', (isset($data->is_must_read))? $data->is_must_read : '') == '1'? 'checked' : '' }}>屬於公務人員必讀課程
                                </span>
                            </div>
                            <!--**入口網站開班方式 -->
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-md">入口網站開班方式</label>
                                <div class="col-md-4 pt-2">
                                    @foreach(config('app.upload1') as $key => $va)
                                        <input type="radio" id="upload1" name="upload1" value="{{ $key }}" {{ old('upload1', (isset($data->upload1)) ? $data->upload1 : 'Y') == $key? 'checked' : '' }}>{{ $va }}
                                    @endforeach
                                </div>
                            </div>
                          </fieldset>
                        <!-- </form> -->
                        <br>
                        <!-- 網頁公告 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">網頁公告</label>
                            <div class="col-md-2 pt-2">
                            @foreach(config('app.publish') as $key => $va)
                                <input type="radio" id="publish" name="publish" value="{{ $key }}" {{ old('publish', (isset($data->publish))? $data->publish : 'Y') == $key? 'checked' : '' }}>{{ $va }}
                            @endforeach
                            </div>
                            <label class="col-sm-2 control-label text-md-right pt-2">公告備註</label>
                            <!--公告備註-->
                            <div class="col-sm-6">
                                <input type="text"  class="form-control input-max" id="precautions" name="precautions" placeholder="此欄位內容會顯示於訓練需求及學習服務系統" value="{{ old('precautions', (isset($data->precautions))? $data->precautions : '') }}" autocomplete="off" maxlength="255">
                            </div>
                        </div>
                        <!-- v訓練績效計算方式 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">訓練績效計算方式</label>
                            <div class="col-md-6 pt-2">
                                <!-- <select id="cntflag" name="cntflag" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.cntflag') as $key => $va)
                                        <option value="{{ $key }}" {{ old('cntflag', (isset($data->cntflag))? $data->cntflag : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                @foreach(config('app.cntflag') as $key => $va)
                                    <input type="radio" id="cntflag" name="cntflag" value="{{ $key }}" {{ old('cntflag', (isset($data->cntflag))? $data->cntflag : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                            <!-- v講座審查 -->
                            <span class="col-sm-2 md-left pt-2">
                                    <input type="checkbox" id="profchk" name="profchk" value="Y" {{ old('profchk', (isset($data->profchk))? $data->profchk : '') == 'Y'? 'checked' : '' }}  >講座審查
                            </span>
                            <!-- v追蹤培訓班別 -->
                            <!-- <span class="col-sm-2 md-left pt-2">
                                    <input type="checkbox" id="trace" name="trace" {{ old('trace', (isset($data->trace))? $data->trace : '') == 'Y'? 'checked' : '' }} >追蹤培訓班別
                            </span> -->
                        </div>
                            <!--label class="col-form-label text-md">講座審查<span class="text-danger">*</span></label>
                            <div class="col-md-1"-->
                                <!-- <select id="profchk" name="profchk" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.yorn') as $key => $va)
                                        <option value="{{ $key }}" {{ old('profchk', (isset($data->profchk))? $data->profchk : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select> -->
                                
                                    <!--input type="radio" id="profchk" name="profchk" value="{{ $key }}" >{{ $va }} -->
                        <!-- 相同課程群組 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">相同課程群組</label>
                            <div class="col-md-6">
                                <select id="samecourse" name="samecourse" class="browser-default custom-select">
                                    @if(!isset($data) || $data->samecourse=='')
                                    <option value="">請選擇</option>
                                    @else
                                    <option value="">取消群組</option>
                                    @endif
                                    @foreach($sameCourseList as $value)
                                    <option value="{{$value['groupid']}}" {{ old('samecourse', (isset($data->samecourse))? $data->samecourse : '') == $value['groupid']? 'selected' : '' }}>{{$value['class_group']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" onclick="window.open('/admin/class_group', '_blank')" class="btn btn-sm btn-danger">檢核群組</button>
                            </div>
                        </div>
                        @if(isset($data))
                        <div class="form-group row" >
                            <label class="col-md-12 text-md-right">最後更新時間：{{$data->modifytime}}    最後更新人員：{{$data->modifyusername}} </label>    
                        </div>
                        @endif
                        <!-- 辦理方式 -->
                     <!--   <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">辦理方式<span class="text-danger">*</span></label>
                            <div class="col-md-10">
                                <select id="process" name="process" class="select2 form-control select2-single input-max">
                                    @foreach(config('app.process') as $key => $va)
                                        <option value="{{ $key }}" {{ old('process', (isset($data->process))? $data->process : 1) == $key? 'selected' : '' }}>{{ $va }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->
                        <?php
                            $tempVal = [];
                            for($j=0; $j<sizeof($orgchkPopList[1]); $j++) {
                                $tempVal[] = $orgchkPopList[1][$j]->enrollorg;
                            }
                            $tempVal = join(",", $tempVal);
                        ?>
                        <?php if($tempVal != '') { ?>
                            <input type="text" class="form-control" name="tempOrgchk" value="{{$tempVal}}" hidden>
                        <?php } else { ?>
                            <input type="text" class="form-control" name="tempOrgchk" hidden>
                        <?php } ?>
                      
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="checksigninsubmit();submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        @if(isset($data))
                            <button type="button" onclick="deleteClass()" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button>                         
                        @endif
                        <a href="/admin/classes">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>
            <!-- lightbox -->
            <div class="lightbox" id="example1">
                <figure>
                    <a href="#" class="close"></a>
                <!----報名方式設定---->
                    <!-- 報名方式 -->
                    <figcaption>
                        <b>報名方式設定</b>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right" id="registration_method_setting">報名方式</label>
                            <div class="col-md-10 pt-2">
                                    <!--input type="radio" id="signnotin" name="signin" value="0" style="display: none;" {{!isset($data->signin)?'checked' : ''}} -->
                                @foreach(config('app.signin') as $key => $va)
                                    <input type="radio" id="signin" name="signin" value="{{ $key }}" onclick="getregistration({{ $key }})" {{ old('signin', (isset($data->signin))? $data->signin : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                                
                            </div>
                        </div>
                        <!-- registration -->
                        <div class="form-group row" id="registrationclass" style="display: none;">
                            <label class="col-md-2 col-form-label text-md-right">開放報名對象</label>
                            <div class="col-md-10 pt-2 md-left">
                                @foreach(config('app.registration') as $key => $va)
                                    <input type="radio" id="registration" name="registration" value="{{ $key }}" onclick="getopenregistration({{ $key }})"  {{ old('registration', (isset($data->registration))? $data->registration : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>
                        <!-- 報名人數 -->
                        <div class="form-group row" id="quotaclass" style="display: none;">
                            <label class="col-sm-2 control-label text-md-right pt-2">正取名額</label>
                            <div class="col-sm-2 md-left" >
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="signupquota" name="signupquota" min="1" placeholder="請輸入正取名額" value="{{ old('signupquota', (isset($data->signupquota))? $data->signupquota : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>
                            </div>
                            <label class="control-label text-md pt-2">後補名額</label>
                            <div class="col-sm-2 md-left">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <!-- 輸入欄位 -->
                                    <input type="text" class="form-control number-input-max" id="signupexquota" name="signupexquota" min="1" placeholder="請輸入後補名額" value="{{ old('signupexquota', (isset($data->signupexquota))? $data->signupexquota : 0) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')">
                                </div>
                            </div>
                        </div>

                        <!-- **參訓機關 -->
                        <div class="form-group row" id="orgchkclass" style="display: flex;">
                            <label class="col-md-2 col-form-label text-md-right">參訓機關</label>
                            <div class="col-md-10 pt-2">
                                @foreach(config('app.orgchk') as $key => $va)
                                    <input type="radio" id="orgchk" name="orgchk" onchange="changeOrgchkPop({{ $key }})" value="{{ $key }}" {{ old('orgchk', (isset($data->orgchk))? $data->orgchk : 0) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                                <span class="input-group-btn">
                                 <button id="btn-orgchk" type="button" class="btn btn-primary" onclick="chooseOrgchk()">+</button>
                                </span>
                            </div>
                        </div>

                        <!-- v機關分區 -->
                        <div class="form-group row" id="areachkclass" style="display: none;">
                            <label class="col-md-2 col-form-label text-md-right">機關分區</label>
                            <div class=" md-left col-md-10 pt-2" id='areachk_div'>
                                @foreach(config('app.areachk') as $key => $va)
                                    <input type="radio" id="areachk" name="areachk" value="{{ $key }}" {{ old('areachk', (isset($data->areachk))? $data->areachk : 1) == $key? 'checked' : '' }}>{{ $va }}
                                @endforeach
                            </div>
                        </div>
                        <!--資格限制-->
                        <!-- 職等(第1組) -->
                        <fieldset style="border:groove; padding: inherit; display: block;" id="openregistration">
                            <legend>資格限制</legend>
                            <fieldset style="border:groove; padding: inherit">
                                <legend>第一組</legend>
                                <div class="form-group row">
                                    <label class="col-md-1 col-form-label text-md">職等</label>
                                    <div class="col-md-8">
                                        <!-- <select class="select2 form-control select2-single input-max" multiple="multiple"> -->
                                            <?php $oldData = old('rkchk', (isset($data->rkchk))? $data->rkchk : array());
                                                $showrkchk = '';
                                                foreach(config('app.post') as $key => $va){
                                                    $showrkchk .= in_array($key, $oldData)? $va.',' : '';
                                                } ?>
                                            <input type="text" class="form-control" name="showrkchk" value="{{ $showrkchk }}"  disabled>
                                            <input type="hidden" name="rkchk" value=""  >
                                            
                                        <!-- </select> -->
                                    </div>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="chooseRkchk()">+</button>
                                    </span>
                                </div>
                                <!--v第一組學員資格-->
                                <div class="form-group row">
                                    <label class="col-md-2 col-form-label text-md">學員資格</label>
                                    <div class="col-md-5 pt-2">
                                         @foreach(config('app.chfchk') as $key => $va)
                                            <input type="radio" id="chfchk" name="chfchk" value="{{ $key }}" {{ old('chfchk', (isset($data->chfchk))? $data->chfchk : '') == $key? 'checked' : '' }}>{{ $va }}
                                        @endforeach
                                    </div>
                                    <div class="col-md-5 pt-2">
                                         @foreach(config('app.perchk') as $key => $va)
                                            <input type="radio" id="perchk" name="perchk" value="{{ $key }}" {{ old('perchk', (isset($data->perchk))? $data->perchk : '') == $key? 'checked' : '' }}>{{ $va }}
                                        @endforeach
                                    </div>
                                </div>
                            </fieldset>
                            <!--第二組-->
                            <fieldset style="border:groove; padding: inherit">
                                <legend>第二組</legend>
                                <div class="form-group row">
                                    <label class="col-md-1 col-form-label text-md">職等</label>
                                    <div class="col-md-8">
                                        <?php $oldData = old('subrkchk', (isset($data->subrkchk))? $data->subrkchk : array());
                                        $showsubrkchk = '';
                                        foreach(config('app.post') as $key => $va){
                                            $showsubrkchk .= in_array($key, $oldData)? $va.',' : '';
                                        } ?>
                                        <input type="text" class="form-control" name="showsubrkchk" value="{{ $showsubrkchk }}"  disabled>
                                        <input type="hidden" name="subrkchk" value=""  >
                                    </div>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button" onclick="chooseSubrkchk()">+</button>
                                    </span>
                                </div>
                                <div class="form-group row">
                                    <!--v第二組學員資格-->
                                    <label class="col-md-2 col-form-label text-md">學員資格</label>
                                    <div class="col-md-5 pt-2" id="subchfchk">
                                         @foreach(config('app.chfchk') as $key => $va)
                                            <input type="radio"  name="subchfchk" value="{{ $key }}" {{ old('subchfchk', (isset($data->subchfchk))? $data->subchfchk : '') == $key? 'checked' : '' }}>{{ $va }}
                                        @endforeach
                                    </div>
                                    <div class="col-md-5 pt-2" id="subperchk">
                                         @foreach(config('app.perchk') as $key => $va)
                                            <input type="radio"  name="subperchk" value="{{ $key }}" {{ old('subperchk', (isset($data->subperchk))? $data->subperchk : '') == $key? 'checked' : '' }}>{{ $va }}
                                        @endforeach
                                    </div>
                                </div>
                            </fieldset>
                        </fieldset> 


                        <div class="card-footer">
                            <input type="hidden" id="checksignin" value="{{ isset($data->registration)? $data->registration : 0 }}">
                            <a href="#">
                                <button type="button" class="btn btn-sm btn-info" ><i class="fa fa-save pr-2"></i>關閉</button>
                            </a>
                            <!-- <a href="#">
                                <button type="button" class="btn btn-sm btn-danger" onclick="doClear()"><i class="fa fa-reply"></i>取消</button>
                            </a> -->
                        </div>
                    </figcaption> 
                </figure>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
    @if(isset($data))
        {!! Form::open([ 'method'=>'delete', 'url'=>'/admin/classes/'.$data->class, 'id'=>'deleteform']) !!}
            <!-- <button onclick="return confirm('確定要刪除嗎?')" class="btn btn-sm btn-danger"><i class="fa fa-trash pr-2"></i>刪除</button> -->
        {!! Form::close() !!}                            
    @endif
    <!-- 圖片 -->
    @include('admin/layouts/form/image')
 <!-- 上課方式 日期選擇 modal -->
 <div class="modal fade bd-example-modal-lg classDay" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog_80" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">上課方式</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime1" value="Y" autocomplete="off" {{ old('time1', (isset($data->time1))? $data->time1 : '') == 'Y'? 'checked' : '' }}><label>週一</label> 
                        </div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime2" value="Y" autocomplete="off" {{ old('time2', (isset($data->time2))? $data->time2 : '') == 'Y'? 'checked' : '' }}><label>週二</label>
                        </div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime3" value="Y" autocomplete="off" {{ old('time3', (isset($data->time3))? $data->time3 : '') == 'Y'? 'checked' : '' }}><label>週三</label>
                        </div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime4" value="Y" autocomplete="off" {{ old('time4', (isset($data->time4))? $data->time4 : '') == 'Y'? 'checked' : '' }}><label>週四</label>
                        </div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime5" value="Y" autocomplete="off" {{ old('time5', (isset($data->time5))? $data->time5 : '') == 'Y'? 'checked' : '' }}><label>週五</label>
                        </div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime6" value="Y" autocomplete="off" {{ old('time6', (isset($data->time6))? $data->time6 : '') == 'Y'? 'checked' : '' }}><label>週六</label>
                        </div>
                        <div class="display_inline">
                            <input type="checkbox" name="stime7" value="Y" autocomplete="off" {{ old('time7', (isset($data->time7))? $data->time7 : '') == 'Y'? 'checked' : '' }}><label>週日</label>
                        </div>
                    </div>
                    <div>
                        <div class="display_inline">
                            <input type="checkbox" name="sholiday" value="Y" autocomplete="off" {{ old('holiday', (isset($data->holiday))? $data->holiday : '') == 'Y'? 'checked' : '' }}><label>含國定假日</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="setTimes()">確定</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>


<!-- 班別類別 modal -->
<div class="modal fade bd-example-modal-lg classType" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog modal-dialog_120" role="document">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">班別類別</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body" style="height: 60vh;overflow: auto;">
                    <?php for($i = 0; $i < sizeof($classCategory); $i++) { ?>
                        
                        <?php if(isset($classCategory[$i+1]->indent)) { ?>
                            <?php if($classCategory[$i]->indent < $classCategory[$i+1]->indent) { ?>
                                <?php if($i == 0) { ?>
                                    <ul id="treeview" class="filetree">
                                <?php } ?>

                                <?php if($classCategory[$i]->indent == 1) { ?>
                                        <li><span class="folder classType_item"><?=$classCategory[$i]->name?></span>
                                <?php } else { ?>
                                        <li><span class="folder classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span>
                                <?php } ?>  
                                            <ul> 
                            <?php } else if($classCategory[$i]->indent == $classCategory[$i+1]->indent) { ?>
                                        <li><span class="file classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span></li>
                            <?php } else if($classCategory[$i]->indent > $classCategory[$i+1]->indent) { ?>
                                                <li><span class="file classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span></li>
                                <?php for($j = 0; $j < $classCategory[$i]->indent-$classCategory[$i+1]->indent; $j++) { ?>
                                            </ul>
                                        </li> 
                                <?php } ?>
                                      
                                    
                            <?php } ?>
                        <?php } else { ?>
                                        <li><span class="file classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span></li>
                                    </ul>
                        <?php } ?>
                    <?php } ?>
                    </ul>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmClassType()">確定</button>
			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
		        </div>
		    </div>
		</div>
	</div>


  <!-- 參訓機關 modal -->
  <div class="modal fade bd-example-modal-lg orgchk" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
	  	<div class="modal-dialog" role="document" style="max-width:900px;">
		    <div class="modal-content">
		        <div class="modal-header">
			        <h4 class="modal-title"><strong id="popTitle">參訓機關</strong></h4>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		        </div>
		        <div class="modal-body">
                    <div class="col-md-12" id="course_div" style='display: flex;'>
                        <!-- 未選取的課程  class="checkbox"-->
                        <div style="flex:1;">
                            <span>可選取欄位</span>
                            <div class="halfArea" style="flex:1;height:300px;max-width:300px;overflow:auto;">
                                <table style="width:100%;">
                                    <tbody id="orgchk_uncheckList">
                                        <tr class="item_con orgchk_item item_uncheck">
                                            <th>班號</th>
                                            <th>機關代碼</th>
                                        </tr>
                                        <?php for($i=0; $i<sizeof($orgchkPopList[0]); $i++) { ?>
                                            <tr class="item_con orgchk_item item_uncheck" onclick="selectItem(this, 'orgchk')">
                                                <td>{{$orgchkPopList[0][$i]->enrollorg}}</td>
                                                <td>{{$orgchkPopList[0][$i]->enrollname}}</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>  
                                </table>
                            </div>
                        </div>
                        

                        <div class="arrow_con">
                            <button class="btn btn-primary" onclick="changeItem(true, 'orgchk')" style="margin-bottom:10px;"><i class="fas fa-arrow-right" style="margin-right:3px;"></i>新增</button>
                            <button class="btn btn-danger" onclick="changeItem(false, 'orgchk')"><i class="fas fa-arrow-left" style="margin-right:3px;"></i>移除</button>
                        </div>

                        <!-- 已選取的課程 class="checkbox checkbox-primary"-->
                        <div style="flex:1;">
                            <span>已選取欄位</span>
                            <div class="halfArea" style="flex:1;height:300px;max-width:300px;overflow:auto;">
                                <table style="width:100%;">
                                    <tbody id="orgchk_checkList">
                                        <tr class="item_con orgchk_item item_check">
                                            <th>班號</th>
                                            <th>機關代碼</th>
                                        </tr>
                                        <?php for($i=0; $i<sizeof($orgchkPopList[1]); $i++) { ?>
                                            <tr class="item_con orgchk_item item_check" onclick="selectItem(this, 'orgchk')">
                                                <td>{{$orgchkPopList[1][$i]->enrollorg}}</td>
                                                <td>{{$orgchkPopList[1][$i]->enrollname}}</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
		        </div>
		        <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmOrgchk()">確定</button>
			        <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
		        </div>
		    </div>
		</div>
	</div>
    
    <!-- 第一組職等 modal -->
    <div class="modal fade bd-example-modal-lg rkchk" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width:900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">選取職等(第一組)</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" id="course_div" style='display: flex;'>
                        <!-- 未選取的課程  class="checkbox"-->
                        <div style="flex:1;">
                            <span>可選取欄位</span>
                            <div class="halfArea" style="flex:1;height:300px;max-width:300px;overflow:auto;">
                                <table style="width:100%;">
                                    <tbody id="rkchk_uncheckList">
                                        <tr class="item_con rkchk_item item_uncheck">
                                            <th>代碼</th>
                                            <th>職等</th>
                                        </tr>
                                        <?php $oldData = old('rkchk', (isset($data->rkchk))? $data->rkchk : array());?>
                                        @foreach(config('app.post') as $key => $va)
                                            @if(!in_array($key, $oldData))
                                            <tr class="item_con rkchk_item item_uncheck" onclick="selectItem(this, 'rkchk')">
                                                <td>{{ $key }}</td>
                                                <td>{{ $va }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>  
                                </table>
                            </div>
                        </div>
                        <div class="arrow_con">
                            <button class="btn btn-primary" onclick="changeItem(true, 'rkchk')" style="margin-bottom:10px;"><i class="fas fa-arrow-right" style="margin-right:3px;"></i>新增</button>
                            <button class="btn btn-danger" onclick="changeItem(false, 'rkchk')"><i class="fas fa-arrow-left" style="margin-right:3px;"></i>移除</button>
                        </div>
                        <!-- 已選取的課程 class="checkbox checkbox-primary"-->
                        <div style="flex:1;">
                            <span>已選取欄位</span>
                            <div class="halfArea" style="flex:1;height:300px;max-width:300px;overflow:auto;">
                                <table style="width:100%;">
                                    <tbody id="rkchk_checkList">
                                        <tr class="item_con rkchk_item item_check">
                                            <th>代碼</th>
                                            <th>職等</th>
                                        </tr>
                                        
                                        @foreach(config('app.post') as $key => $va)
                                            @if(in_array($key, $oldData))
                                            <tr class="item_con rkchk_item item_check" onclick="selectItem(this, 'rkchk')">
                                                <td>{{ $key }}</td>
                                                <td>{{ $va }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmRkchk()">確定</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 第二組職等 modal -->
    <div class="modal fade bd-example-modal-lg subrkchk" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width:900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">選取職等(第二組)</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" id="course_div" style='display: flex;'>
                        <!-- 未選取的課程  class="checkbox"-->
                        <div style="flex:1;">
                            <span>可選取欄位</span>
                            <div class="halfArea" style="flex:1;height:300px;max-width:300px;overflow:auto;">
                                <table style="width:100%;">
                                    <tbody id="subrkchk_uncheckList">
                                        <tr class="item_con subrkchk_item item_uncheck">
                                            <th>代碼</th>
                                            <th>職等</th>
                                        </tr>
                                        <?php $oldData = old('subrkchk', (isset($data->subrkchk))? $data->subrkchk : array());?>
                                        @foreach(config('app.post') as $key => $va)
                                            @if(!in_array($key, $oldData))
                                            <tr class="item_con subrkchk_item item_uncheck" onclick="selectItem(this, 'subrkchk')">
                                                <td>{{ $key }}</td>
                                                <td>{{ $va }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>  
                                </table>
                            </div>
                        </div>
                        <div class="arrow_con">
                            <button class="btn btn-primary" onclick="changeItem(true, 'subrkchk')" style="margin-bottom:10px;"><i class="fas fa-arrow-right" style="margin-right:3px;"></i>新增</button>
                            <button class="btn btn-danger" onclick="changeItem(false, 'subrkchk')"><i class="fas fa-arrow-left" style="margin-right:3px;"></i>移除</button>
                        </div>
                        <!-- 已選取的課程 class="checkbox checkbox-primary"-->
                        <div style="flex:1;">
                            <span>已選取欄位</span>
                            <div class="halfArea" style="flex:1;height:300px;max-width:300px;overflow:auto;">
                                <table style="width:100%;">
                                    <tbody id="subrkchk_checkList">
                                        <tr class="item_con subrkchk_item item_check">
                                            <th>代碼</th>
                                            <th>職等</th>
                                        </tr>
                                        
                                        @foreach(config('app.post') as $key => $va)
                                            @if(in_array($key, $oldData))
                                            <tr class="item_con subrkchk_item item_check" onclick="selectItem(this, 'subrkchk')">
                                                <td>{{ $key }}</td>
                                                <td>{{ $va }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmSubrkchk()">確定</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
    <!--  挑選機關選單 -->
    <div class="modal fade bd-example-modal-lg choose_organ" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog_120 modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong id="popTitle">機關選擇</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 60vh;overflow: auto;">
                    <form>     
                        <div class="search-float">
                            <div class="float-md mobile-100 row mr-1 mb-3">      
                                <div class="input-group col-4">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">機關代碼</label>
                                    </div>
                                    <input type="text" name="query_enrollorg" class="form-control">
                                </div>  
                                <div class="input-group col-6">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">機關名稱</label>
                                    </div>
                                    <input type="text" name="query_enrollname" class="form-control">
                                </div> 
                            </div>
                            <button type="button" onclick="queryOrgan()" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>
                            <button type="button" onclick="doClear()" class="btn mobile-100 mb-3 mb-md-0">重設條件</button>
                        </div>                                
                    </form> 
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th class='text-center'>功能</th>
                                <th class='text-center'>機關代碼</th>
                                <th class='text-center'>機關名稱</th>
                            </tr>
                        </thead>
                        <tbody id="organ_tbody">

                        </tbody>
                    </table>
                </div>
                <div id="wrapper">
                    <section>
                        <div id="pagination"></div>
                    </section>
                </div>            
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗 -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script src="/backend/plugins/pagination/pagination.js" charset="UTF-8"></script>
<script>
    var processList = <?=$processList?>;
    function deleteClass(){
        if(confirm('確定要刪除嗎?')){
            $("#deleteform").submit();
        }
    }

    //院區代號賦予
    function getbranchcode(){
        var title = $('select[name=branch]').val();
        // 機關分區
        var areachk = <?= isset($data->areachk)?$data->areachk:'1'?>;
        if(areachk =='3'){
            var areachk3 ='checked';
        }else if(areachk =='2'){
            var areachk2 ='checked';
        }else{
            var areachk1 ='checked';
        }
        // 流程名稱
        var key = 1;
        var html = '<option value="">不套用流程</option>';
        $.each(processList, function (key, value) {
            if(value.branch==title){
                if( key <2  && value.preset=='Y'){
                    html +='<option value='+value.id+' selected>'+value.name+'</option>';
                    key = 3;
                }else{
                    html +='<option value='+value.id+' >'+value.name+'</option>';
                }
            }
        });
        $('select[name=class_process]').html(html);
        
        if(title =='1'){
            $('#branchcode').val('A');
            html = '<input type="radio" id="areachk" name="areachk" value="1" '+areachk1+' >苗栗以北(含花東離島)\
                    <input type="radio" id="areachk" name="areachk" value="3" '+areachk3+'>不分區</div>';
            $('#areachk_div').html(html);        
        }else if(title = '2'){
            $('#branchcode').val('B');

            html = '<input type="radio" id="areachk" name="areachk" value="2" '+areachk2+'>臺中以南\
                    <input type="radio" id="areachk" name="areachk" value="3" '+areachk3+'>不分區</div>';
            $('#areachk_div').html(html);
        }else{
            $('#branchcode').val('');
        }
        return false;
    }
    // 選擇機關
    function showOrgan()
    {
        $(".choose_organ").modal('show');
    }

    function queryOrgan()
    {
        $("#organ_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");

        var enrollorg = $("input[name=query_enrollorg]").val();
        var enrollname = $("input[name=query_enrollname]").val();

        $.ajax({
            url: "/admin/field/getData/m17tbs",
            data: {'enrollorg': enrollorg, 'enrollname':enrollname}
        }).done(function(response) {
            console.log(response);
            if (response.total > 0){
                paginate(response.queryData, response.total);
            }else{
                $("#organ_tbody").html("<tr><td class='text-center' colspan='5'>查無資料</td><tr>");      
                $('#pagination').html("");      
            }
        });
    }

    function paginate(queryData, total) {
        var container = $('#pagination');

        container.pagination({
            dataSource: '/admin/field/getData/m17tbs?enrollorg=' + queryData.enrollorg + '&enrollname=' + queryData.enrollname,
            locator: 'data',
            totalNumber: total,
            pageSize: 10,
            showpages: true,
            showPrevious: true,
            showNext: true,
            showNavigator: true,
            showFirstOnEllipsisShow: true,
            showLastOnEllipsisShow: true,
            ajax: {
                beforeSend: function() {
                $("#organ_tbody").html("<tr><td class='text-center' colspan='5'>搜尋中......</td><tr>");
                }
            },
            callback: function(data, pagination) {
                // window.console && console.log(22, data, pagination);
                var organ_html = "";
                for (var i in data) {
                    organ_html += "<tr>" + 
                                    "<td class='text-center'>" + "<button data-dismiss='modal' type='button' onclick='chooseOrgan(\"" + data[i].enrollorg + '","' + data[i].enrollname  + "\")' class='btn btn-primary'>選擇</button>" + "</td>" + 
                                    "<td class='text-center'>" + data[i].enrollorg + "</td>" + 
                                    "<td>" + data[i].enrollname + "</td>" + 
                                "</tr>";
                }
                $("#organ_tbody").html(organ_html);
            }
        })
    }

    function chooseOrgan(enrollorg, enrollname){
        $("input[name=commission]").val(enrollorg);
        // $("input[name=enrollorg]").val(enrollorg);
        $("input[name=enrollname]").val(enrollname);
    }

    //清除條件
    function doClear(){
      $('#signin').val('1').trigger("click");
      document.all.query_enrollorg.value = "";
      document.all.query_enrollname.value = "";
      document.all.subperchk.value = "";
      document.all.subchfchk.value = "";
      document.all.subrkchk.value = "";
      document.all.perchk.value = "";
      document.all.chfchk.value = "";
      document.all.rkchk.value = "";
      document.all.checksignin.value = "0";
    }
    //報名方式設定
    function dosignin(){
        $('#checksignin').val('1');
    }

    // 選擇班別類別
    function chooseClassType() {
            $(".classType").modal('show');
    }
    // 選擇班別類別
    let classType = "";
    let className = "";
    function chooseType(index, code,name) {
        $('.classType_item').css('background-color', '');
        $('.classType_item').eq(index).css('background-color', '#ffe4c4');
        classType = code;
        className = name;
    }

    // 確認班別類別
    function confirmClassType() {
        $("#category2").val(className);
        $("#category").val(classType);
        $("#category").click();
    }
    
    // 選擇上課方式 日期
    function chooseClassDay() {
        $(".classDay").modal('show');
    }
    // 設定四期勾選
    function setTimes() {
        var class_str = '';
        if($('input[name=stime1]')[0].checked) { class_str+="週一," ;$('input[name=time1]').prop("checked", true); }else{
            $('input[name=time1]').prop("checked", false);
        }
        if($('input[name=stime2]')[0].checked) { class_str+="週二," ;$('input[name=time2]').prop("checked", true); }else{
            $('input[name=time2]').prop("checked", false);
        }
        if($('input[name=stime3]')[0].checked) { class_str+="週三," ;$('input[name=time3]').prop("checked", true); }else{
            $('input[name=time3]').prop("checked", false);
        }
        if($('input[name=stime4]')[0].checked) { class_str+="週四," ;$('input[name=time4]').prop("checked", true); }else{
            $('input[name=time4]').prop("checked", false);
        }
        if($('input[name=stime5]')[0].checked) { class_str+="週五," ;$('input[name=time5]').prop("checked", true); }else{
            $('input[name=time5]').prop("checked", false);
        }
        if($('input[name=stime6]')[0].checked) { class_str+="週六," ;$('input[name=time6]').prop("checked", true); }else{
            $('input[name=time6]').prop("checked", false);
        }
        if($('input[name=stime7]')[0].checked) { class_str+="週日," ;$('input[name=time7]').prop("checked", true); }else{
            $('input[name=time7]').prop("checked", false);
        }
        if($('input[name=sholiday]')[0].checked) { class_str+="含國定假日" ;$('input[name=holiday]').prop("checked", true); }else{
            $('input[name=holiday]').prop("checked", false);
        }
        if(class_str!=''){
            class_str=class_str.substring(0,class_str.length-1)

        }
        $("#newstyle").val(class_str);
        $(".classDay").modal('hide');
    }

    //根據學習性質判斷時數必填項目
    function getclassified(val){

        if(val=='1'){
            $('#elearnhr').attr('required','');
            $('#classhr').removeAttr('required');
            $('#classhr').attr('disabled','');
            $('#classhr').val(0);
            $('#elearnhr').removeAttr('disabled'); 
        }else if(val=='2'){
            $('#elearnhr').removeAttr('required');
            $('#classhr').attr('required','');
            $('#elearnhr').attr('disabled','');
            $('#elearnhr').val(0);
            $('#classhr').removeAttr('disabled');            
        }else if(val=='3'){
            $('#elearnhr').attr('required','');
            $('#classhr').attr('required','');
            $('#elearnhr').removeAttr('disabled'); 
            $('#classhr').removeAttr('disabled'); 
        }else{
            swal('參數錯誤!');
            // 強制為1
            $('#classified').val('1').trigger("change");
        }
    }
    //報名方式：若有選取委辦班別，不需設定報名方式
    function getprocess(){
        var select = $("select[name=process]").val();
        var branch = $("select[name=branch]").val();
        var base_class_process = $("input[name=base_class_process]").val();
        var html = '';
        var check = 0;
        $.each(processList, function (key, value) {
            if(value.branch==branch && value.process==select ){
                if (check<2 && value.id == base_class_process ){
                    html +='<option value='+value.id+' selected>'+value.name+'</option>';
                    check = 2;
                }else if (check<1 && value.preset=='Y' && base_class_process!= 0){
                    html +='<option value='+value.id+' selected>'+value.name+'</option>';
                    check = 1;
                }else{
                    html +='<option value='+value.id+' >'+value.name+'</option>';
                }
            }
        });
        if(html==''){
            getbranchcode();
        }else{
            var newhtml = '<option value="">不套用流程</option>';
            newhtml += html; 
            $('select[name=class_process]').html(newhtml);
        }
        if (select == '2') {
            $('#regist').attr('disabled','');
            $("#clientclass").css('display', 'flex');
        }else{
            $('#regist').removeAttr('disabled');
            $("#clientclass").css('display', 'none');
        }
    }
   
    function gettype(){
         //上課:非其他不＋顯示
        if($("#style").val() == 4) {
            $("#btn-classStyle").css("display", "block");
            $("#newstyle").css("display", "block");
            $("#newtype_class").css("display", "block");
            
        }else {
            $("#btn-classStyle").css("display", "none");
            $("#newstyle").css("display", "none");
            $("#newtype_class").css("display", "none");
        }
         //上課:密集:單位(周)不顯示
        var style = $('#style').val();
        if (style == '1') {
            $('#week').css('display','inline');
        }else{
            $('#week').css('display','none');
            $("input[name='kind']:radio[value='2']").attr('checked','true');
        }
        countTotalDays();
    }
    // 選擇職等 第一組
    function chooseRkchk(){
        $(".rkchk").modal('show');
    }


    // 選擇職等 第二組
    function chooseSubrkchk(){
        $(".subrkchk").modal('show');
    }


    // 選擇參訓機關
    function chooseOrgchk() {
        $(".orgchk").modal('show');
    }
    // 點選項目
    let leftCon;
    let rightCon;
    function selectItem(e, name) {
        leftCon = -1;
        rightCon = -1;
        let classname = name+"_item";

        if($(e).hasClass("active")) {
            $(e).removeClass("active");
            rightCon = -1;
            leftCon = -1;
            return;
        }
        else {
            $("."+classname).removeClass("active");

            $(e).addClass('active');
        }

        if($(e).hasClass("item_uncheck")) {
            leftCon = $(e).index();
            rightCon = -1;
        }
        else {
            rightCon = $(e).index();
            leftCon = -1;
        }
    }

    // 左右換項目
    function changeItem(type, name) {
        let classname = name+"_item";
        let countIndex = 0;
        if(!type) {      // 移除項目
            if(rightCon == -1) {
                return;
            }
            countIndex = $("."+classname+".item_uncheck").length;
            let a = $("."+classname+".item_check").eq(rightCon);
            a.addClass("item_uncheck");
            a.removeClass("item_check");
            a.find('input').prop("checked", false);
            $("."+classname+".item_uncheck").eq(countIndex-1).after(a);
            rightCon = -1;
            leftCon = countIndex;
        }
        else {      // 新增項目
            if(leftCon == -1) {
                return;
            }
            countIndex = $("."+classname+".item_check").length;
            let b = $("."+classname+".item_uncheck").eq(leftCon);
            b.addClass("item_check");
            b.removeClass("item_uncheck");
            b.find('input').prop("checked", true);
            $("."+classname+".item_check").eq(countIndex).after(b);
            leftCon = -1;
            rightCon = countIndex;      
        }
    }
    function paddingLeft(str,lenght){
        if(str.length >= lenght)
        return str;
        else
        return paddingLeft("0" +str,lenght);
    }
    // pop選擇參訓機關
    function confirmOrgchk() {
        let tempOrgchk = '';
        for(let i=1; i<$(".orgchk_item.item_check").length; i++) {
            tempOrgchk += $(".orgchk_item.item_check").eq(i).find('td').html() + ',';
        }

        $("input[name=tempOrgchk]").val(tempOrgchk);
    }
    // pop職等
    function confirmRkchk(){
        let rkchk = '';
        let base ='';
        for(let i=1; i<$(".rkchk_item.item_check").length; i++) {
            base = $(".rkchk_item.item_check").eq(i).find('td').html();
            rkchk += paddingLeft(base,2) + ',';
        }
        $("input[name=showrkchk]").val('編輯中...');
        $("input[name=rkchk]").val(rkchk);
    }  
    function confirmSubrkchk(){
        let subrkchk = '';
        let base ='';
        for(let i=1; i<$(".subrkchk_item.item_check").length; i++) {
            base = $(".subrkchk_item.item_check").eq(i).find('td').html();
            subrkchk += paddingLeft(base,2) + ',';
        }
        $("input[name=showsubrkchk]").val('編輯中...');
        $("input[name=subrkchk]").val(subrkchk);
    }   
    // 訓期類別為天跟周要為整數,天為小數第一位
    function kindChange() {
        var kindval = $(':radio[name="kind"]:checked').val();
        // 確保訓期有值
        if ( ! $('#period').val()) {
            $('#period').val(1)
        }

        if (kindval == '2') {
            // 天為小數點第一位
            var period = parseFloat($('#period').val());
            $('#period').val(period.toFixed(1));
        } else {
            // 時數為整數
            $('#period').val(parseInt($('#period').val()));
        }
    }
    // 計算總天數、時數
    function countTotalDays() {

        var kindval = $(':radio[name="kind"]:checked').val();
        // 確保訓期有值
        if ( ! $('#period').val()) {
            $('#period').val(1)
        }
        // 確保每日上課時數有值
        if ( ! $('#dayhour').val()) {
            $('#dayhour').val(8)
        }
        // 取得訓期
        var period = parseFloat($('#period').val());
        // 取得每天時數
        var dayhoues = parseInt($('#dayhour').val());
        // 計算總天數 
        if (kindval == '1') { // 以週為單位
            var total_days = period * 5;
            if(dayhoues < 3 ){ //若每日上課時數<3小時，訓練天數以0.5天計。
                var total_days = total_days.toFixed(0)/2 ;
            }else{
                var total_days = total_days.toFixed(0) ;
            }
        } else if (kindval == '2') { // 以天為單位
            if(dayhoues < 3 ){ //若每日上課時數<3小時，訓練天數以0.5天計。
                var total_days = period.toFixed(0)/2 ;
            }else{
                var total_days = period.toFixed(0) ;
            }
        } else { // 以時為單位
            var total_days = period / parseInt($('#dayhour').val());
            if(total_days < 1){
                total_days = 1;
            }else{
                total_days =Math.round(total_days);
            }
        }
        // 寫入訓練傯天數
        $('#trainday').val(total_days);

        // 計算總時數
        if (kindval == '1') {
            // 以週為單位
            var totalhours = period * 5 * dayhoues;

        } else if (kindval == '2') {
            // 以天為單位
            var totalhours = parseInt(period * dayhoues);
        } else {
            // 以時為單位
            var totalhours = period;
        }
        // 寫入訓練總時數
        $('#trainhour').val(totalhours);

        // 學習性質為數位
        if($("input[name=classified]")[0].checked){
            $('#elearnhr').val(totalhours);
        }else{ //實體或混成
            $('#elearnhr').val('0');
            $('#classhr').val(totalhours);
        }
    }
    // 實體時數或數位時數不能高於總時數
    function hoursChange() {
        // var trainhour = parseInt($('#trainhour').val());
        // if (parseInt($('#'+em).val()) > trainhour) {
        //     $('#'+em).val(trainhour);
        // }
        // 訓練總時數=數位+實體時數
        var elearnhr = $('#elearnhr').val();
        var classhr = $('#classhr').val();
        var totalhours = parseInt(elearnhr) + parseInt(classhr);
        $('#trainhour').val(totalhours);
    }
    // 切換地點
    @if(!isset($data))
        function locationChange() {

            if ($('#serial_number').val()) {
                if (parseInt($('#serial_number').val()) > 500) {

                    $('#branch').val('2').trigger("change");
                    $("#areachk").val("1").trigger("change");
                } else {

                    $('#branch').val('1').trigger("change");
                    $("#areachk").val("3").trigger("change");
                }
            }
        }
        // 初始化
        locationChange();
    @endif
 
    // function gotoregist(){
    //     $("#registration_method_setting")[0].scrollIntoView();
    // }
    //報名方式
    function getregistration(value){
        $('#checksignin').val(1);
        if (value=='2'){
            $('#areachkclass').css('display','none'); 
            $('#quotaclass').css('display','none'); 
            //開放對象未開放 預設對象強制為1
            $('#registrationclass').css('display','none'); 
            $('#registration').val('1').trigger("change");
            $('#registration').trigger('click');
            if($('#process').val() =='2'){
                 swal('若為委辦班別，則報名方式不可為年度臨時增開班期!');
                // 強制為1
                $('#signin').val('1').trigger("change");
                $('#signin').val('1').trigger("click");
            }
        }else if(value=='3'){
            $('#areachkclass').css('display','none'); 
            $('#quotaclass').css('display','inline'); 
            $('#registrationclass').css('display','inline'); 
            if($('#process').val() =='2'){
                 swal('若為委辦班別，若為委辦班別，則報名方式不可為開放自由報名班期!');
                // 強制為1
                $('#signin').val('1').trigger("change");
                $('#signin').val('1').trigger("click");
            }
        }else if(value=='1'){
            $('#areachkclass').css('display','inline'); 
            $('#quotaclass').css('display','none'); 
            //開放對象未開放 預設對象強制為1
            $('#registrationclass').css('display','none');
            $('#registration').val('1').trigger("change"); 
            $('#registration').trigger('click');
        }else{
            swal('參數錯誤!');
            // 強制為1
            $('#signin').val('1').trigger("change");
        }
    }

  // 新增頁面，日期改變更新參訓機關list
  function changeOrgchkPop(orgchk) {
    if(orgchk == 3) {
        $.ajax({
                type: 'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType: "html",
                url:"/admin/classes/getOrgchk",
                @if ( isset($data) )
                data: { class: '{{ $data->class }}' },
                @else
                data: { class: '' },
                @endif 
                success: function(data){
                    let dataArr = JSON.parse(data);
                    let tempHTML = "<tr class='item_con orgchk_item item_uncheck'>\
                                            <th>班號</th>\
                                            <th>機關代碼</th>\
                                        </tr>";
                    for(let i=0; i<dataArr[0].length; i++) {
                        tempHTML += "<tr class='item_con orgchk_item item_uncheck' onclick='selectItem(this, \"orgchk\")'>\
                                        <td>"+dataArr[0][i].enrollorg+"</td>\
                                        <td>"+dataArr[0][i].enrollname+"</td>\
                                    </tr>";
                    }
                    $("#orgchk_uncheckList").html(tempHTML);
                        tempHTML = '';
                        tempHTML += "<tr class='item_con orgchk_item item_check'>\
                                        <th>班號</th>\
                                        <th>機關代碼</th>\
                                    </tr>";
                    for(let i=0; i<dataArr[1].length; i++) {
                        tempHTML += "<tr class='item_con orgchk_item item_check' onclick='selectItem(this, \"orgchk\")'>\
                                        <td>"+dataArr[1][i].enrollorg+"</td>\
                                        <td>"+dataArr[1][i].enrollname+"</td>\
                                    </tr>";
                    }
                    $("#orgchk_checkList").html(tempHTML);
                },
                error: function() {
                    console.log('Ajax Error');
                }
            });
        $("#btn-orgchk").css("display", "block");
    }
    else {
        $("#btn-orgchk").css("display", "none");
    }
          
          
        }

    //開放報名對象
    function getopenregistration(value){
        if (value=='1'){
            $('#openregistration').css('display','block');
            $('#orgchkclass').css('display','flex');
             
        }else if(value=='2' || value=='3'){
            $('#openregistration').css('display','none'); 
            $('#orgchkclass').css('display','none');
        }else{
            swal('參數錯誤!');
        }
    }
    //檢查報名設定
    function checksigninsubmit(){
        if( $('#checksignin').val()=='0' ){
            swal('請設定報名方式!');

            return false;
        }
    }

    $(document).ready(function (e) {
        $('#signin:checked').trigger('click');
        $('#orgchk:checked').trigger('click');
        gettype();
        getbranchcode();
        getprocess();
        setTimes();
    });
   
    // 選擇參訓機關(判斷是否顯示額外btn)
    function changeOrgchk() {
        if($("#orgchk:checked").val() == 3) {
            $("#btn-orgchk").css("display", "block");
        }
        else {
            $("#btn-orgchk").css("display", "none");
        }
    }

    // 選擇學習性質
    function changeClassified() {
    let classifiedValue = $("input[name=classified]:checked").val();
    if(classifiedValue == 1) {
        $("input[name=elearnhr]").prop("disabled", false);   
        $("input[name=classhr]").prop("disabled", true);
        $("input[name=classhr]").val(0);
    }
    else if(classifiedValue == 2) {
        $("input[name=elearnhr]").prop("disabled", true);
        $("input[name=classhr]").prop("disabled", false);
        $("input[name=elearnhr]").val(0);
    }
    else if(classifiedValue == 3) {
        $("input[name=elearnhr]").prop("disabled", false);
        $("input[name=classhr]").prop("disabled", false);
    }
    $("input[name=trainhour]").val(Number($("input[name=elearnhr]").val())+Number($("input[name=classhr]").val()));
    }
    // 初始化
    countTotalDays();
    kindChange();
    changeOrgchk();
    changeClassified();
   
    // 初始化階層樹
    setTimeout(() => {
        $("#treeview").treeview({
            persist: "location",
            collapsed: true,
            unique: false,
            toggle: function() {
                // console.log("%s was toggled.", $(this).find(">span").text());
            }
        });
    }, 1000);


</script>
@endsection