<style>
    .title{
        text-align: center;
        font-size: 26px;
        font-weight: bold;
    }
    .order-info-box{
        margin: 30px 10px;
        font-size: 14px;
    }
</style>
<div class="page">
    <div class="page_title">
        <h3 class="title">酒店住宿费用发票</h3>
    </div>
    <div class="page_content">

        <div class="order-info-box">
            <div >请在7日内下载</div>
            <div>
                消费订单号：{{$record->goods_order_no}}<br/>
                发票下载地址：{{$record->downloadUrl}} <br/>
            </div>
            {{--<ul>
                <li>
                    订单号：2XXXXXXXX
                </li>
                <li>
                    订房金额：XXXX
                </li>
            </ul>--}}
        </div>
    </div>
</div>
