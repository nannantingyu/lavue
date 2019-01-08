<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArticleBody extends Model {
    protected $table = 'weixin_article_detail';
    protected $fillable = ['id', 'body'];
}