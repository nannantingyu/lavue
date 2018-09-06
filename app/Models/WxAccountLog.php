<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxAccountLog extends Model
{
    protected $table = 'wx_account_log';

    protected $fillable = [
        'wx_id',
        'account_type',
        'single_price',
        'amount',
        'start_time',
        'end_time'
    ];
}