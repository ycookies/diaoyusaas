<?php

namespace Dcat\Admin\Scheduling\Repositories;

use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Scheduling\CronSchedule;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Support\Str;
use Dcat\Admin\Grid;
use Dcat\Admin\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;
use Dcat\Admin\Scheduling\Scheduling;
class CommandsLists extends Repository
{
    /**
     * 定义主键字段名称
     *
     * @return string
     */
    public function getPrimaryKeyColumn()
    {
        return 'id';
    }

    /**
     * 查询表格数据
     *
     * @param Grid\Model $model
     * @return LengthAwarePaginator
     */
    public function get(Grid\Model $model)
    {
        // 当前页数
        $currentPage = $model->getCurrentPage();
        // 每页显示行数
        $perPage = $model->getPerPage();

        // 获取排序字段
        [$orderColumn, $orderType] = $model->getSort();

        // 获取"scope"筛选值
        $city = $model->filter()->input($model->filter()->getScopeQueryName(), '广州');

        // 如果设置了其他过滤器字段，也可以通过“input”方法获取值，如：
        $title = $model->filter()->input('title');
        if ($title !== null) {
            // 执行你的筛选逻辑

        }

        $start = ($currentPage - 1) * $perPage;
        $task_arr = (new Scheduling())->getTasks();
        $list = [];
        foreach ($task_arr as $key => $items) {
            $list[] = [
                'id' => ($key+1),
                'task'          => $items['task']['name'],
                'expression'    => $items['expression'],
                'nextRunDate'   => $items['nextRunDate'],
                'description'   => $items['description'],
                'readable' => $items['readable'],
                'logFile'      => '', // 日志文件位置
            ];
        }


        $data['total'] = count($list);
        $data['subjects'] = $list;

        return $model->makePaginator(
            $data['total'] ?? 0, // 传入总记录数
            $data['subjects'] ?? [] // 传入数据二维数组
        );
    }
}
