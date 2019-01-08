<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HotBanner extends Model {
    protected $table = 'hot_banner';
    protected $fillable = ['title', 'image', 'link', 'state', 'sequence'];
}