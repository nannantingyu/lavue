<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Config extends Model {
    protected $table = 'config';
    protected $fillable = ['key', 'value', 'group', 'state', 'sequence', 'id', 'comment'];
}