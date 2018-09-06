<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxUser extends Model
{
    protected $table = 'wx_user';

    protected $fillable = [
        'wx_id',
        'avatar',
        'wx_name',
        'province',
        'city',
        'country',
        'gender'
    ];
}