<?php

namespace App\Models\Cgcms;


use Illuminate\Database\Eloquent\Model;
use Dcat\Admin\Traits\Resizable;

class Archive extends BaseModel
{
    use Resizable;
    //protected $primaryKey = 'aid';

    public $timestamps = false;
    public function contents()
    {
        return $this->belongsTo(ArticleContent::class,'id','aid');
    }

    // 栏目
    public function types(){
        return $this->belongsTo(Arctype::class,'typeid','id');
    }

    public function downlist(){
        return $this->hasMany(DownloadFile::class, 'aid');
    }
}
