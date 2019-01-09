<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ArticleBody extends Model {
    protected $table = 'weixin_article_detail';
    const UPDATED_AT = 'updated_time';
    const CREATED_AT = 'created_time';
    protected $fillable = ['id', 'body'];
}