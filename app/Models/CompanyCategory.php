<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CompanyCategory extends Model {
    protected $table = 'company_category';
    protected $fillable = ['cid', 'ccid'];
}