<style>
    #case_show_page .nav > li > a {
        padding: 8px 10px;
        margin-bottom: -2px;
    }

    .page-nav:after {
        content: "\f078"; /** 箭头向上图标*/
        font-family: FontAwesome;
    }

    /**箭头图标展开式时的样式*/
    /** collapsed是bootstrap的属性，表示父标签展开的时候加入的图标 after表示在此后加入字体图标  */
    .page-nav.collapsed:after {
        content: "\f054"; /** 箭头向下图标*/
        font-family: FontAwesome;
    }

    /*.images_zone{
        width: 80px;
        height: 80px;
    }*/
    .images_zone {
        display: inline-block;
    }

    .images_zone img {
        width: 80px;
        height: 80px;
        margin-top: 10px;
    }

    .images_zone .look_file {
        color: #aaa8a8;
    }

    .images_zone .docs {
        font-size: 8px;
        color: #dddddd;
    }

    .buy-panel {
        float: right;
    }

    .color-hse {
        font-size: 18px;
        font-weight: bold;
    }

    .total {
        font-size: 22px;
        font-weight: bold;
        color: #aa0000;
        text-align: right;
    }
</style>
<div class=row>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-gray">
                案件时间轴
            </div>
            <div class="card-body " style="background-color: #ebebeb;">
                @if(!empty($actionlog_list))
                <div class="timeline">

                        @foreach ($actionlog_list as $items)
                        <div>
                            <i class="fa fa-star bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{$items['created_at']}}</span>
                                <h3 class="timeline-header">{{$items['type_name']}}</h3>
                                <div class="timeline-body">
                                    {!! getContentRep($items['content']) !!}
                                </div>
                                <div class="timeline-footer">
                                    <span class="pull-right">
                                        @if(!empty($items['users']['first_name']))
                                            <p class="text-right">
                                                                                    操作者:{{$items['users']['first_name']}}</p>
                                        @endif
                                    </span>
                                    <div>&nbsp;&nbsp;&nbsp;</div>

                                </div>
                            </div>
                        </div>
                        @endforeach

                </div>
                @else
                    <span class="text text-dark">暂无信息</span>
                @endif
            </div>
        </div>

    </div>
<style>
    .f-color-t{color: #b6b0b0;}
    .countries_list td{
        height:auto;
    }
</style>
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-gray">
                案件信息
            </div>
            <div class="card-body">
                <table class="table table-sm countries_list" >
                    <tbody>
                    <tr>
                        <td class="f-color-t">案管编号</td>
                        <td class="fs15 fw700 text-right">
                            <span style="font-size: 22px;font-weight: bold;">{{$case->case_sysno}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">序号ID</td>
                        <td class=" text-right">
                            {{$case->case_id}}
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">归属者</td>
                        <td class="fs15 fw700 text-right">
                            <label class="label bg-blue"> {{$belonger ?? ''}}</label>

                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">案件类型</td>
                        <td class="fs15 fw700 text-right">{{$case->caseType}}
                            -> {{$case->caseSubType}}</td>
                    </tr>
                    <tr>
                        <td class="f-color-t">案件进度</td>
                        <td class="fs15 fw700 text-right">{{$case->case_status_name}}
                            -> {{$case->case_sub_status_name}} </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">案件名称</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->case_name)?$case->case_name:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">立案编号</td>
                        <td class="fs15 fw700 text-right">
                            <span style="font-size: 18px;font-weight: bold;color: red;">{{!empty($case->case_number)?$case->case_number:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">立案法院</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->court_name)?$case->court_name:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">创建时间</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->created_at)?$case->created_at:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">更新时间</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->updated_at)?$case->updated_at:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">案件标签</td>
                        <td class="fs15 fw700 text-right" style="width: 80%">
                            @php
                                if(!empty($case->tags)){
                                $tags_arr = explode(',',$case->tags);
                                foreach ($tags_arr as $name){
                                 echo "<span class ='badge badge-info'>$name</span>";
                                }
                                }
                            @endphp
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">建议最低赔偿金额</td>
                        <td class="fs15 fw700 text-right">
                            <b>
                                {{$case->lawsuit_money ? $case->lawsuit_money.' 元': '-'}}
                            </b>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">备注</td>
                        <td class="fs15 fw700 text-right" style="width: 80%;line-height:20px;">
                            <span style="line-height:20px;"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="description" colspan="2" style="max-height: 50px;word-wrap: break-word;max-width: 200px">
                            {!!  !empty($case->description)?$case->description:'-'!!}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h4 class="line_30" style="border-bottom: 1px solid #cccccc">案件附件</h4>
                @if(!$case_files->isEmpty())
                    @foreach ($case_files as $itemd)

                        <div class="images_zone" style="width: 80px;height: 100px;">
                            <input type="hidden" name="imgurl" value="{{env('FILE_SOURCE')}}{{$itemd->file_url}}">
                            <img style="width: 60px;max-height: 60px;" src="{{env('FILE_SOURCE')}}{{$itemd->file_url}}" onerror="imgErrorHandle(this)">
                            <div class="look_file" onclick="look_load(this)">查看下载</div>
                        </div>

                    @endforeach

                @else
                    暂无相关附件
                @endif

                <h4 class="line_30">代理律所</h4>
                <table class="table table-sm countries_list">
                    <tbody>
                    <tr>
                        <td class="f-color-t">接案律所</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($law_firm)?$law_firm:'-'}}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h4 class="line_30">原告信息</h4>
                <table class="table table-sm countries_list">
                    <tbody>
                    <tr>
                        <td class="f-color-t">客户</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->first_name)?$case->first_name:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">客户权利</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->rights_title)?$case->rights_title:'-'}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">品牌介绍</td>
                        <td class="fs15 fw700 text-right">
                            <span><a target="_blank" href="{{ route('clients.brandDesc', [$case->client_id]) }}">点击查看</a></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="f-color-t">原告信息</td>
                        <td class="fs15 fw700 text-right">
                            <span>{{!empty($case->party_name)?$case->party_name:'-'}}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <h4 class="line_30">被告信息</h4>
                <table class="table table-sm countries_list">
                    <tbody>
                    <tr>
                        <td>
                            <p class="subscri-ti-das">
                                <span class="f-color-t">平台： </span>{{$parties->dfdant_platform}}
                                <br/>
                                <span class="f-color-t">掌柜名： </span>{{!empty($parties->dfdant_nick) ? $parties->dfdant_nick :'-'}}
                                <br/>
                                <span class="f-color-t">店铺名： </span>{{!empty($parties->dfdant_shop_name) ? $parties->dfdant_shop_name :'-'}}
                                <br/>
                                <span class="f-color-t">店铺Id：</span>{{!empty($parties->dfdant_shop_id) ? $parties->dfdant_shop_id:'-'}}
                                <br/>
                                <span class="f-color-t">店铺链接：</span><a class="width-120 text1 tooltips"
                                                                       href="{{$parties->dfdant_shop_link}}"
                                                                       target="_blank"> {{$parties->dfdant_shop_link}}</a><br/>
                                {{--<span class="f-color-t">查询店铺内相关权利商品：</span> <br/>--}}
                                <span class="f-color-t">披露主体： </span>{{!empty($parties->dfdant_publish_name)? $parties->dfdant_publish_name:'-'}}
                                <br/>
                                <span class="f-color-t">披露电话： </span>{{!empty($parties->dfdant_publish_mobile) ? $parties->dfdant_publish_mobile :'-'}}
                                <br/>
                                <span class="f-color-t">披露身份证： </span>{{!empty($parties->dfdant_publish_id) ? $parties->dfdant_publish_id:'-'}}
                                <br/>
                                <span class="f-color-t">所在地区： </span>{{$parties->dfdant_country}} {{$parties->dfdant_state}} {{$parties->dfdant_city_id}} {{$parties->dfdant_address}}
                                <br/>
                                <span class="f-color-t">附加信息： </span>{{!empty($parties->appendinfo) ? $parties->appendinfo:'-'}}
                                <br/>
                            </p>
                        </td>
                    </tr>
                    {{--@if(count($respondent_and_advocate)>0 && !empty($respondent_and_advocate)) @php $i=1; @endphp @foreach($respondent_and_advocate as $value)
                        <tr>
                            <td>

                                <p class="subscri-ti-das">
                                    <span class="f-color-t">平台： </span>{{''.!empty($value['dfdant_platform'])?$value['dfdant_platform']:'-' }}
                                    <br/>
                                    <span class="f-color-t">掌柜名： </span>{{ ''.!empty($value['dfdant_nick'])?$value['dfdant_nick']:'-' }}
                                    <br/>
                                    <span class="f-color-t">店铺名： </span>{{ ''.!empty($value['dfdant_shop_name'])?$value['dfdant_shop_name']:'-' }}
                                    <br/>
                                    <span class="f-color-t">店铺Id：</span>{{ ''.!empty($value['dfdant_shop_id'])?$value['dfdant_shop_id']:'-' }}
                                    <br/>
                                    <span class="f-color-t">店铺链接：</span><a class="width-120 text1 tooltips"
                                                                           href="{{$value['dfdant_shop_link']}}"
                                                                           target="_blank">{{$value['dfdant_shop_link'] }}</a><br/>
                                    <span class="f-color-t">披露主体： </span>{{ ''.!empty($value['dfdant_publish_name'])?$value['dfdant_publish_name']:'-' }}
                                    <br/>
                                    <span class="f-color-t">披露电话： </span>{{ ''.!empty($value['dfdant_publish_mobile'])?$value['dfdant_publish_mobile']:'-' }}
                                    <br/>
                                    <span class="f-color-t">披露身份证： </span>{{ ''.!empty($value['dfdant_publish_id'])?$value['dfdant_publish_id']:'-' }}
                                    <br/>
                                    <span class="f-color-t">所在地区： </span>{{$value['dfdant_country']}}{{$value['dfdant_state']}}{{$value['dfdant_city_id']}}{{$value['dfdant_address']}}
                                    <br/>
                                    <span class="f-color-t">附加信息： </span>{{!empty($value['appendinfo'])?$value['appendinfo']:'-' }}
                                    <br/>
                                    <span class="f-color-t">商品数据： </span> << 已经移动到上面第一个位置 请注意>>

                                </p>

                            </td>
                        </tr>
                        @php $i++; @endphp @endforeach @endif--}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
