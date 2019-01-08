<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Module extends Model{
    protected $table = 'admin_module';
    protected $fillable = ['id', 'name', 'path', 'pid', 'sequence', 'display'];
}