<style>
.panel-heading .accordion-toggle {
    display: inline-block;
    text-align: center;
    color: rgb(255, 255, 255);
    font-size: 16px;
    box-sizing: border-box;
    line-height: 1.8em;
    vertical-align: middle;
    font-family: 微軟正黑體, "Microsoft JhengHei", Arial, Helvetica, sans-serif !important;
    background: rgb(250, 160, 90);
    padding: 5px 20px;
    border-width: initial;
    border-style: none;
    border-color: initial;
    border-image: initial;
    margin: 2px 0px;
    border-radius: 6px;
    margin-bottom: 10px;
}
.panel-heading .accordion-toggle:hover {
    background-color: #f79448 !important;
    border: 1px solid #f79448 !important;
        -webkit-box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
    box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12);
    opacity: 1;
}
.panel-heading .accordion-toggle::before {
    background-color: inherit; !important;
}
.panel-heading .accordion-toggle.collapsed::before {
    background-color: inherit; !important;
}
</style>


<div class="float-md mobile-100 row mr-1 mb-3 ">
    <div class="input-group col-2">
        <div class="input-group-prepend">
            <span class="input-group-text">年度</span>
        </div>
        <select type="text" id="yerly" name="t01tb[yerly]" class="browser-default custom-select" style="min-width: 80px; flex:0 1 auto">
            @for($i = (int)date("Y")-1910; $i >= 90 ; $i--)
                <option value="{{$i}}"
                {{ ( $queryData['t01tb']['yerly'] == $i ) ? 'selected' : '' }}
                >{{$i}}</option>
            @endfor
        </select>
    </div>
    <!-- 班號 -->
    <div class="input-group col-4">
            <div class="input-group-prepend">
            <span class="input-group-text">班號</span>
        </div>
        <input type="text" id="class" name="t01tb[class]" class=" form-control" autocomplete="off" value="{{ $queryData['t01tb']['class']  }}" >
    </div>
    <div class="input-group col-2">
        <div class="input-group-prepend">
            <span class="input-group-text">期別</span>
        </div>
        <input type="text" id="term" name="t04tb[term]" class="form-control field_term" autocomplete="off" value="{{ $queryData['t04tb']['term'] }}" style="min-width: 80px; flex:0 1 auto">
    </div>
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">班別名稱</span>
        </div>
        <input type="text" id="name" name="t01tb[name]" class="form-control" autocomplete="off" value="{{ $queryData['t01tb']['name']  }}">
    </div>
</div>



<!-- 班別名稱 -->
<div class="float-md mobile-100 row mr-1 mb-3">
    <!-- **類別1 -->
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">辦班院區</span>
        </div>
        <select class="form-control select2 " name="t01tb[branch]">
            <option value="">全部</option>
            @foreach(config('app.branch') as $key => $va)
                <option value="{{ $key }}"
                {{ ($queryData['t01tb']['branch'] == $key) ? 'selected' : '' }}
                >{{ $va }}</option>
            @endforeach
        </select>
    </div>
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">開訓日期(起)</span>
        </div>
        <input type="text" id="sdate" name="sdate_start" class="form-control" autocomplete="off" value="{{ $queryData['sdate_start'] }}" >
        <span class="input-group-addon" style="cursor: pointer;" id="datepicker1"><i class="fa fa-calendar"></i></span>
    </div>
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">開訓日期(訖)</span>
        </div>
        <input type="text" id="edate" name="sdate_end" class="form-control" autocomplete="off" value="{{ $queryData['sdate_end'] }}">
        <span class="input-group-addon" style="cursor: pointer;" id="datepicker2"><i class="fa fa-calendar"></i></span>
    </div>
</div>
<!-- 進階/簡易搜尋開始 -->
<div class="panel-group" id="accordion">
    <header class="panel-heading">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#search"> </a>
    </header>
    <footer id="search" class="panel-collapse collapse">
        
    <!-- 進階/簡易搜尋開始 -->
    <div class="float-md mobile-100 row mr-1 mb-3">

        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">上課地點</span>
            </div>
            <select class="form-control select2" name="t04tb[site_branch]">
                <option value="">請選擇</option>
                @foreach (config('database_fields.m14tb')['branch'] as $branch => $branch_name)
                    <option value="{{ $branch }}"
                    {{ ($queryData['t04tb']['site_branch'] == $branch) ? 'selected' : '' }}
                    >{{ $branch_name }}</option>
                @endforeach
            </select>
        </div>
        
    </div>

    <div class="float-md mobile-100 row mr-1 mb-3">
        @if (!isset($ignoreQueryField['t01tb']['process']))
        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">班別類型</span>
            </div>
            <select class="form-control select2" name="t01tb[process]" id="process">
                <option value="">全部</option>
                @foreach(config('app.process') as $key => $va)
                    <option value="{{ $key }}" 
                    {{ ($queryData['t01tb']['process'] == $key) ? 'selected' : '' }}
                    >{{ $va }}</option>
                @endforeach
            </select>
        </div>
        @endif 
                                                   
        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">班務人員</span>
            </div>
            <select class="form-control select2" name="t04tb[sponsor]">
                <option value="">請選擇</option>                                                        
                @foreach ($sponsors as $sponsor)
                    <option value="{{ $sponsor->userid }}"
                    {{ ($queryData['t04tb']['sponsor'] == $sponsor->userid) ? 'selected' : '' }}
                    >{{ $sponsor->username }}</option>
                @endforeach 
            </select>
        </div>

        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">委訓單位</span>
            </div>
            <input type="text" name="t01tb[commission]" class="form-control" autocomplete="off" value="{{ $queryData['t01tb']['commission'] }}">
        </div>     
    </div>

    <div class="float-md mobile-100 row mr-1 mb-3">
        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">訓練性質</span>
            </div>
            <select class="form-control select2" name="t01tb[traintype]">
                <option value="">全部</option>
                @foreach(config('app.traintype') as $key => $va)
                    <option value="{{ $key }}" 
                    {{ ($queryData['t01tb']['traintype'] == $key) ? 'selected' : '' }}
                    >{{ $va }}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">班別性質</span>
            </div>
            <select class="form-control select2" name="t01tb[type]">
                <option value="">全部</option>
                @foreach(config('app.class_type') as $key => $va)
                    <option value="{{ $key }}" 
                    {{ ($queryData['t01tb']['type'] == $key) ? 'selected' : '' }}
                    >{{ $va }}</option>
                @endforeach
            </select>                                                    
        </div>                                                
        <div class="input-group col-4">
            <div class="input-group-prepend">
                <span class="input-group-text">類別1</span>
            </div>
            <select class="form-control select2" name="t01tb[categoryone]">
                <option value="">請選擇</option>
                @foreach($s01tbM as $code => $name)
                    <option value="{{ $code }}" {{ ($queryData['t01tb']['categoryone'] == $code) ? 'selected' : null }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="float-md mobile-100 row mr-1 mb-3">
        <div class="input-group col-6">
            <div class="input-group-prepend">
                <span class="input-group-text">結訓日期(起)</span>
            </div>
            <input type="text" id="graduate_start_date" name="edate_start" class="form-control" autocomplete="off" value="{{ $queryData['edate_start'] }}">
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker3"><i class="fa fa-calendar"></i></span>
        </div>
        <div class="input-group col-6">
            <div class="input-group-prepend">
                <span class="input-group-text">結訓日期(訖)</span>
            </div>
            <input type="text" id="graduate_end_date" name="edate_end" class="form-control" autocomplete="off" value="{{ $queryData['edate_end'] }}">
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker4"><i class="fa fa-calendar"></i></span>
        </div>
    </div>

    <div class="float-md mobile-100 row mr-1 mb-3">
        <div class="input-group col-6">
            <div class="input-group-prepend">
                <span class="input-group-text">在訓期間(起)</span>
            </div>
            <input type="text" id="training_start_date" name="training_start" class="form-control" autocomplete="off" value="{{ $queryData['training_start'] }}">
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker5"><i class="fa fa-calendar"></i></span>
        </div>
        <div class="input-group col-6">
            <div class="input-group-prepend">
                <span class="input-group-text">在訓期間(訖)</span>
            </div>
            <input type="text" id="training_end_date" name="training_end" class="form-control" autocomplete="off" value="{{ $queryData['training_end'] }}">
            <span class="input-group-addon" style="cursor: pointer;" id="datepicker6"><i class="fa fa-calendar"></i></span>
        </div>
    </div>  

<!-- 進階/簡易搜尋結束 -->
</footer>
                                            </div>
<!-- 進階/簡易搜尋結束 -->
