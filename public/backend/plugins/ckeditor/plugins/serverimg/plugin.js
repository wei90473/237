CKEDITOR.plugins.add(
    "serverimg",
    {
        requires: ["dialog"],
        init: function (editor) {
            editor.addCommand("serverimg", new CKEDITOR.dialogCommand("serverimg"));
            editor.ui.addButton(
                "serverimg",
                {
                    label: "上傳圖片",
                    command: "serverimg",
                    icon:  this.path + "images/upload.svg",
                    toolbar: 'insert'
                });
            CKEDITOR.dialog.add("serverimg", this.path +  "dialogs/code.js");
        }
    }
);