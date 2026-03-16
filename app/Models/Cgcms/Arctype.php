<?php

namespace App\Models\Cgcms;

use Dcat\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class Arctype extends BaseModel
{
    use ModelTree;
    protected $table = 'arctype';
    //public $timestamps = false;

    // 父级ID字段名称，默认值为 parent_id
    protected $parentColumn = 'parent_id';

    // 排序字段名称，默认值为 order
    protected $orderColumn = 'sort_order';

    // 标题字段名称，默认值为 title
    protected $titleColumn = 'title';

    // Since v2.1.6-beta，定义depthColumn属性后，将会在数据表保存当前行的层级
    protected $depthColumn = 'depth';

    /*
     * <option value="1" data-nid="article">文章模型</option>
                        <option value="2" data-nid="product">产品模型</option>
                        <option value="3" data-nid="images">图集模型</option>
                        <option value="4" data-nid="download">下载模型</option>
                        <option value="6" data-nid="single" selected="true">单页模型</option>
                        <option value="8" data-nid="guestbook">留言模型</option>
                        <option value="9" data-nid="recruit">招聘模型</option>
    */
    public static $channeltype_list = [
        1 => [
            'id' => 1,
            'name' => '文章模型',
            'nid' => 'article',
        ],
        2 => [
            'id' => 2,
            'name' => '产品模型',
            'nid' => 'product',
        ],
        3 => [
            'id' => 3,
            'name' => '图集模型',
            'nid' => 'images',
        ],
        4 => [
            'id' => 4,
            'name' => '下载模型',
            'nid' => 'download',
        ],
        5 => [
            'id' => 5,
            'name' => '单页模型',
            'nid' => 'single',
        ],
        6 => [
            'id' => 6,
            'name' => '留言模型',
            'nid' => 'guestbook',
        ],
        7 => [
            'id' => 7,
            'name' => '招聘模型',
            'nid' => 'recruit',
        ],
    ];

    public static  $arc_level_arr = [
        0 => '不限会员',
        1 => '注册会员',
        2 => '中级会员',
        3 => '高级会员',

    ];

    public static function getChannelTypeList(){
        $channeltype_list = self::$channeltype_list;
        $list = [];
        foreach ($channeltype_list as $key => $items) {
            $list[$items['id']] = $items['name'];
        }
        return $list;
    }
}
