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
//        dump($request->url());  // Without Query String...
//        dump($request->path());
//        dump($request->fullUrl());  // With Query String...
//        dump($request->method());
//        dump($request->all());  //全部参数
//        if($request->has("age")){
//            dump("has age");
//        }
//
//        $this->test->say();
//        app()->make('food')->start();
//        $this->food->start();
//
//        Food::start();
//        \App\Facades\FoodFacade::start();

//        \App\Facades\FoodFacade::start();
//        \Food::start();
//        $food = resolve("food");
//        $food->start();
//
//        $food2 = \App::make("food");
//        $food2->start();
//
//        $food3 = app()->make('food');
//        $food3->start();

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