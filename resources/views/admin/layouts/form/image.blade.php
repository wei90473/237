<!-- 刪除確認視窗 -->
<div id="image_del_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <button type="submit" class="btn mr-3 btn-danger" onclick="$($(imgDelElement).parents('.image-box')).remove();$('#image_del_modol').modal('hide')">確定</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 上傳用的input(多張) -->
<input type="file" id="img_file_ary" multiple onchange="upimgAry(this)" class="displaynone">

<!-- 上傳用的input(單張) -->
<input type="file" id="img_file" onchange="upimg(this)" class="displaynone">

<!-- 圖片模組(多張) -->
<model id="image-model-ary" class="displaynone">
    <div class="item col-md-4 image-box pl-0 pr-2 pb-2">
        <div class="img-thumbnail img-border">
            <button type="button" onclick="delImage(this)" class="img-del-btn btn btn-icon waves-effect waves-light btn-danger m-b-5"><i class="fa fa-remove"></i></button>
            <!-- loading -->
            <div class="image-box-loading">
                <div class="spinner-border text-pink my-5" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <!-- 圖片及內容 -->
            <div class="image-box-content displaynone">
                <img src="" class="thumb-img">
                <input value="" name="image[]" class="image_value" type="hidden">

                <div class="input-group m-t-10">
                    <div class="input-group-append">
                        <span class="input-group-text">圖片描述</span>
                    </div>

                    <input value="" name="image_alt[]" type="text" class="form-control input-max" placeholder="請輸入圖片描述"  autocomplete="off" required>
                </div>
            </div>
        </div>
    </div>
</model>
{{-- 上傳圖片大小限制 --}}
{!! (config('app.upimage_max_size'))? '<script>var upimgMaxSize = '.config('app.upimage_max_size').';</script>' : '' !!}