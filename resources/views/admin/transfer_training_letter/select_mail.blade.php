@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'notice_emai';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>收件者</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="float-left search-float" style="min-width: 1000px;">
                                    </div>

                                    {!! Form::open([ 'method'=>'post', 'name'=>'form', 'id'=>'form']) !!}
                                    <input type="hidden" name="class" value="{{ $queryData['class'] }}">
                                    <input type="hidden" name="term" value="{{ $queryData['term'] }}">
                                    <input type="hidden" name="subject" value="{{ $queryData['subject'] }}">
                                        <!--班期資料-->
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="5%"><input type="checkbox" id="checkAll" onclick="toggle(this)"></th>
                                                        <th class="text-center">姓名</th>
                                                        <th class="text-center">單位</th>
                                                        <th class="text-center">MAIL</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($data as $row) {?>
                                                    <tr class="text-center">
                                                        <td><input type="checkbox" name="checkbox[]" value="{{$row['email']}}" <?=($row['mail_list']=='Y')?'checked':'';?> ></td>
                                                        <td>{{$row['cname']}}</td>
                                                        <td>{{$row['dept']}}</td>
                                                        <td>{{$row['email']}}</td>
                                                    </tr>
                                                    <?php }?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-12 row" style="margin-top:1%">
                                            <button type="button" class="btn btn-primary mobile-100 mb-3 mb-md-0" onclick="submitform();">送出</button>
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
        window.document.form.action='/admin/transfer_training_letter/savelist';
        submitForm('#form');
    }
    function toggle(source) {
        checkboxes = document.getElementsByName('checkbox[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
@endsection

