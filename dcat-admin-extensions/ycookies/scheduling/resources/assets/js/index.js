(function (w, $) {
    function ExtensionDemo(options) {
        this.options = $.extend({
            $el: $('.demo'),
        }, options);

        this.init(this.options);
    }

    ExtensionDemo.prototype = {
        init: function (options) {
            options.$el.on('click', function () {
                Dcat.success($(this).text());
            });

            console.log('Done.');
        },
    };

    $.fn.extensionDemo = function (options) {
        options = options || {};
        options.$el = $(this);

        return new ExtensionDemo(options);
    };
    $('.scheduling-log-view').click(function () {
        var logFile = $(this).attr('data-logFile');
        layer.open({
            type: 2,
            title: '查看执行日志',
            area: ['65%', '80%'],
            content: '/admin/scheduling/logview?logFile='+logFile,
        });
    });

    $('.scheduling-task-run').click(function () {
        var taskid = $(this).attr('data-taskid');
        var task = $(this).attr('data-task');
        layer.open({
            type: 2,
            title: '执行命令',
            area: ['65%', '80%'],
            content: '/admin/scheduling/taskrun?taskid='+taskid+'&task='+task,
        });
    });

})(window, jQuery);