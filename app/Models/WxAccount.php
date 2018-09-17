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
        'single_price',
        'account_description',
        'icon'
    ];

    public function logs() {
        return $this->hasMany('App\Models\WxAccountLog', 'account_name', 'id');
    }
}