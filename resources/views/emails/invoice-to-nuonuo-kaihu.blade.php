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
        <h3 class="title">融宝科技 商户数电发票开户</h3>
    </div>
    <div class="page_content">
        <div class="order-info-box">
            <div>
                销方公司名：{{$info->salerName}} <br/>
                销方税号：{{$info->salerTaxNum}}<br/>
                销方开户行及账号：{{$info->salerAccount}}<br/>
                销方地址电话：{{$info->salerAddress}}<br/>
                发票种类：{{$info->invoiceLine}}<br/>
                开票人：{{$info->clerk}}<br/>
                复核人：{{$info->checker}}<br/>
                收款人：{{$info->payee}}<br/>
                分机号：{{$info->extensionNumber}}<br/>
                跨地市标志：{{$info->InterCityIndicator}}<br/>
                产权证书：{{$info->PropertyOwnershipCertificate}}<br/>
                面积单位：{{$info->AreaUnit}}<br/>
                区域：{{$info->region}}<br/>
                详细地址：{{$info->salerAddress_as}}<br/>
                发票种类：{{$info->invoiceLine_as}}<br/>
                是否具备数电能力：{{$info->is_shudian}}<br/>
                特定要素特定要素字段：{{$info->specificFactor}}<br/>
                优惠政策标识：{{$info->favouredPolicyFlag}}<br/>
                商品编号：{{$info->goodCode}}<br/>
                商品名称：{{$info->goodName}}<br/>
                电费税率：{{$info->taxRate}}<br/>
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
