<?php

namespace Dcat\Admin\Scheduling\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Admin;
//use http\Client\Request;
use Illuminate\Routing\Controller;
use Dcat\Admin\Scheduling\Repositories\CommandsLists;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Show;
use Dcat\Admin\Scheduling\Scheduling;
use Illuminate\Http\Request;
use Dcat\Admin\Widgets\Tab;
use Dcat\Admin\Widgets\Alert;
use Dcat\Admin\Widgets\Box;
use Illuminate\Contracts\Support\Renderable;
use Dcat\Admin\Widgets\Card;

class SchedulingController extends Controller
{


    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Content $content)
    {
        $this->loadScript();
        return $content
            ->title('定时任务管理')
            ->description('')
            ->breadcrumb(['text' => '任务管理', 'url' => ''])
            ->body($this->pageMain());
    }
    // 页面
    public function pageMain(){
        $req = Request()->all();
        $type = request('_t', 1);

        $tab = Tab::make();
        // 第一个参数是选项卡标题，第二个参数是内容，第三个参数是是否选中
        $tab->add('列表管理',$this->tab0());
        $tab->add('日志',$this->tab1());
        $tab->add('配置信息',$this->tab2());
        $tab->add('Larevel任务调度文档',$this->tab3());
        $tab->add('作者',$this->tab4());
        // 添加选项卡链接
        //$tab->addLink('Larevel任务调度文档', 'https://www.kancloud.cn/tonyyu/laravel_5_6/786249');
        return $tab->withCard();
    }


    public function tab0(){
        $grid =  Grid::make(new CommandsLists, function (Grid $grid) {
            $grid->column('id','#');
            $grid->column('task','命令')->display(function (){
                $html = '<span class="label" style="background:#f9f2f4;color:#c7254e">'.$this->task.'</span>';
                return $html;
            });
            $grid->column('expression','执行规则')->help('cron')->display(function (){
                $html = '<span class="label" style="background:#21b978">'.$this->expression.'</span>';
                return $html;
            });
            $grid->column('nextRunDate','下次时间');
            $grid->column('description','描述');
            $grid->column('logFile','查看日志')->display(function (){
                $htmls = "<a href='/admin/scheduling/logview?logfile=" . $this->logFile . "'>查看</a>";
                return $htmls;
            });
            $grid->column('logFile', '查看日志')->display(function ($e){
                $htmls = "<a href='javascript:void(0);' class='scheduling-log-view' data-logFile='$this->logFile'>查看</a>";
                return $htmls;
            });
            //$grid->column('action','操作');
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->disableBatchDelete();
            $grid->tools($this->schedulingRun());
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $id =  $actions->row->id;
                $task = urlencode($actions->row->task);
                // 去掉删除
                $actions->disableDelete();
                // 去掉编辑
                $actions->disableEdit();
                $actions->disableView();
                $actions->append("<a href='javascript:void(0);' class='btn btn-block bg-gradient-success btn-xs scheduling-task-run' data-task='$task' data-taskid='$id'><i class='fa fa-flash' style='font-size: 16px'></i> 运行</a>");

            });
        });
        $htmll = <<<HTML
<ul>
    <li>在服务器crontab上添加一行以下语句</li>
    <li>* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1</li>
    <li>直观的查看管理定时任务</li>
    <li>避免任务重复:默认情况下，即使之前的任务还在执行，调度内任务也会执行。你可以使用 withoutOverlapping() 方法来避免这种情况</li>
    <li>描述信息是在这里添加：->description('咨询订单过期处理')</li>
</ul>
HTML;

        $alert = Alert::make($htmll, '提示:');
          // 这种使用方式，js为执行二次
        //return Content::make()->full()->row($alert->info())->row($grid);
        /*$row = Row::make(function (Row $row) {
            $row->column(6, function (Column $column) {
                $column->append('Left Column Content');
            });

            $row->column(6, function (Column $column) {
                $column->append('Right Column Content');
            });
        });*/
        /*$row = new Row();
        $row->column(12, $alert->info());
        $row->column(12,$grid);
        return $row;*/
        return $alert->info().$grid;
    }

    public function tab1(){

        $alert = Alert::make('<ul><li>暂未上线</li></ul>', '提示:');
        return $alert->info();
    }

    public function tab2(){
        $alert = Alert::make('<ul><li>暂未上线</li></ul>', '提示:');
        return $alert->info();
    }

    public function tab3(){
        $files = public_path('vendor/dcat-admin-extensions/ycookies/scheduling/schedule.md');
        $files_contents = file_get_contents($files);
        $markdown_text = \Illuminate\Mail\Markdown::parse(e($files_contents));
        $markdown_text = htmlspecialchars_decode($markdown_text);
        return Admin::view('ycookies.scheduling::docs', compact('markdown_text','files'));
    }

    /**
     * @desc 作者展示
     * @return Card
     */
    public function tab4(){
        return Card::make('作者展示', Admin::view('ycookies.api-tester::author'));
    }

    public function schedulingRun(){
        return "<button class='btn btn-white scheduling-run'> &nbsp;&nbsp;&nbsp;<i class='feather icon-zap'></i>&nbsp;启动一次调度器&nbsp;&nbsp;&nbsp; </button>&nbsp;";
    }

    // 查看日志
    public function logview(Content $content){
        $list = [];
        return $content->full()->body(Admin::view('ycookies.scheduling::logview', compact('list')));
    }

    // 打开页面
    public function runpage(Content $content){
        $request = Request();
        $taskid = $request->get('taskid');
        $task = $request->get('task','');
        return $content->full()->body(Admin::view('ycookies.scheduling::scheduling-run', compact('task')));
    }

    // 运行命令
    public function taskrun(Content $content){
        $request = Request();
        $taskid = $request->get('taskid');
        $task = $request->get('task');
        $list = [];
        return $content->full()->body(Admin::view('ycookies.scheduling::taskrun', compact('list','task','taskid')));
    }

    // 执行 artisan schedule:run
    public function run(Request $request){
        //$output = '';
        $output = \Artisan::call('schedule:run');
        return [
            'status'    => true,
            'message'   => 'success',
            'data'      => $output,
        ];
    }
    /**
     * @param Request $request
     *
     * @return array
     */
    public function runEvent(Request $request)
    {
        $scheduling = new Scheduling();

        try {
            $output = $scheduling->runTask($request->get('id'));

            return [
                'status'    => true,
                'message'   => 'success',
                'data'      => $output,
            ];
        } catch (\Exception $e) {
            return [
                'status'    => false,
                'message'   => 'failed',
                'data'      => $e->getMessage(),
            ];
        }
    }

    public function loadScript(){
        Admin::script(
            <<<SCRIPT
$('.scheduling-log-view').click(function () {
        var logFile = $(this).attr('data-logFile');
        layer.open({
            type: 2,
            title: '查看执行日志',
            area: ['65%', '80%'],
            content: '/admin/scheduling/logview?logFile='+logFile,
        });
    });
    $('.scheduling-run').click(function () {
        layer.open({
            type: 2,
            title: '执行 scheduling-run',
            area: ['65%', '80%'],
            content: '/admin/scheduling-run-page',
        });
    });

    $('.scheduling-task-run').click(function () {
        var taskid = $(this).attr('data-taskid');
        var task = $(this).attr('data-task');
        layer.open({
            type: 2,
            title: '执行命令',
            area: ['65%', '80%'],
            content: '/admin/scheduling/taskrun?taskid=' + taskid + '&task=' + task,
        });
    });
SCRIPT

        );
    }

    protected $sendOutputTo;

}