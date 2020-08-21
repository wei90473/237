<!--  入口網站班級類別設定 -->
<!-- 班別類別 modal -->
<div class="modal fade bd-example-modal-lg classType" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog_120" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong id="popTitle">班別類別</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 60vh;overflow: auto;">
                <ul>
                <?php for($i = 0; $i < sizeof($classCategory); $i++) { ?>
                    
                    <?php if(isset($classCategory[$i+1]->indent)) { ?>
                        <?php if($classCategory[$i]->indent < $classCategory[$i+1]->indent) { ?>
                            <?php if($i == 0) { ?>
                                <ul id="treeview" class="filetree">
                            <?php } ?>

                            <?php if($classCategory[$i]->indent == 1) { ?>
                                    <li><span class="folder classType_item"><?=$classCategory[$i]->name?></span>
                            <?php } else { ?>
                                    <li><span class="folder classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span>
                            <?php } ?>  
                                        <ul> 
                        <?php } else if($classCategory[$i]->indent == $classCategory[$i+1]->indent) { ?>
                                    <li><span class="file classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span></li>
                        <?php } else if($classCategory[$i]->indent > $classCategory[$i+1]->indent) { ?>
                                            <li><span class="file classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span></li>
                            <?php for($j = 0; $j < $classCategory[$i]->indent-$classCategory[$i+1]->indent; $j++) { ?>
                                        </ul>
                                    </li> 
                            <?php } ?>
                                    
                                
                        <?php } ?>
                    <?php } else { ?>
                                    <li><span class="file classType_item" onclick="chooseType('<?=$i?>', '<?=$classCategory[$i]->category?>','<?=$classCategory[$i]->name?>')"><?=$classCategory[$i]->name?></span></li>
                                </ul>
                    <?php } ?>
                <?php } ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="confirmClassType()">確定</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>


<script>
   // 選擇班別類別
   function chooseClassType() {
        $(".classType").modal('show');
   }
   // 選擇班別類別
   let classType = "";
   let className = "";
    function chooseType(index, code,name) {
        $('.classType_item').css('background-color', '');
        $('.classType_item').eq(index).css('background-color', '#ffe4c4');
        classType = code;
        className = name;
    }

    // 確認班別類別
    function confirmClassType() {
        $("#category2").val(className);
        $("#category").val(classType);
        $("#category").click();
    }
    // 初始化階層樹
    setTimeout(() => {
        $("#treeview").treeview({
            persist: "location",
            collapsed: true,
            unique: false,
            toggle: function() {
                // console.log("%s was toggled.", $(this).find(">span").text());
            }
        });
    }, 1000);        
</script>