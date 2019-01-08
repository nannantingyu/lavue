<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Models\Article;
use App\Models\Category;

class TestController extends Controller {
    public function index() {
        session()->put('name', 12313);
    }

    public function index2() {
        return ["session"=>session()->all()];
    }
}
