CKEDITOR.dialog.add(
    "serverimg",
    function (editor) {
        return {
            title: "上傳圖片",
            minWidth: 400,
            minHeight: 300,
            contents:
                [
                    {
                        id: "tab1",
                        label: "",
                        title: "123",
                        expand: true,
                        padding: 0,
                        elements:
                            [
                                {
                                    type: "html",

                                    html:'<div class="text-center" style="max-height: 350px;margin-bottom: 20px;"><img id="ckeditor_image" class="img-thumbnail" src="" style="max-width:100%;max-height:300px;"></div>' +
                                        '<input onchange="uploadCkeditorImage(this);" type="file" id="ckeditor_image_file" style="display:none">上傳圖片：<button id="ckeditor_image_btn" type="button" class="btn btn-warning waves-effect waves-light btn-sm m-b-5" style="padding-left: 10px;width:56px;height:32px">選擇檔案</button>' +
                                        '<span class="pl-2 pt-2 text-secondary">建議圖片尺寸 寬800高不限</span>' +
                                        '<br></br>圖片描述：<input id="ckeditor_image_alt" style="padding-left: 5px;height: 30px;width:300px;" type="text" class="cke_dialog_ui_input_text" placeholder="請輸入封面圖片描述" autocomplete="off" required="" maxlength="255">',

                                }
                            ]
                    }
                ],
            onOk: function () {

                if ( ! $('#ckeditor_image_alt').val()) {

                    swal('請填寫圖片描述');

                    return false;
                }

                if ( ! $('#ckeditor_image').attr('src')) {

                    swal('請上傳圖片');

                    return false;
                }

                var html = '<img src="'+$('#ckeditor_image').attr('src')+'" class="img-responsive" alt="'+$('#ckeditor_image_alt').val()+'">';
                // 插入到編輯器
                editor.insertHtml(html);

                $('#ckeditor_image').attr('src', '')
                $('#ckeditor_image_alt').val('')

            },
        };
    }
);

// 點擊選擇檔案
$('html').on('click', '#ckeditor_image_btn',function(){

    $('#ckeditor_image_file').click();
});