<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Company extends Model {
    protected $table = 'company';
    protected $fillable = ['name', 'shortname', 'link', 'tag', 'logo'];

    public function categories() {
        return $this->belongsToMany("App\Models\CategoryCompany", 'company_category', 'cid', 'ccid');
    }
}