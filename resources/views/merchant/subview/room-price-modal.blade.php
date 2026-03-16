<div class="modal fade" id="room-price-modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">修改价格</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="room-price-form" action="{{url('/merchant/calendar-price-save')}}" method="POST">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">当天日期</label>
                        <div class="col-sm-8">
                            <div id="room_days_time"></div>
                        </div>
                    </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">当日价格(元)</label>
                    <div class="col-sm-8">
                        <input type="hidden" name="days" id="days" value="">
                        <input type="hidden" name="type" id="type" value="">
                        <input type="hidden" name="room_id" id="room_id" value="">
                        <input type="number" class="form-control form-control-sm" name="price" id="price" placeholder="请填写">
                        <div class="invalid-feedback">
                            填写最多二位小数的正数值
                        </div>
                    </div>
                </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">开放状态</label>
                        <div class="col-sm-8">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="open_status"  value="1">
                                <label class="form-check-label" for="inlineRadio1">正常</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="open_status"  value="0">
                                <label class="form-check-label" for="inlineRadio2">关闭</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary price-save">保存</button>
            </div>
        </div>
    </div>
</div>