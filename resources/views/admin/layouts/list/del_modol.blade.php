<!-- 刪除確認視窗 -->
<div id="del_modol" class="modal fade displaynone" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    {!! Form::open([ 'method'=>'delete', 'url'=>'', 'id'=>'del_form' ]) !!}
                    <button type="button" class="btn mr-2 btn-info pull-left" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn mr-3 btn-danger">確定</button>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>