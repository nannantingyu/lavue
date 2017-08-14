<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function incre(Request $request) {
        $id = $request->id;
        DB::table('weixin_article')->where('id', $id)->increment('favor');
    }
}
