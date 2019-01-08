<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * huodong
 * @package App\Models
 */
class BannerCategory extends Model
{
    protected $table = 'category_banner';
    protected $fillable = [
        'name',
        'pid',
        'sequence',
        'state',
    ];
}