<?php

namespace Lake\FormMedia\Form\Field;

use Lake\FormMedia\Form\FieldIconimg;

/**
 * 表单图标单图字段
 *
 * @create 2020-11-25
 * @author deatil
 */
class Iconimg extends FieldIconimg
{
    protected $limit = 1;
    
    protected $remove = false;
    
    protected $type = 'image';
}
