var uploaderImageFilter = {title: "Image files", extensions: "jpg,jpeg,gif,png,bmp",extensions2 : "accept=\"image/jpg,image/jpeg,image/png,image/gif,image/bmp\""};
var uploaderZipFilter = {title: "Zip files", extensions: "zip,rar"};
var uploaderVideoFilter = {title: "video files", extensions: "mp4,avi,rmvb,wma,rm,flash,mov,HEVC,hevc",extensions2:"accept=\"video/*\" capture=\"camcorder\""};
var uploaderImageFilterAll = [ //只允许上传图片和zip文件
    {title: "Image files", extensions: "jpg,jpeg,gif,png,bmp"},
    {title: "Zip files", extensions: "zip,rar"},
    {title: "Docs files", extensions: "doc,docx,word,pptx,xls,xlsx,txt,pdf"},
    {title: "video files", extensions: "mp4,avi,rmvb,wma,rm,flash,mov,HEVC,hevc"}
];
accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
callbackbody = ''
filename = ''
key = ''
expire = 0
g_object_name = ''
g_object_name_type = ''
now = timestamp = Date.parse(new Date()) / 1000;
// 没有成功回周函数的上传
function getUploader(btnid,filter,single = false,dirpath='case/',field = 'filelist[]') { //这里定义创建upload实例化函数。接收三个参数，dom元素ID值，成功回调函数，文件过滤
    var uploader = new plupload.Uploader({
        browse_button: btnid,// id值
        multi_selection: false,
        flash_swf_url: 'lib/plupload-2.1.2/js/Moxie.swf',
        silverlight_xap_url: 'lib/plupload-2.1.2/js/Moxie.xap',
        url: 'http://oss.aliyuncs.com',//上传路径一般不会变
        filters: {
            mime_types:filter,  //选择对应的文件类型
            max_file_size: '1000mb', //最大只能上传10mb的文件
            prevent_duplicates: true //不允许选取重复文件
        }
    });
    uploader.init();
    //图片选择完毕触发
    uploader.bind('FilesAdded', function (uploader, files) {
        //alert(file.name);
        //选取文件后直接上传
        //uploader.start();
        var $jq = jQuery.noConflict();
        plupload.each(files, function (file) {
            var htmls1 = '<li class="upfiled" ><div  id="' + file.id + '"><b></b>'
                + '<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
                + '</div></li>';
            if(single){
                $jq('#'+btnid).parents('.up-list').find('.upfiled').remove();
                $jq('#'+btnid).parents('.up-list').prepend(htmls1);
            }else{
                $jq('#'+btnid).parents('.up-list').prepend(htmls1);
            }
            g_object_name_type = 'random_name'; //  本地文件名，local_name还是随机文件名random_name
            set_upload_param(uploader, file.name, false);
        });

    });
    // 当队列中的某一个文件正要开始上传前触发监听函数参数
    uploader.bind('BeforeUpload',function (up, file) {

        //set_upload_param(up, file.name, true);
    })
    uploader.bind('UploadProgress', function (up, file) {
        var d = document.getElementById(file.id);
        d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
        var prog = d.getElementsByTagName('div')[0];
        var progBar = prog.getElementsByTagName('div')[0]
        progBar.style.width = 2 * file.percent + 'px';
        progBar.setAttribute('aria-valuenow', file.percent);
    });

    //图片上传成功触发，ps:data是返回值（第三个参数是返回值）//success(uploader,files,data)
    uploader.bind('FileUploaded', function (up, file, info) {
        var $jq = jQuery.noConflict();
        if (info.status == 200) {
            var domain = document.domain;
            if(domain ==  'crm.xidakeji.cn'){
                var url = 'https://dsxiacase.oss-cn-hangzhou.aliyuncs.com/' + get_uploaded_object_name(file.name);
            }else{
                var url = 'https://shren2.oss-cn-hangzhou.aliyuncs.com/' + get_uploaded_object_name(file.name);
            }
            $jq('#' + file.id).html('');
            var imgshow = '<input type="hidden" name="'+field+'" value="' + url + '" /><div class="image-close">X</div><span> <img data-preview-src="'+url+'" data-preview-group="1"  src="' + url + '" onerror="imgErrorHandle(this)" /> </span>';
            $jq('#' + file.id).html(imgshow);
            $jq('.image-close').on('click', function (e) {
                $jq(this).parent().parent().remove();
            });

            //mui.previewImage();
            //success(url,file.id);//成功回调函数 接收图片回显的url。我的url是之前生成的文件名，拼接阿里云文件读取路径
        } else if (info.status == 203) {
            //layer.msg("上传成功，回调服务器失败"+info.response);
            //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '上传到OSS成功，但是oss访问用户设置的上传回调服务器失败，失败原因是:' + info.response;
        } else {
            toastShow(info.response);
            //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
        }
    });

    // 图片上传触发
    uploader.bind('Error', function (up, err) {
        if (err.code == -600) {
            toastShow("选择的文件太大了");
        } else if (err.code == -601) {
            toastShow("选择的文件类型不对");
        } else if (err.code == -602) {
            toastShow("这个文件已经上传过一遍了");
        }else{
            toastShow('上传发生错误:'+err.code);
        }
    });
}

//供应所有页面调用的函数。接收一个数组。里面我选的是{id,function,filter}.依次创建
function setUploader(array) {
    for (var i = 0; i < array.length; i++) {
        var obj = array[i];
        getUploader(obj.id, obj.filter,obj.single,obj.dir,obj.field);
    }
}

function send_request() {
    var xmlhttp = null;
    if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    if (xmlhttp != null) {
        // serverUrl是 用户获取 '签名和Policy' 等信息的应用服务器的URL，请将下面的IP和Port配置为您自己的真实信息。
        var domain = document.domain;
        serverUrl = document.location.protocol+'//'+domain+'/oss/getSts';

        xmlhttp.open("GET", serverUrl, false);
        xmlhttp.send(null);
        return xmlhttp.responseText
    } else {
        alert("您的浏览器不支持XMLHTTP.");
    }
};

// 本地文件名，还是随机文件名
function check_object_radio() {
    g_object_name_type = 'local_name';
    return false;
    var tt = document.getElementsByName('myradio');
    for (var i = 0; i < tt.length; i++) {
        if (tt[i].checked) {
            g_object_name_type = tt[i].value;
            break;
        }
    }
}

function get_signature() {
    // 可以判断当前expire是否超过了当前时间， 如果超过了当前时间， 就重新取一下，3s 作为缓冲。
    now = timestamp = Date.parse(new Date()) / 1000;
    if (expire < now + 3) {
        body = send_request()
        var obj = eval("(" + body + ")");
        host = obj['host']
        policyBase64 = obj['policy']
        accessid = obj['accessid']
        signature = obj['signature']
        expire = parseInt(obj['expire'])
        callbackbody = obj['callback']
        key = obj['dir']
        return true;
    }
    return false;
};

function random_string(len) {
    len = len || 32;
    var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    var maxPos = chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    pos = filename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}

function calculate_object_name(filename) {
    if (g_object_name_type == 'local_name') {
        g_object_name += "${filename}"
    } else if (g_object_name_type == 'random_name') {
        suffix = get_suffix(filename)
        g_object_name = key + random_string(10) + suffix
    }
    return ''
}

function get_uploaded_object_name(filename) {
    if (g_object_name_type == 'local_name') {
        tmp_name = g_object_name
        tmp_name = tmp_name.replace("${filename}", filename);
        return tmp_name
    } else if (g_object_name_type == 'random_name') {
        return g_object_name
    }
}

function set_upload_param(up, filename, ret) {
    if (ret == false) {
        ret = get_signature()
    }
    g_object_name = key;
    if (filename != '') {
        suffix = get_suffix(filename)
        calculate_object_name(filename)
    }
    new_multipart_params = {
        'key': g_object_name,
        'policy': policyBase64,
        'OSSAccessKeyId': accessid,
        'success_action_status': '200', //让服务端返回200,不然，默认会返回204
        'callback': callbackbody,
        'signature': signature,
    };

    up.setOption({
        'url': host,
        'multipart_params': new_multipart_params
    });

    up.start();
}
// 如果图片加载出错的处理
function imgErrorHandle(e) {
    var $jq = jQuery.noConflict();
    var imgpath = $jq(e).attr('src');
    console.log(imgpath);
    if(imgpath == ''){
        //$(e).attr('src','/assets/assets/images/l_load.png');
        //$(e).attr('data-src','/assets/assets/images/l_load.png');
        return false;
    }
    var img1 = '';
    var fileExtension = imgpath.substring(imgpath.lastIndexOf('.') + 1);
    console.log(fileExtension);
    if(fileExtension == 'xlsx' || fileExtension == 'xls'){
        img1 = '/assets/images/excel-icon.png';
    }else if(fileExtension == 'zip'){
        img1 = '/assets/images/zip-icon.png';
    }else if(fileExtension == 'pdf'){
        img1 = '/assets/images/pdf-icon.png';
    }else if(fileExtension == 'word'){
        img1 = '/assets/images/word-icon.png';
    }else if(fileExtension == 'rar'){
        img1 = '/assets/images/rar-icon.png';
    }else if(fileExtension == 'docx'){
        img1 = '/assets/images/doc-icon.png';
    }else if(fileExtension == 'doc'){
        img1 = '/assets/images/doc-icon.png';
    }else if(fileExtension == 'mp4'){
        img1 = '/assets/images/mp4-icon.png';
    }else if(fileExtension == 'jpg' || fileExtension == 'jpeg' || fileExtension == 'png'){
        img1= '/assets/images/notimg.png';
    }else{
        img1= '/assets/images/notimg.png';
    }
    $jq(e).attr('src',img1);
    $jq(e).attr('data-src',img1);
}