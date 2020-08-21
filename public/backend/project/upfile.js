// 要上傳的config
var fileChannel = '';
// 新增檔案的目標
var aimsFile;
// 上傳檔案上限,抓取config值,不存在時為2M
if(typeof(upfileMaxSize) == 'undefined'){var upfileMaxSize = 2;}


var upfileMaxSizeValue;
// 計算多少bit
upfileMaxSizeValue = (upfileMaxSize)? upfileMaxSize * 1048576 : upfileMaxSizeValue;

// 選擇上傳檔案(多個)
$(".uploadFileAry").click(function(){
    // 要上傳的config
    fileChannel = $($(this)[0]).attr('data-uploadChannel');
    // 新增圖片的目標
    aimsFile = this;
    $('#file_file_ary').click();
})

// 開始上傳(多個檔案)
function upfileAry(e){

    var files = e.files;

    // 檢查檔案
    for (var i = 0; i < files.length; i++) {

        var file = files[i];

        // 檢查檔案大小
        if (upfileMaxSize && file.size > upfileMaxSizeValue) {
            swal('單個檔案大小超過' + upfileMaxSize + 'M');

            return;
        }
    }

    // 迴圈跑所有檔案上傳
    for (var i = 0; i < files.length; i++) {

        ajaxUploadFile(files[i]);
    }

    // 清除input
    $('#file_file_ary').val('');
}

function ajaxUploadFile(file)
{
    html = $('#file-model-ary').html();

    var element = $(aimsFile).parents('.file_box').find('.sortable_box').append(html);

    element = $(element).find('.file-box').last();
    // 建立表單
    var formData = new FormData();
    // push file
    formData.append('file', file, file.name);
    // laravel csrf
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        async: true,
        url: '/admin/upload/file/' + fileChannel,
        type: "post",
        dataType: "json",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        success: function (result) {

            if (result.success && result.success != 'undefined') {

                // // 替換值
                $($(element).find('.extension')).html('.' + result.data.extension);
                $($(element).find('input[name="file[extension][]"]')).attr('value', result.data.extension);
                $($(element).find('input[name="file[path][]"]')).attr('value', result.data.path);
                $($(element).find('input[name="file[name][]"]')).attr('value', result.data.name);

                // 關閉loading
                $(element).find('.file-box-loading').hide();
                // 顯示圖片內容
                $(element).find('.file-box-content').css('opacity', '1');
            } else {
                // 錯誤,刪除元素
                $(element).remove();
                swal('上傳失敗,錯誤訊息:' + result.msg);
            }
        }
    })

}

// 加入html圖片(多個)
function appendFilrHtmlAry(data){

    $($('#file-model-ary').find('.extension')).html('.' + data.extension);
    $($('#file-model-ary').find('input[name="file[extension][]"]')).attr('value', data.extension);
    $($('#file-model-ary').find('input[name="file[path][]"]')).attr('value', data.path);
    $($('#file-model-ary').find('input[name="file[name][]"]')).attr('value', data.name);

    html = $('#file-model-ary').html();

    $(aimsFile).parents('.file_box').find('.sortable_box').append(html);
}

// 上傳檔案註記刪除
function fileSetDel ()
{
    $(fileDelElement).parents('.file-box').find('input[name="file[act][]"]').attr('value', 'del');
    $(fileDelElement).parents('.file-box').hide();
    $('#file_del_modol').modal('hide');
}


// 選擇上傳檔案(單個)
$(".uploadFile").click(function(){
    // 要上傳的config
    fileChannel = $($(this)[0]).attr('data-uploadChannel');
    // 新增圖片的目標
    aimsFile = this;
    $('#file_file').click();
})

// 開始上傳(單個檔案)
function upfile(e) {
    // 要上傳的唯一檔案
    var file = e.files[0];

    // 檢查檔案大小
    if (upfileMaxSize && file.size > upfileMaxSizeValue) {
        swal('單個檔案大小超過' + upfileMaxSize + 'M');

        return;
    }

    element = $(aimsFile).parents('.upload_file_box');
    // 顯示loading
    $(element).find('.loading').show();
    $(element).find('.file-box').hide();

    // 建立表單
    var formData = new FormData();
    // push file
    formData.append('file', file, file.name);
    // laravel csrf
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        async: true,
        url: '/admin/upload/file/' + fileChannel,
        type: "post",
        dataType: "json",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        success: function (result) {

            if (result.success && result.success != 'undefined') {

                $(element).find('input[name="path"]').attr('value', result.data.path);
                $(element).find('input[name="extension"]').attr('value', result.data.extension);
                $(element).find('input[name="size"]').attr('value', result.data.size);
                $(element).find('.extension_text').html(result.data.extension);

                // 改掉連結
                $(element).find('.download-link').attr('href', '#');
                $(element).find('.file-download-btn').attr('onclick', "swal('新上傳的檔案，請先儲存後再下載')");

                // 關閉loading
                $(element).find('.loading').hide();
                $(element).find('.file-box').show();

            } else {
                // 關閉loading
                $(element).find('.loading').hide();
                
                swal('上傳失敗,錯誤訊息:' + result.msg);
            }
        }
    })
}
