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
<table class="table table-striped">
    <thead>
    <tr>
        <th>序号</th>
        <th>材料类型</th>
        <th>材料文件</th>
        <th>相关备注</th>
        <th>日期时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    @if(!empty($docs_list_59) && !$docs_list_59->isEmpty())
        @foreach($docs_list_59 as $key => $itemss)
            @if(empty($itemss->file_list) && empty($itemss->info[0]['imgs0']) && empty($itemss->info[0]['imgs1']) && empty($itemss->cert_img) )
                @continue;
            @endif
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$itemss->docs_type_name}}</td>
                <td>
                    @if(!empty($itemss->file_list))
                        @foreach (explode('|',$itemss->file_list) as $itemm)
                            <div class="images_zone">
                                <input type="hidden" name="imgurl" value="{{switchFileUrl($itemm)}}">
                                <img src="{{env('FILE_SOURCE')}}/{{ str_replace('//up','/up',$itemm)  ?? '' }}" data-src="{{env('FILE_SOURCE')}}/{{ str_replace('//up','/up',$itemm) ?? '' }}" onerror="imgErrorHandle(this)" data-magnify="gallery" data-group="g1"  data-caption="案件材料">
                                <div class="look_file" onclick="look_load(this)">查看下载</div>
                            </div>
                        @endforeach
                    @else
                        @if(!empty($itemss->info[0]['imgs0']))
                            <div class="images_zone">
                                <input type="hidden" name="imgurl"
                                       value="{{switchFileUrl($itemss->info[0]['imgs0'] ?? '')}}">
                                <img src="{{env('FILE_SOURCE')}}{{@$itemss->info[0]['imgs0']}}" data-src="{{env('FILE_SOURCE')}}{{@str_replace('//up','/up',$itemss->info[0]['imgs0']) }}"
                                     onerror="imgErrorHandle(this)" data-magnify="gallery" data-group="g1" data-caption="案件材料">
                                <div class="look_file" onclick="look_load(this)">查看下载</div>
                            </div>
                        @endif

                        @if(!empty($itemss->info[0]['imgs1']))
                            <div class="images_zone">
                                <input type="hidden" name="imgurl"
                                       value="{{switchFileUrl($itemss->info[0]['imgs1'] ?? '')}}">
                                <img src="{{env('FILE_SOURCE')}}{{@$itemss->info[0]['imgs1']}}" data-src="{{env('FILE_SOURCE')}}{{@$itemss->info[0]['imgs1']}}"
                                     onerror="imgErrorHandle(this)" data-magnify="gallery" data-group="g1" data-caption="案件材料">
                                <div class="look_file" onclick="look_load(this)">查看下载</div>
                            </div>
                        @endif
                        @if(!empty($itemss->info[0]['imgs2']))
                            <div class="images_zone">
                                <input type="hidden" name="imgurl"
                                       value="{{switchFileUrl($itemss->info[0]['imgs2'] ?? '')}}">
                                <img src="{{env('FILE_SOURCE')}}{{@$itemss->info[0]['imgs2']}}" data-src="{{env('FILE_SOURCE')}}{{@$itemss->info[0]['imgs2']}}"
                                     onerror="imgErrorHandle(this)" data-magnify="gallery" data-group="g1" data-caption="案件材料">
                                <div class="look_file" onclick="look_load(this)">查看下载</div>
                            </div>
                        @endif
                        @if(!empty($itemss->cert_img))
                            <div class="images_zone">
                                <input type="hidden" name="imgurl"
                                       value="{{switchFileUrl($itemss->cert_img ?? '')}}">
                                <img src="{{showFileUrl($itemss->cert_img)}}" data-src="{{showFileUrl($itemss->cert_img)}}"
                                     onerror="imgErrorHandle(this)" data-magnify="gallery" data-group="g1" data-caption="案件材料">
                                <div class="look_file" onclick="look_load(this)">查看下载</div>
                            </div>
                        @endif
                        @if(!empty($itemss->tort_goods_img))
                            @foreach (explode(',',$itemss->tort_goods_img) as $itemm)
                                <div class="images_zone">
                                    <input type="hidden" name="imgurl" value="{{switchFileUrl($itemm)}}">
                                    <img src="{{showFileUrl($itemm)}}" data-src="{{showFileUrl($itemm)}}" onerror="imgErrorHandle(this)" data-magnify="gallery" data-group="g1"  data-caption="案件材料">
                                    <div class="look_file" onclick="look_load(this)">查看下载</div>
                                </div>
                            @endforeach
                        @endif
                    @endif
                </td>
                <td>{{@$itemss->info[0]['remarks']}}</td>
                <td>{{$itemss->created_at}}</td>
                <td>
                    {{--@if(\Auth::guard('admin')->user()->user_type == "Admin" || Auth::guard('admin')->user()->id == $itemss->user_id)
                        <a class="btn btn-xs btn-danger docs-del" data-id="{{$itemss->id}}">删除</a>
                    @endif--}}
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="5" style="text-align: center">暂无信息
            </td>
        </tr>
    @endif
    </tbody>
</table>
<script>
    // 如果图片加载出错的处理
    function imgErrorHandle(e) {
        var imgpath = $(e).attr('src');
        console.log(imgpath);
        if(imgpath == ''){
            $(e).attr('src','/assets/assets/images/l_load.png');
            $(e).attr('data-src','/assets/assets/images/l_load.png');
            return false;
        }
        var img1 = '';
        var fileExtension = imgpath.substring(imgpath.lastIndexOf('.') + 1);
        console.log(fileExtension);
        if(fileExtension == 'xlsx' || fileExtension == 'xls'){
            img1 = '/assets/assets/images/excel-icon.png';
        }else if(fileExtension == 'zip'){
            img1 = '/assets/assets/images/zip-icon.png';
        }else if(fileExtension == 'pdf'){
            img1 = '/assets/assets/images/pdf-icon.png';
        }else if(fileExtension == 'word'){
            img1 = '/assets/assets/images/word-icon.png';
        }else if(fileExtension == 'rar'){
            img1 = '/assets/assets/images/rar-icon.png';
        }else if(fileExtension == 'docx'){
            img1 = '/assets/assets/images/doc-icon.png';
        }else if(fileExtension == 'doc'){
            img1 = '/assets/assets/images/doc-icon.png';
        }else if(fileExtension == 'mp4'){
            img1 = '/assets/assets/images/mp4-icon.png';
        }else if(fileExtension == 'jpg' || fileExtension == 'jpeg' || fileExtension == 'png'){
            img1= '/assets/assets/images/notimg.png';
        }else{
            img1= '/assets/assets/images/l_load.png';
        }
        $(e).attr('src',img1);
        //$(e).attr('data-src',img1);
    }
    //查看下载
    function look_load(a) {
        var userAgent = navigator.userAgent;
        var url = $(a).parents(".images_zone").find("input").val();
        if(url.indexOf('http') ==  -1){
            url = '<?php echo e(env('APP_URL'), false); ?>/'+url;
        }
        url = url.replace('?x-oss-process=style/400x400.jpg','');
        if(userAgent.indexOf('Firefox')  > -1){
            window.location.href = url;
        }else{
            window.open(url);
        }
    }
</script>