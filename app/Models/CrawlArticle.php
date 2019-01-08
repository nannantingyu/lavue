<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CrawlArticle extends Model {
    protected $table = 'crawl_article';
    protected $fillable = ['url', 'state', 'user_id', 'categories'];
}