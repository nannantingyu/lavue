<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 活动
 * @package App\Models
 */
class Banner extends Model
{
    protected $table = 'banner';

    protected $fillable = [
        'title',
        'page',
        'image',
        'link',
        'cid',
        'hits',
        'state',
        'sequence',
        'expire_time',
    ];
}