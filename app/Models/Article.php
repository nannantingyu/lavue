<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model {
    protected $table = 'weixin_article';
    protected $fillable = ['title', 'image', 'description', 'type', 'keywords', 'author', 'publish_time', 'hits', 'state', 'recommend', 'favor', 'source_type'];

    public function body() {
        return $this->hasOne("App\Models\ArticleBody", 'id', 'id');
    }

    public function categories() {
        return $this->belongsToMany("App\Models\Category", 'article_category', 'aid', 'cid');
    }

    public function comments() {
        return $this->hasMany('App\Models\Comment', 'id', 'id');
    }
}