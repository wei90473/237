// ckeditor 上傳圖片
function uploadCkeditorImage(e) {

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
    // push file
    formData.append('image', file, file.name);
    // laravel csrf
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        async: true,
        url: '/admin/upload/image/ckeditor',
        type: "post",
        dataType: "json",
        data: formData,
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        success: function (result) {

            if (result.success && result.success != 'undefined') {
                $('#ckeditor_image').attr('src', result.img);
            } else {
                swal('上傳失敗,錯誤訊息:' + result.msg);
            }
        }
    })
}