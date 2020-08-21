@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'effectiveness_process';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">成效問卷處理(105)表單</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin" class="text-info">首頁</a></li>
                        <li><a href="/admin/effectiveness_process" class="text-info">成效問卷處理(105)列表</a></li>
                        <li class="active">成效問卷處理(105)表單</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- form start -->
            @if ( isset($data) )
                {!! Form::open([ 'method'=>'put', 'url'=>'/admin/effectiveness_process/'.$data[0]->class.'-'.$data[0]->term.'-'.$data[0]->times.'-'.$data[0]->serno, 'id'=>'form']) !!}
            @else
                {!! Form::open([ 'method'=>'post', 'url'=>'/admin/effectiveness_process/create', 'id'=>'form']) !!}
            @endif

            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">成效問卷處理(105)表單</h3></div>
                        <div class="card-body pt-4">

                            <!-- 班號 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">班號<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="class" class="form-control input-max" placeholder="請輸入班號" value="{{ $class_info['class'] }}" autocomplete="off" required maxlength="255" readonly>
                                </div>
                            </div>

                            <!-- 期別 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">期別<span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="term" class="form-control input-max" placeholder="請輸入期別" value="{{$class_info['term'] }}" autocomplete="off" required maxlength="255" readonly>
                                </div>
                            </div>

                            <!-- 第幾次調查 -->
                            <div class="form-group row">
                                <label class="col-sm-2 control-label text-md-right pt-2">第幾次調查</label>
                                <div class="col-sm-10">
                                    <input type="text" name="times" class="form-control input-max" placeholder="請輸入第幾次調查" value="{{$class_info['times']}}" autocomplete="off" maxlength="255" readonly>
                                </div>
                            </div>
                          
                            <!-- 研習規劃 -->
                            <div class="form-group row">
                                <label class="col-md-2 control-label text-md-right pt-1">研習規劃</label>
                                <div class=" col-xs-2">
                                    <div class="form-group row pl-2 pt-1">
                                        <div class="col-sm-2 control-label">1</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q11" name="q11" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-sm-2 control-label">2</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q12" name="q12" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-sm-2 control-label">3</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q13" name="q13" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-sm-2 control-label">4</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q14" name="q14" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-sm-2 control-label">5</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q15" name="q15" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            

                            <!-- 學習投入 -->
                            <div class="form-group row">
                                <label class="col-md-2 control-label text-md-right pt-1">學習投入</label>
                                <div class=" col-xs-2">
                                    <div class="form-group row pl-2 pt-1">
                                        <div class="col-sm-2 control-label">6</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q21" name="q21" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-sm-2 control-label">7</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q22" name="q22" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-sm-2 control-label">8</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q23" name="q23" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 學習輔導 -->
                            <div class="form-group row">
                                <label class="col-md-2 control-label text-md-right pt-1">學習輔導</label>
                                <div class=" col-xs-2">
                                    <div class="form-group row pl-2 pt-1">
                                        <div class="col-sm-2 control-label">9</div>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-max" id="q31" name="q31" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1">
                                        <div class="col-xs-4 control-label">10</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-max" id="q32" name="q32" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1 pl-2">
                                        <div class="col-xs-5 control-label">11</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-max" id="q33" name="q33" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 行政問題 -->
                            <div class="form-group row">
                                <label class="col-md-2 control-label text-md-right pt-1">行政問題</label>
                                <div class=" col-xs-2">
                                    <div class="form-group row pl-2 pt-1">
                                        <div class="col-xs-4 control-label">12</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-max" id="q41" name="q41" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xs-2">
                                    <div class="form-group row pt-1 pl-2">
                                        <div class="col-xs-4 control-label">13</div>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control input-max" id="q42" name="q42" size="1" placeholder="5"  autocomplete="off" maxlength="1" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 講座方面 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">講座方面</label>
                                <div class="col-md-10">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>題次</td>
                                                    <td>課程名稱</td>
                                                    <td>講座</td>
                                                    <td>教學技法</td>
                                                    <td>教學內容</td>
                                                    <td>教學態度</td>
                                                </tr>
                                            </thead>
                                             <?php $i=1; ?>
                                            @foreach ($class_list as $class_info)
                                                <?php $idno=explode(",",$class_info['idno']);$course=explode(",",$class_info['course']); ?>
                                                <?php for($j=0;$j<count($class_info['class_name']);$j++){?>
                                                <?php   
                                                    for($z=0;$z<count($class_info['teacher_name']);$z++){
                                                        if($idno[$j]==$class_info['teacher_name'][$z]->idno){
                                                            $teacher_name=$class_info['teacher_name'][$z]->cname;
                                                        }
                                                    }
                                                    
                                                ?>
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$class_info['class_name'][$j]->name}}</td>
                                                    <td>{{$teacher_name}}</td>
                                                    <td><input type="text" style="width:100px" name="course_{{$idno[$j]}}_{{$course[$j]}}[]" ></td>
                                                    <td><input type="text" style="width:100px" name="course_{{$idno[$j]}}_{{$course[$j]}}[]" ></td>
                                                    <td><input type="text" style="width:100px" name="course_{{$idno[$j]}}_{{$course[$j]}}[]" ></td>
                                                </tr>
                                                <?php $i++; ?>
                                                <?php }?>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 其他建議 -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label text-md-right">其他建議</label>
                                <div class="col-md-10">
                                    <textarea class="form-control input-max" rows="5" maxlength="1000" name="note" id="note"></textarea>
                                </div>
                            </div>

                    </div>

                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/effectiveness_process">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                        </a>
                    </div>
                </div>
            </div>

            {!! Form::close() !!}

        </div>
    </div>

    <!-- 圖片 -->
    @include('admin/layouts/form/image')

@endsection
