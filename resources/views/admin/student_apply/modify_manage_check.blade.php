<div class="row">
    <div class="float-left search-float col-12">
        {!! Form::open(['method' => 'get']) !!}
            <div class="float-md mobile-100 row mr-1 mb-3">
                <div class="input-group col-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">年度</span>
                    </div>
                    <select type="text" id="yerly" name="yerly" class="yerly browser-default custom-select">
                        @for($year=(int)date("Y")-1910;$year>=90;$year--)
                            <option value="{{ $year }}" {{ ($queryData['yerly'] == $year) ? 'selected' : null }} >{{ $year }}</option>
                        @endfor
                    </select>
                </div>    
                <div class="input-group col-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">班號</span>
                    </div>
                    <input type="text" class="form-control" class="class" name="class" value="{{ $queryData['class'] }}">
                </div>  
                <div class="input-group col-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">班別名稱：</span>
                    </div>
                    <input type="text" class="form-control" name="class_name" value="{{ $queryData['class_name'] }}">
                </div> 
                <div class="input-group col-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">期別：</span>
                    </div>
                    <input type="text" class="form-control" name="term" value="{{ $queryData['term'] }}">
                </div> 
                <div class="input-group col-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">類型：</span>
                    </div>
                    <select type="text" name="type" class="yerly browser-default custom-select " value="{{ $queryData['type'] }}">
                        <option value="" {{ ($queryData['type'] == "") ? 'selected' : null }}>不限</option>
                        <option value="1" {{ ($queryData['type'] == "1") ? 'selected' : null }}>換員</option>
                        <option value="2" {{ ($queryData['type'] == "2") ? 'selected' : null }}>補報</option>
                        <option value="4" {{ ($queryData['type'] == "4") ? 'selected' : null }}>取消報名</option>
                    </select>
                </div>                                                                                                                                                                                                                                            
            </div>
            <button type="submit" class="btn mobile-100 mb-3 mb-md-0"><i class="fa fa-search fa-lg pr-1"></i>搜尋</button>  
            <a href="/admin/student_apply/modify_manage" ><button type="button" class="btn btn-primary btn-sm mb-3 mb-md-0">重設條件</button></a>
                                                        
        {!! Form::close() !!}
    </div>
</div>   
<div class="table-responsive margin_top_bottom10" >
    {{ Form::open(['method' => 'put', 'url' => '/admin/student_apply/reviewModify']) }}
    <table class="table table-bordered mb-0">
        <thead>
        <tr>
            <th width="100">類型</th>
            <th class="text-center" width="70">班號</th>
            <th>班別名稱</th>
            <th>期別</th>
            <th>舊學員身分證號</th>
            <th>舊學員身姓名</th>
            <th>舊學員服務機關</th>
            <th>新學員身分證號</th>
            <th>新學員姓名</th>
            <th>新學員服務機關</th>
            <th style="width:130px">審核</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($modifyLogs as $modifyLog)
            <tr>
                <td widtd="100">
                @if (!empty(config('database_fields.apply_modify_logs.type')[$modifyLog->type]))
                    {{ config('database_fields.apply_modify_logs.type')[$modifyLog->type] }}
                @endif 
                </td>
                <td class="text-center" widtd="70">{{ $modifyLog->class }}</td>
                <td>{{ $modifyLog->name }}</td>
                <td>{{ $modifyLog->term }}</td>
                <td>{{ $modifyLog->idno }}</td>
                <td>{{ $modifyLog->cname }}</td>
                <td>{{ $modifyLog->enrollname }}</td>
                <td>{{ $modifyLog->new_idno }}</td>
                <td>{{ $modifyLog->new_cname }}</td>
                <td>{{ $modifyLog->new_enrollname }}</td>
                <td>
                {{ Form::select("status[{$modifyLog->id}]", config('database_fields.apply_modify_logs.status'), $modifyLog->status, ['class' => 'browser-default custom-select', 'disabled' => ($modifyLog->status == 'Y') ? 'disabeld' : null]) }}
                </td>
            </tr>
            @endforeach 
        </tbody>
    </table>   
    <div>
        <button class='btn btn-parimary'>送出</button> 
    </div>   
    {{ Form::close() }}                     
</div> 
