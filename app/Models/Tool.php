<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $table = 'tool';

    protected $fillable = ['title', 'description', 'image', 'tag', 'sequence', 'state','url'];
}