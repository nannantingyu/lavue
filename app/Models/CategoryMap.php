<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 文章分类对应关系
 * @package App\Models
 */
class CategoryMap extends Model
{
    protected $table = 'category_map';

    protected $fillable = [
        'source_category',
        'source_site',
        'target',
        'state',
    ];
}