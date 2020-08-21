<!-- 刪除確認視窗 -->
<div id="file_del_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <button type="submit" class="btn mr-3 btn-danger" onclick="fileSetDel()">確定</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 上傳用的input(多個檔案) -->
<input type="file" id="file_file_ary" multiple onchange="upfileAry(this)" class="displaynone">

<!-- 上傳用的input(單個檔案) -->
<input type="file" id="file_file" onchange="upfile(this)" class="displaynone">

<!-- 檔案模組(多個) -->
<model id="file-model-ary" class="displaynone">
    <div class="item col-md-4 file-box pl-0 pr-2 pb-2">
        <div class="file-border">

            <div class="float-right">
                <!-- 下載 -->
                <button  onclick="swal('新上傳的檔案，請先儲存後再下載')" type="button" class="file-download-btn btn btn-icon waves-effect waves-light btn-primary m-b-5"><i class="ion-ios7-cloud-download"></i></button>
                <!-- 刪除 -->
                <button type="button" onclick="delFile(this)" class="file-del-btn btn btn-icon waves-effect waves-light btn-danger m-b-5"><i class="fa fa-remove"></i></button>
            </div>

            <!-- Input -->
            <input type="hidden" name="file[act][]" value="create">
            <input type="hidden" name="file[id][]" value="0">
            <input type="hidden" name="file[extension][]" value="">
            <input type="hidden" name="file[path][]" value="">

            <!-- loading -->
            <div class="file-box-loading text-center">
                <div class="spinner-border text-pink my-4" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            <!-- 下載次數 -->
            <div class="float-left pt-1 file-box-content" style="opacity:0;">
                <div class="font-weight-normal pl-1 py-2">
                    <i class="fa fa-file pr-1"></i>
                    <span>下載次數：0</span>
                </div>
            </div>

            <!-- 檔案內容 -->
            <div class="input-group m-t-10 file-box-content" style="opacity:0;">
                <input type="text" class="form-control input-max" name="file[name][]" placeholder="請輸入檔案名稱" value="" autocomplete="off" required maxlength="255">

                <div class="input-group-append">
                    <span class="input-group-text extension"></span>
                </div>
            </div>

        </div>
    </div>
</model>
{{-- 上傳檔案大小限制 --}}
{!! (config('app.upfile_max_size'))? '<script>var upfileMaxSize = '.config('app.upfile_max_size').';</script>' : '' !!}

<script>
    // 檔案上傳說明
    function fileExplain() {
        swal(
            {
                title: '<strong>檔案上傳說明</strong>',
                html:
                    '<div class="text-left"><b>檔案大小</b>：不得超過 {{ config('app.upfile_max_size') }} MB<br>' +
                    '<b>檔案副檔名</b>：{{ implode("、", config('app.extension_pass')) }} <div>',
                confirmButtonText: "確定",
            }
        );
    };
</script>