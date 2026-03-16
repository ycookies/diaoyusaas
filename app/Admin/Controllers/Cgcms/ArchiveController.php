<?php

namespace App\Admin\Controllers\Cgcms;

use App\Admin\Actions\Tree\ViewArctypeList;
use App\Admin\Repositories\Cgcms\Archive;
use App\Admin\Repositories\Cgcms\DownloadFile;
use App\Models\Cgcms\Archive as ArchiveModel;
use App\Models\Cgcms\Arctype as ArctypeModel;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Dcat\Admin\Show;
use Dcat\Admin\Tree;
use Dcat\Admin\Widgets\Modal;

// 列表
class ArchiveController extends AdminController {

    public $channel; // 内容模型

    public $typeid; // 内容分类ID
    /**
     * page index
     */
    public function index(Content $content) {
        return $content
            ->header('列表')
            ->description('全部')
            ->breadcrumb(['text' => '列表', 'uri' => ''])
            ->body(function (Row $row) {
                $tree = new Tree(new ArctypeModel);
                $tree->expand(false);
                $tree->disableCreateButton();
                $tree->disableQuickCreateButton();
                $tree->disableDeleteButton();
                $tree->disableEditButton();
                //$tree->disableQuickEditButton();
                $tree->disableSaveButton();
                $tree->disableRefreshButton();
                $tree->branch(function ($branch) {
                    return $branch['title'];
                });
                $tree->actions(function (Tree\Actions $actions) {
                    //$actions->edit(false);
                    $actions->prepend(new ViewArctypeList());
                    $actions->disableEdit();
                    $actions->disableQuickEdit();
                    $actions->disableDelete();
                });
                $row->column(2, $tree);

                $row->column(10, function (Column $column) {
                    $column->append($this->grid());
                });
            });
        //->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        return Grid::make(Archive::with('types'), function (Grid $grid) {
            $grid->model()->setConstraints(['typeid' => \Request::get('typeid')])->orderBy('id', 'DESC');
            $grid->column('id');
            $grid->column('title');
            $grid->column('types.title', '栏目');
            $grid->column('created_at');
            //$grid->fie
            /*$grid->column('typeid');
            $grid->column('stypeid');
            $grid->column('channel');
            $grid->column('is_b');
            $grid->column('subtitle');
            $grid->column('litpic');
            $grid->column('is_head');
            $grid->column('is_special');
            $grid->column('is_top');
            $grid->column('is_recom');
            $grid->column('is_jump');
            $grid->column('is_litpic');
            $grid->column('is_roll');
            $grid->column('is_slide');
            $grid->column('is_diyattr');
            $grid->column('origin');
            $grid->column('author');
            $grid->column('click');
            $grid->column('arcrank');
            $grid->column('jumplinks');
            $grid->column('ismake');
            $grid->column('seo_title');
            $grid->column('seo_keywords');
            $grid->column('seo_description');
            $grid->column('attrlist_id');
            $grid->column('merchant_id');
            $grid->column('free_shipping');
            $grid->column('users_price');
            $grid->column('users_free');
            $grid->column('old_price');
            $grid->column('sales_num');
            $grid->column('stock_count');
            $grid->column('stock_show');
            $grid->column('prom_type');
            $grid->column('tempview');
            $grid->column('status');
            $grid->column('sort_order');
            $grid->column('lang');
            $grid->column('admin_id');
            $grid->column('users_id');
            $grid->column('arc_level_id');
            $grid->column('restric_type');
            $grid->column('is_del');
            $grid->column('del_method');
            $grid->column('joinaid');
            $grid->column('downcount');
            $grid->column('appraise');
            $grid->column('collection');
            $grid->column('htmlfilename');
            $grid->column('province_id');
            $grid->column('city_id');
            $grid->column('area_id');
            $grid->column('add_time');
            $grid->column('update_time');
            $grid->column('no_vip_pay');*/

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->expand(false);
                $filter->equal('id')->width(2);
                $filter->like('title')->width(3);
                $filter->equal('typeid')->select(ArctypeModel::selectOptions())->width(4);
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id) {
        return Show::make($id, new Archive(), function (Show $show) {
            $show->field('id');
            $show->field('typeid');
            $show->field('stypeid');
            $show->field('channel');
            $show->field('is_b');
            $show->field('title');
            $show->field('subtitle');
            $show->field('litpic');
            $show->field('is_head');
            $show->field('is_special');
            $show->field('is_top');
            $show->field('is_recom');
            $show->field('is_jump');
            $show->field('is_litpic');
            $show->field('is_roll');
            $show->field('is_slide');
            $show->field('is_diyattr');
            $show->field('origin');
            $show->field('author');
            $show->field('click');
            $show->field('arcrank');
            $show->field('jumplinks');
            $show->field('ismake');
            $show->field('seo_title');
            $show->field('seo_keywords');
            $show->field('seo_description');
            $show->field('attrlist_id');
            $show->field('merchant_id');
            $show->field('free_shipping');
            $show->field('users_price');
            $show->field('users_free');
            $show->field('old_price');
            $show->field('sales_num');
            $show->field('stock_count');
            $show->field('stock_show');
            $show->field('prom_type');
            $show->field('tempview');
            $show->field('status');
            $show->field('sort_order');
            $show->field('lang');
            $show->field('admin_id');
            $show->field('users_id');
            $show->field('arc_level_id');
            $show->field('restric_type');
            $show->field('is_del');
            $show->field('del_method');
            $show->field('joinaid');
            $show->field('downcount');
            $show->field('appraise');
            $show->field('collection');
            $show->field('htmlfilename');
            $show->field('province_id');
            $show->field('city_id');
            $show->field('area_id');
            //$show->field('add_time');
            //$show->field('update_time');
            $show->field('no_vip_pay');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        // 获取分类ID
        $this->typeid = \Request::get('typeid', '');
        if(!empty($this->typeid)){
            $this->channel = ArctypeModel::where('id', $this->typeid)->value('current_channel');
        }
        return Form::make(ArchiveModel::with(['types', 'downlist']), function (Form $form) {
            if($form->isEditing()){
                $this->typeid = $form->model()->typeid;
                $this->channel = ArctypeModel::where('id', $this->typeid)->value('current_channel');
            }
            $form->tools(function (Form\Tools $tools) {
                // 去掉跳转列表按钮
                $tools->disableList();
                // 去掉跳转详情页按钮
                $tools->disableView();
                // 去掉删除按钮
                $tools->disableDelete();
                // 添加一个按钮, 参数可以是字符串, 匿名函数, 或者实现了Renderable或Htmlable接口的对象实例
                $tools->append('<a href="' . admin_url('/archive') . '?typeid=' . $this->channel . '" class="btn btn-sm"><i class="feather icon-list"></i>&nbsp;&nbsp;返回列表</a>');
            });
            $form->tab('基础内容', function (Form $form) {
                $typeid = \Request::get('typeid', '');
                $form->display('id');
                $form->select('typeid')->options(ArctypeModel::selectOptions())
                    ->when('notIn',0, function (Form $form) {
                        //$select_value = 84; // 获取 select 选中值 查询分类所使用的内容模型
                        //$channel = ArctypeModel::where('id', $select_value)->value('current_channel');
                        // todo 暂时不能解决 根据选中值 返回不同的表单内容
                        switch ($this->channel) {
                            case 2:  //  产品内容模型
                                //$form->mediaSelector('img_collect', '图集');
                                break;
                            case 3: // 图集内容模型
                                $form->mediaSelector('img_collect', '图集');
                                break;
                            case 4: //  下载内容模型
                                $form->select('arc_level_id', '下载限制')->options(ArctypeModel::$arc_level_arr)->required();
                                /*$form->file('upfile', '上传文件')->rules(function (Form $form) {
                                    return 'nullable|zip,tar,xls';
                                })->accept('zip,tar,xls');*/

                                $form->mediaSelector('upfile', ' 上传文件');
                                /*$form->hasMany('downlist', function (Form\NestedForm $form) {
                                    $form->text('title','标题');
                                    $form->text('file_size','大小');
                                    //$form->datetime('completed_at');
                                });*/
                                if ($form->isCreating()) {
                                    $form->html($this->getFilelist());
                                } else {
                                    $form->html($this->getFilelist($form->getKey()));
                                }

                                break;
                            case 5: // 单页内容模型

                                break;
                            case 6: // 留言模型
                                break;
                            case 7: // 招聘模型
                                break;
                        }
                    })->default($typeid)->required();
                $form->text('stypeid');
                $form->hidden('channel')->options(ArctypeModel::getChannelTypeList());
                $form->text('is_b');
                $form->text('title')->required();
                $form->text('subtitle');
                //$form->file('litpic')->disk('admin')->accept('jpg,png,gif,jpeg')->saveFullUrl()->autoUpload();
                /*$form->mediaSelector('litpic')
                    ->options(['type' => 'image'])
                    ->help('上传或选择一个图片');*/
                $form->photo('photo1','图片1')
                    ->nametype('datetime')
                    ->remove(true)
                    ->help('单图，可删除');
                $form->image('litpic')->rules(function (Form $form) {
                    return 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,webp,avif';
                })->accept('jpg,png,gif,jpeg', 'image/*')->saveFullUrl()->autoUpload()->required();

                $form->editor('content', '文章内容')->required();


            })
                ->tab('SEO优化', function (Form $form) {
                $form->text('seo_title');
                $form->text('seo_keywords');
                $form->text('seo_description');})
                ->tab('更多设置', function (Form $form) {
                $form->text('author');
                $form->text('origin');
                $form->text('click');
                $form->text('downcount');
                $arcrank_arr = [
                    '0'   => '开放浏览',
                    '-1'  => '待审核稿件',
                    '10'  => '注册会员',
                    '50'  => '中级会员',
                    '100' => '高级会员'
                ];
                $form->select('arcrank')->options($arcrank_arr);
                $form->switch('is_head');
                $form->switch('is_special');
                $form->switch('is_top');
                $form->switch('is_recom');
                $form->switch('is_jump');
                $form->switch('is_litpic');
                $form->switch('is_roll')->value(0);
                $form->switch('is_slide')->value(0);
                $form->switch('is_diyattr')->value(0);
                $form->text('jumplinks');
                $form->switch('ismake');

                $form->hidden('attrlist_id')->value(1);
                $form->text('merchant_id')->value(0);
                $form->text('free_shipping')->value(0);
                $form->text('users_price')->value(0.00);
                $form->switch('users_free')->value(0);
                $form->text('old_price')->value(0);
                $form->text('sales_num')->value(0);
                $form->text('stock_count')->value(0);
                $form->switch('stock_show')->value(0);
                $form->text('prom_type');
                $form->text('tempview');
                //$form->switch('status');
                //$form->text('sort_order');
                //$form->text('lang');
                $form->hidden('admin_id')->value(Admin::guard()->user()->id);
                $form->text('users_id')->value(Admin::guard()->user()->id);

                $form->text('restric_type');
            })->if(function (Form $form) { //  如果是产品模型
                return $this->channel == 2;
                })->then(function (Form $form) {
                         /*$form->tab('产品设置',function (Form $form) {
                             $form->text('sku','颜色');
                         });*/
               });
            // 保存后回调
            $form->saved(function (Form $form, $result) {
                // 判断是否是新增操作
                if ($form->isCreating()) {
                    // 自增ID
                    $newId = $result;
                    // 也可以这样获取自增ID
                    $newId = $form->getKey();

                    if (!$newId) {
                        return $form->error('数据保存失败');
                    }

                    return false;
                }

            });

            // 在新增页面调用
            $form->creating(function (Form $form) {
                $typeid = \Request::get('typeid');
                if (!\Request::has('typeid')) { // 验证逻辑
                    Modal::make('标题')->lg()->render();

                    //$form->responseValidationMessages('title', 'title格式错误');
                    // 如有多个错误信息，第二个参数可以传数组
                    //$form->responseValidationMessages('content', ['content格式错误', 'content不能为空']);
                }
            });
            // 在编辑页面调用
            $form->editing(function (Form $form) {
                $typeid = \Request::get('typeid');
                if (!\Request::has('typeid')) { // 验证逻辑
                    $form->responseValidationMessages('title', 'title格式错误');

                    // 如有多个错误信息，第二个参数可以传数组
                    $form->responseValidationMessages('content', ['content格式错误', 'content不能为空']);
                }
            });
            // 保存前回调
            /*$form->saving(function (Form $form) {
                $form->updates();
                info($updates);
                //$username = $form->model()->channel;
                $form->channel = 11;
            });*/
            // 在表单提交前调用，在此事件中可以修改、删除用户提交的数据或者中断提交操作
            $form->submitted(function (Form $form) {
                // 获取用户提交参数
                $typeid        = $form->typeid;
                $form->channel = ArctypeModel::where('id', $typeid)->value('current_channel');

                /*// 上面写法等同于
                $title = $form->input('title');

                // 删除用户提交的数据
                $form->deleteInput('title');

                // 中断后续逻辑
                return $form->response()->error('服务器出错了~');*/
            });
            /*$form->switch('is_del');
            $form->text('del_method');
            $form->text('joinaid');

            $form->text('appraise');
            $form->text('collection');
            $form->text('htmlfilename');
            $form->text('province_id');
            $form->text('city_id');
            $form->text('area_id');
            $form->text('no_vip_pay');*/
        });
    }

    // 下载列表
    public function getFilelist($aid = '') {
        //Admin::translation('download-file');
        return Grid::make(new DownloadFile(), function (Grid $grid) {
            $grid->model()->where('aid', 8)->orderBy('id', 'DESC');
            $grid->column('title', '标题');
            $grid->column('file_size', '文件大小');
            $grid->column('file_url', '文件地址')->link();
            $grid->disableCreateButton();
            //$grid->disableDeleteButton();
            $grid->disableRefreshButton();
            $grid->disableFilterButton();
            $grid->disablePagination();
            $grid->disableRowSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                //$actions->disableDelete();
                $actions->disableEdit();
                $actions->disableQuickEdit();
                $actions->disableView();
            });
        });
    }

}
