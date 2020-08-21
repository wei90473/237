// 檢查是否有輸入
function submitForm(formName) {

    result = true;

    // 確認沒有空白欄位
    $.each( $(formName + ' [required]'), function( key, va ) {

        // 是否為空白
        if ($(va).val() == '' || $(va).val() == null) {

            result = false;

            if( $(va).is("input") ){
                // 顯示錯誤訊息
                showInputError(va, $(va).attr('placeholder'));

            }else if( $(va).is("select") ){
                // Select
                showSelectError(va);
            }
        }
    });

    // 檢查最短長度
    $.each( $(formName + ' [minlength]'), function( key, va ) {

        min = $(va).attr('minlength')

        if ($(va).val().length < min) {

            result = false;

            showInputError(va, '最少要輸入' + min + '個字元');
        }
    });

    // 確認密碼一致
    $.each( $(formName + ' [name="password_confirmation"]'), function( key, va ) {

        if ($(va).val() !== $(formName + ' [name="password"]').val()) {
            result = false;

            showInputError(va, '密碼與確認密碼不一致');
        }
    });

    // 密碼強度檢查
    // $.each( $(formName + ' [name="password"]'), function( key, va ) {
    //     // 密碼不是空白時才檢查
    //     if ($(va).val()) {
    //
    //         var regNumber = /\d+/; // 驗證0-9任意數字至少出現一次
    //         var regString = /[a-zA-Z]+/; // 驗證大小寫英文至少出現一次
    //
    //         if ($(va).val().length < 6 || ! regNumber.test($(va).val()) || ! regString.test($(va).val())) {
    //
    //             result = false;
    //
    //             showInputError(va, '密碼最少要輸入 6 個字元，並含有英文及數字');
    //         }
    //     }
    // });

    // 民國日期檢查
    $.each( $('.roc-date'), function( key, va ) {


        if ($(va).find('.roc-date-year').val() == '' && $(va).find('.roc-date-month').val() == '' && $(va).find('.roc-date-day').val() == '') {
            // 完全沒輸入
            $(va).find('.roc-date-input').val('');

        } else if($(va).find('.roc-date-year').val() || $(va).find('.roc-date-month').val() || $(va).find('.roc-date-day').val()) {
            // 有輸入
            if ($(va).find('.roc-date-year').val() == '' || $(va).find('.roc-date-month').val() == '' || $(va).find('.roc-date-day').val() == '') {
                // 輸入不完整
                if ($(va).find('.roc-date-year').val() == '') {
                    result = false;
                    showInputError($(va).find('.roc-date-year'), '請輸入完整日期');
                }

                if ($(va).find('.roc-date-month').val() == '') {
                    result = false;
                    showInputError($(va).find('.roc-date-month'), '請輸入完整日期');
                }

                if ($(va).find('.roc-date-day').val() == '') {
                    result = false;
                    showInputError($(va).find('.roc-date-day'), '請輸入完整日期');
                }
            } else {

                // 輸入完整,檢查格式
                var year = parseInt($(va).find('.roc-date-year').val()) + 1911;
                var month = parseInt($(va).find('.roc-date-month').val());
                var day = parseInt($(va).find('.roc-date-day').val());

                // 檢查月份1~12月
                if (month < 1 || month > 12) {

                    result = false;

                    showInputError($(va).find('.roc-date-month'), '請輸入正確日期');
                }

                // 不得超過31的月份檢查
                if (month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) {
                    if (day < 1 || day > 31) {

                        result = false;

                        showInputError($(va).find('.roc-date-day'), '請輸入正確日期');
                    }
                }
                // 不得超過30的月份檢查
                if (month == 4 || month == 6 || month == 9 || month == 11) {
                    if (day < 1 || day > 30) {

                        result = false;

                        showInputError($(va).find('.roc-date-day'), '請輸入正確日期');
                    }
                }
                // 二月份檢查
                if (month == 2) {

                    if ((year % 4 == 0 && year % 100 != 0) || (year % 400 == 0 && year % 3200 != 0)) {
                        // 閏年
                        if (day < 1 || day > 29) {

                            result = false;

                            showInputError($(va).find('.roc-date-day'), '請輸入正確日期');
                        }
                    } else {
                        // 平年
                        if (day < 1 || day > 28) {

                            result = false;

                            showInputError($(va).find('.roc-date-day'), '請輸入正確日期');
                        }
                    }
                }

                if (result) {
                    // 寫入到隱藏欄位
                    $(va).find('.roc-date-input').val(year + '-'+month+'-'+day);
                }
            }
        }


    });

    // 是否有額外的檢查function
    if (window.verification) {

        result = verification(result);
    }

    if (result) {
        // 送出表單
        $(formName).submit();
    } else {
        // 滑動到錯誤訊息的地方
        $('html,body').animate({scrollTop:$($('.input_error').parents('.form-group')).offset().top - 100}, 300);
    }
}

// 顯示select錯誤訊息
function showSelectError(va) {
    // 刪除原本的錯誤訊息
    $(va).parent().parent().find('.error').remove();
    $(va).parent().find('.select2-selection--single').addClass('input_error');
    html = '<div><label class="error pt-1">請選擇</label></div>';
    $(va).next().after(html);
}
// 顯示錯誤訊息後再次點擊,刪除訊息
$('form').on('click', '.select2-selection',function(){
    // 刪除紅框
    $(this).removeClass('input_error');
    // 刪除錯誤訊息,最多刪除到兩層內的error
    $(this).parent().find('.error').remove();
    $(this).parent().parent().find('.error').remove();
    $(this).parent().parent().parent().find('.error').remove();
});


// 顯示錯誤訊息
function showInputError(va, msg) {

    if (msg == undefined) {
        msg = '此為必填欄位';
    }

    // 刪除原本的錯誤訊息
    $(va).parent().find('.error').remove();
    $(va).parent().parent().find('.error').remove();
    // 組成錯誤訊息
    html = '<label class="error">' + msg + '</label>';
    // 顯示紅框
    $(va).addClass('input_error');

    if ($(va).parent().hasClass('input-group')) {
        // 顯示錯誤訊息, 如果是input-group內的輸入欄位則顯示在父結點後面
        $(va).parent().after(html);
    } else if($(va).parent().hasClass('file-box')) {
        // 上傳檔案,顯示在父父節點後面
        $(va).parents('.file-box').after(html);
    }else {
        // 顯示在input後面
        $(va).after(html);
    }
}


// 顯示錯誤訊息後再次點擊,刪除訊息
$('form').on('click', '[required],.select2-selection,.roc-date-year,.roc-date-month,.roc-date-day',function(){
    // 刪除紅框
    $(this).removeClass('input_error');
    // 刪除錯誤訊息,最多刪除到三層內的error
    $(this).parent().find('.error').remove();
    $(this).parent().parent().find('.error').remove();
});

// 上傳圖片刪除訊息
$('form').on('click', '.uploadImage',function(){
    // 刪除錯誤訊息
    $(this).parents('.image_box').find('.error').remove();
    // 刪除紅框
    $(this).parents('.image_box').find('.input_error').removeClass('input_error');
});

// 上傳檔案刪除訊息
$('form').on('click', '.uploadFile',function(){
    // 刪除錯誤訊息
    $(this).parents('.upload_file_box').find('.error').remove();
    // 刪除紅框
    $(this).parents('.upload_file_box').find('.input_error').remove();
});