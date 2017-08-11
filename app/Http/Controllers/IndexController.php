<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FoodService;

class IndexController extends Controller
{
    public function __construct(FoodService $food) {
        $this->food = $food;
    }

    public function index(Request $request) {
        $name = "nannantingyu";
        $password = "abc123";

        $articles = DB::table('article')->take(10)->get();

        return view("index.index", ['name'=>$name, 'password'=>$password, 'articles'=>$articles]);
    }

    public function detail(Request $request) {
        $id = $request->id;
        $article = DB::table('article')->where('id', $id)->first();

        $hot = DB::table('article')->orderBy('hits', 'desc')->take(5)->get();
        return view('index.detail', ['article'=>$article, 'hot'=>$hot]);
    }

    public function search(Request $request) {
        $keywords = $request->keywords;
        $articles = DB::table('article')->where('title', 'like', '%'.$keywords.'%')
            ->orWhere('keywords', 'like', '%'.$keywords.'%')
            ->orWhere('body', 'like', '%'.$keywords.'%')
            ->paginate(15);

        return view('index.search', ['articles'=>$articles]);
    }
}