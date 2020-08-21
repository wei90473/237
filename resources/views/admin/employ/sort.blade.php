@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'employ';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>設定講座排序</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">

                                    {!! Form::open([ 'method'=>'post', 'name'=>'form', 'id'=>'form']) !!}
                                    <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                    <input type="hidden" name="term" value="{{ $queryData['term'] }}">
                                        <!--班期資料-->
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">講師姓名</th>
                                                        <th style="width: 20%;" class="text-center">排序</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row) {?>
                                                    <tr class="text-center">
                                                        <td>{{$row['cname']}}</td>
                                                        <td><input type="text" class="form-control number-input-max" id="teacher_sort" name="{{ $row['idno'] }}_sort" maxlength="2" placeholder="排序" value="{{ old('teacher_sort', (isset($row['teacher_sort']))? $row['teacher_sort'] : '') }}"  onkeyup="this.value=this.value.replace(/[^\d]/g,'')" ></td>
                                                    </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-12 row" style="margin-top:1%">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0" onclick="submitform();">設定</button>

                                            <!-- <a href="/admin/employ/detail?class={{ $queryData['class'] }}&term={{ $queryData['term'] }}">
                                                <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i> 回列表頁</button>
                                            </a> -->
                                        </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script type="text/javascript">
function submitform(){
    window.document.form.action='/admin/employ/sort';
    submitForm('#form');
}

</script>
@endsection

