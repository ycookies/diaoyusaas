function setCookiek(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}
function getCookiek(name) {
    var nameEQ = name + "=";
    var cookies = document.cookie.split(';');
    for(var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        while (cookie.charAt(0) == ' ') {
            cookie = cookie.substring(1, cookie.length);
        }
        if (cookie.indexOf(nameEQ) == 0) {
            return cookie.substring(nameEQ.length, cookie.length);
        }
    }
    return null;
}
$(function() {
    $('.nav-items').on('click',function (d) {
        $(this).parent('.navbar-left').find('.nav-items').removeClass('active');
        $(this).addClass('active');
        var id = $(this).attr('data-id');
        var idss = 'parent_id_'+id;
        setCookiek('menu_parent_id',id,365);

        $('.nav-sidebar').children('li').removeClass('show-menu');
        $('.nav-sidebar').children('li').removeClass('menu-open');
        $('#'+idss).addClass('show-menu');
        $('#'+idss).addClass('menu-open');
    })
});
$(document).ready(function(){
    var menu_parent_id = getCookiek('menu_parent_id');
    $('.nav-sidebar').children('li').removeClass('show-menu');
    if(menu_parent_id != undefined){
        var idss = 'parent_id_'+menu_parent_id;
        $('#'+idss).addClass('show-menu');
        $('#'+idss).addClass('menu-open');
    }else{
        $('.nav-sidebar').children('li:first').addClass('show-menu')
    }
});

/**
 * 全屏
 */
$('body').on('click', '[data-check-screen]', function () {
    var check = $(this).attr('data-check-screen');
    if (check == 'full') {
        openFullscreen();
        $(this).attr('data-check-screen', 'exit');
        $(this).html('<i class="feather icon-minimize"</i>');
    } else {
        closeFullscreen();
        $(this).attr('data-check-screen', 'full');
        $(this).html('<i class="feather icon-maximize"></i>');
    }
});
// 进入全屏
function openFullscreen() {
    var elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.mozRequestFullScreen) { /* Firefox */
        elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) { /* IE/Edge */
        elem.msRequestFullscreen();
    }
}

// 退出全屏
function closeFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) { /* Firefox */
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE/Edge */
        document.msExitFullscreen();
    }
}
function downimg(imgurl) {
    var imageUrl = imgurl;

    // 从 URL 中提取文件名
    var fileName = getFileNameFromUrl(imageUrl);
    // 使用 XMLHttpRequest 获取图片
    var xhr = new XMLHttpRequest();
    xhr.open('GET', imageUrl, true);
    xhr.responseType = 'blob'; // 设置响应类型为 Blob

    xhr.onload = function () {
        if (xhr.status === 200) {
            // 创建一个隐藏的<a>元素
            var link = document.createElement('a');
            link.href = URL.createObjectURL(xhr.response); // 将 Blob 转换为 URL
            link.download = fileName; // 设置下载的文件名

            // 将<a>元素添加到文档中
            document.body.appendChild(link);

            // 模拟点击<a>元素
            link.click();

            // 移除<a>元素
            document.body.removeChild(link);

            // 释放 Blob URL
            URL.revokeObjectURL(link.href);
        } else {
            console.error('保存图片失败: HTTP 状态码 ' + xhr.status);
        }
    };

    xhr.onerror = function () {
        console.error('保存图片失败: 网络错误');
    };

    xhr.send();
}

/**
 * 从 URL 中提取文件名
 * @param {string} url - 图片的 URL
 * @returns {string} - 文件名
 */
function getFileNameFromUrl(url) {
    // 使用 URL 对象解析 URL
    var urlObj = new URL(url);
    // 获取路径部分（例如 '/path/to/your/image.png'）
    var pathname = urlObj.pathname;
    // 提取文件名（例如 'image.png'）
    var fileName = pathname.split('/').pop(); // 获取最后一个部分
    return fileName;
}
