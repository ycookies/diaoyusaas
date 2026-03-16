<?php

namespace App\Models\Hotel;


use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\ModelTree;

class Category extends Model
{
    use ModelTree;
    protected $table = 'category';

    // 父级ID字段名称，默认值为 parent_id
    //protected $parentColumn = 'pid';

    // 排序字段名称，默认值为 order
    protected $orderColumn = 'sort_order';

    // 标题字段名称，默认值为 title
    protected $titleColumn = 'name';

    // Since v2.1.6-beta，定义depthColumn属性后，将会在数据表保存当前行的层级
    protected $depthColumn = 'depth';

    public function setParentIdAttribute($extra) {
        if(empty($extra)) {
            $this->attributes['parent_id'] = 0;
        }else{
            $this->attributes['parent_id'] = $extra;
        }
    }


}
