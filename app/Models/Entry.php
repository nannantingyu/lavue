<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model {
    protected $table = 'entry';
    protected $fillable = ['entry_name', 'description', 'sequence', 'state'];

    public function articles() {
        return $this->belongsToMany("App\Models\Article", 'entry_article', 'entry_id', 'article_id');
    }
}