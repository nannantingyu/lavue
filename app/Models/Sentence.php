<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{
    protected $table = 'sentence';

    protected $fillable = [
        'content',
        'favor',
    ];
}