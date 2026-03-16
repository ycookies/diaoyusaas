<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CaseArticle extends Model
{
	public $connection = 'case';
    protected $table = 'case_articles';
    
}
