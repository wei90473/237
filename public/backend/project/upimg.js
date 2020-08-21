// 要上傳的config
var imageChannel = '';
// 新增圖片的目標
var aimsImage;
// 上傳圖片容量上限,抓取config值,不存在時為2M
if(typeof(upimgMaxSize) == 'undefined'){var upimgMaxSize = 2;}

var upimgMaxSizeValue;
// 計算多少bit
upimgMaxSizeValue = (upimgMaxSize)? upimgMaxSize * 1048576 : upimgMaxSizeValue;

// 選擇上傳檔案(多張)
$(".uploadImageAry").click(function(){
    // 要上傳的config
    imageChannel = $($(this)[0]).attr('data-uploadChannel');
    // 新增圖片的目標
    aimsImage = this;
    $('#img_file_ary').click();
})

// 檢查檔案
function upimgAry(e){

    var files = e.files;

    // 檢查檔案
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        // 確認檔案type
        if ( ! file.type.match('image.*')) {
            swal('檔案格式錯誤');
            return;
        }
        // 檢查檔案大小
        if (upimgMaxSize && file.size > upimgMaxSizeValue) {
            swal('單個檔案大小超過' + upimgMaxSize + 'M');
            return;
        }
    }

    // 迴圈跑所有檔案上傳
    for (var i = 0; i < files.length; i++) {

        ajaxUploadImage(files[i]);
    }
}

// 上傳圖片
function ajaxUploadImage(file) {

    html = $('#image-model-ary').html();

    var element = $(aimsImage).parents('.image_box').find('.sortable_box').append(html);

    element = $(element).find('.image-box').last();
    // 建立表單
    var formData = new FormData();
    // push file
    formData.append('image', file, file.name);
    // laravel csrf
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        async: true,
        url: '/admin/upload/image/' + imageChannel,
        type: "post",
        dataType: "json",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        success: function (result) {

            if (result.success && result.success != 'undefined') {
                console.log(result)
                // 替換圖片值
                $($(element).find('img')).attr('src', result.img);
                $($(element).find('.image_value')).val(result.img);
                // 關閉loading
                $(element).find('.image-box-loading').hide();
                // 顯示圖片內容
                $(element).find('.image-box-content').show();
            } else {
                // 錯誤,刪除元素
                $(element).remove();

                swal('上傳失敗,錯誤訊息:' + result.msg);
            }
        }
    })
}

// 選擇上傳檔案(單張)
$(".uploadImage").click(function(){
    // 要上傳的config
    imageChannel = $($(this)).attr('data-uploadChannel');
    // 新增圖片的目標
    aimsImage = this;
    $('#img_file').click();
})

// 開始上傳(單張)
function upimg(e){
    // 要上傳的唯一檔案
    var file = e.files[0];
    // 確認檔案type
    if ( ! file.type.match('image.*')) {
        swal('檔案格式錯誤');
        return;
    }
    // 檢查檔案大小
    if (upimgMaxSize && file.size > upimgMaxSizeValue) {
        swal('檔案大小超過' + upimgMaxSize + 'M');
        return;
    }
    // 建立表單
    var formData = new FormData();
    // loading
    $($($(aimsImage).parents('.image_box')).find('img')).hide();
    $($($(aimsImage).parents('.image_box')).find('.loading')).show();

    // push file
    formData.append('image', file, file.name);
    // laravel csrf
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        async: true,
        url: '/admin/upload/image/' + imageChannel,
        type: "post",
        dataType: "json",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        success: function (result) {

            if (result.success && result.success != 'undefined') {
                appendImageHtml(e, result.img);
            } else {
                swal('上傳失敗,錯誤訊息:' + result.msg);
            }
        }
    })
}

// 加入html圖片(單張)
function appendImageHtml(e, imagePath){
    $($($(aimsImage).parents('.image_box')).find('img')).attr('src', imagePath);
    $($($(aimsImage).parents('.image_box')).find('input')).val(imagePath);
    // 結束loading
    $($($(aimsImage).parents('.image_box')).find('img')).show();
    $($($(aimsImage).parents('.image_box')).find('.loading')).hide();
}

