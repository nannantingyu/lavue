<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BlockCoin extends Model {
    protected $table = 'block_coin';

    protected $fillable = ['coin_id', 'coin_name', 'symble', 'state', 'sequence'];
}