<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HqController extends Controller
{
    //
    public function index(Request $request) {
        $seo_title = "比特币,以太币等区块币行情";
        $seo_description = "比特币,以太币等区块币行情";
        $seo_keywords = "区块链,行情,比特币,以太币";
        return view('hq/btc', ["seo_title"=>$seo_title, "seo_description"=>$seo_description, "seo_keywords"=>$seo_keywords]);
    }
}
