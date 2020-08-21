@inject('base', 'App\Presenters\BasePresenter')
@extends('admin/layouts/layouts')
@section('content')

    <?php $_menu = 'web_portal';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">入口網站代碼維護</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li><a href="/admin/web_portal" class="text-info">入口網站代碼維護</a></li>
                        <li class="active">入口網站代碼編輯</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')
            <!-- form start -->
            {!! Form::open([ 'method'=>'post', 'url'=>'/admin/web_portal/edit/'.$data->code, 'id'=>'form']) !!}
            <div class="col-md-10 offset-md-1 p-0">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">入口網站代碼編輯</h3></div>
                    <div class="card-body pt-4">
                        <!-- 學院代碼 -->
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">學院代碼<span class="text-danger">*</span></label>
                            <div class="col-md-1">
                                <input type="text" class="form-control number-input-max" id="code" name="code" value="{{ old('code', (isset($data->code))? $data->code : '') }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <!-- 學院專長 -->
                            <label class="col-md-2 col-form-label text-md-right">學院專長<span class="text-danger">*</span></label>
                            <div class="col-md-5">
                                <div class="input-group bootstrap-touchspin number_box">
                                    <input type="text" class="form-control number-input-max" id="name" name="name" value="{{ old('name', (isset($data->name))? $data->name : '') }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label text-md-right">入口網站代碼<span class="text-danger">*</span></label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control number-input-max" id="category2"  value="{{ old('category', (isset($data->category))? $data->categoryname.$data->category : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required readonly>
                                <input type="hidden" id="category" name="category"value="{{ old('category', (isset($data->category))? $data->category : NULL) }}" autocomplete="off" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" required readonly>
                                <button class="btn btn-sm btn-info" type="button" onclick="chooseClassType()">修改</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" onclick="submitForm('#form');" class="btn btn-sm btn-info"><i class="fa fa-save pr-2"></i>儲存</button>
                        <a href="/admin/web_portal">
                            <button type="button" class="btn btn-sm btn-danger"><i class="fa fa-reply"></i>回上一頁</button>
                        </a>
                    </div>    
                </div>
            </div>
            {!! Form::close() !!}
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

@endsection
@section('js')
<script>
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

