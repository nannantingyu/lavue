<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxAccount extends Model
{
    protected $table = 'wx_account';

    protected $fillable = [
        'wx_id',
        'account_type',
        'account_name',
        'account_single_price',
    ];
}