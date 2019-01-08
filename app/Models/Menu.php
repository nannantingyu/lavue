<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = ['name', 'url', 'target', 'area', 'sequence', 'state', 'title', 'keywords', 'description'];
}