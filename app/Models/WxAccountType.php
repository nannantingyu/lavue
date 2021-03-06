<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxAccountType extends Model
{
    protected $table = 'wx_account_type';

    protected $fillable = [
        'wx_id',
        'account_type',
        'account_description',
        'icon'
    ];

    public function accounts() {
        return $this->hasMany('App\Models\WxAccount', 'account_type', 'id');
    }
}