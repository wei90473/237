

// 解掉手機版一開始選單會跑出來的問題
$('.open-left').click(function(){
    $('.side-menu').css('display', 'block')
})

// 下拉選單
$('.select2').select2();

// 切換每頁幾筆
$('#paginate_qty').change(function(){
    // 取得切換成幾筆
    qty = $('#paginate_qty').val();
    // 替換搜尋的欄位
    $('#_paginate_qty').val(qty)
    // 送出搜尋表單
    $('#search_form').submit();
})

// 日期選擇
$('.datepicker').datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    // startDate: "today",
    // clearBtn: true,
    // calendarWeeks: true,
    // todayHighlight: true,
    language:"zh-TW"
});

// 日期套件
$('.date-range').daterangepicker(
    {
        autoUpdateInput: false, // 允許輸入空值
        "locale": {

            format: 'YYYY-MM-DD',
            applyLabel: "確定",
            cancelLabel: "取消",
            resetLabel: "重置",
            daysOfWeek : [ "日", "一", "二", "三", "四", "五", "六" ],
            monthNames : [ "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月" ],
        }
    }
);

// 允許輸入空值
$('.date-range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
});
// 允許輸入空值
$('.date-range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
});

// 時間選擇器
jQuery('.time').timepicker({
    showMeridian: false
});


// 狀態開關
$(function() {
    $('.active_checkbox').bootstrapToggle({
        on: '啟用',
        off: '停用'
    });
})

// 列表切換狀態
function changeActive(e) {

    id = $(e).data('id');

    active = ($(e).prop("checked"))? 1 : 0;

    $.ajax({
        type: "get",
        dataType: "html",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: location.pathname + '/active',
        data: { id: id, active: active},
        success: function(data){
        },
        error: function() {
            console.log('Ajax Error');
        }
    });
}


// 刪除圖片對象
var imgDelElement;
// 刪除圖片
function delImage(e) {
    imgDelElement = e;
    $('#image_del_modol').modal();
}

// 刪除檔案對象
var fileDelElement;
// 刪除檔案
function delFile(e) {
    fileDelElement = e;
    $('#file_del_modol').modal();
}

// 拖拉
$.each( $('.sortable_box'), function( key, va ) {
    // 不含有disabled屬性的才能拖拉
    if ($(va).attr("disabled") == undefined) {
        Sortable.create(va, {
            // 參數設定[註1]
            disabled: false, // 關閉Sortable
            animation: 300,  // 物件移動時間(單位:毫秒)
            handle: ".sortable_box",  // 可拖曳的區域
            // filter: "input",  // 過濾器，不能拖曳的物件
            // preventOnFilter: true, // 當過濾器啟動的時候，觸發event.preventDefault()
            draggable: ".item",  // 可拖曳的物件
            ghostClass: "sortable-ghost",  // 拖曳時，給予物件的類別
            chosenClass: "sortable-chosen",  // 選定時，給予物件的類別
            forceFallback: true  // 忽略HTML5 DnD
        });
    }
});

// 數字欄位 加一
$('form').on('click', '.number-plus',function(){
    // 不含有disabled才能操作
    if ($(this).parents('.number_box').find('input').attr("disabled") == undefined) {
        number = $(this).parents('.number_box').find('input');

        if ($(number).val() == '') {

            $(number).val(0);
        }

        $(number).val(parseInt($(number).val()) + 1);

        // 是否有設定最大數字
        if (parseInt($(number).attr('max')) && $(number).val() > parseInt($(number).attr('max'))) {
            $(number).val(parseInt($(number).attr('max')))
        }

        // 是否有設定最小數字
        if (parseInt($(number).attr('min')) && $(number).val() < parseInt($(number).attr('min'))) {
            $(number).val(parseInt($(number).attr('min')))
        }

        // 觸發onchange
        $(number).trigger('onchange');
    }
});

// 數字欄位 減一
$('form').on('click', '.number-less',function(){
    if ($(this).parents('.number_box').find('input').attr("disabled") == undefined) {
        number = $(this).parents('.number_box').find('input');
        if ($(number).val() == '') {

            $(number).val(0);
        }

        $(number).val(parseInt($(number).val()) - 1);

        // 是否有設定最大數字
        if (parseInt($(number).attr('max')) && $(number).val() > parseInt($(number).attr('max'))) {
            $(number).val(parseInt($(number).attr('max')))
        }

        // 是否有設定最小數字
        if ((parseInt($(number).attr('min')) || parseInt($(number).attr('min')) == 0) && $(number).val() < parseInt($(number).attr('min'))) {
            $(number).val(parseInt($(number).attr('min')))
        }

        // 觸發onchange
        $(number).trigger('onchange');
    }
});

// 確保數字部會超過最大數字或低於最小數字
$('.number-input-max').change(function() {

    number = $(this);

    // 是否有設定最大數字
    if (parseInt($(number).attr('max')) && $(number).val() > parseInt($(number).attr('max'))) {
        $(number).val(parseInt($(number).attr('max')))
    }

    // 是否有設定最小數字
    if ((parseInt($(number).attr('min')) || parseInt($(number).attr('min')) == 0) && $(number).val() < parseInt($(number).attr('min'))) {
        $(number).val(parseInt($(number).attr('min')))
    }
});



