/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    // 增加上傳圖片功能
    config.extraPlugins = 'serverimg';

    config.toolbar = [
        { name: 'styles',      items : [ 'FontSize' ] },
        { name: 'colors',      items : [ 'TextColor','BGColor' ] },
        { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript' ] },
        { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
        { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
        { name: 'insert',      items : [ 'serverimg','Image','Table','HorizontalRule' ] },
        // { name: 'tools',       items : [ 'Maximize', 'ShowBlocks' ] }
    ];

    // Toolbar groups configuration.
    config.toolbarGroups = [
        { name: 'styles' },
        { name: 'colors' },
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
        { name: 'forms' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
        { name: 'links' },
        { name: 'insert' },
        { name: 'tools' },
    ];

    config.height = 600;//編輯器的高度

    // 禁止設置圖片
    config.disallowedContent = 'img{width,height,border-width,margin-left,margin,margin-top,float,data-cke-saved-href}';
    // 禁止圖片的其他頁籤
    // config.removeDialogTabs = 'image:Link;image:advanced';

    // 編輯器enter換行前台不要跑出<br>
    CKEDITOR.on( 'instanceReady', function( ev ){
        with (ev.editor.dataProcessor.writer) {
            setRules("p",  {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("h1", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("h2", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("h3", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("h4", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("h5", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("div", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("table", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("tr", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("td", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("iframe", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("li", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("ul", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
            setRules("ol", {indent : false, breakBeforeOpen : false, breakAfterOpen : false, breakBeforeClose : false, breakAfterClose : false} );
        }
    })
};
