<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kuaixun;

class KuaixunController extends Controller
{
    public function getkx(Request $request) {
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $ret = $kuaixun->getKuaixun($request->input('page'), $request->input('num'), $date);

        return ['success'=>1, 'value'=>array_values($ret)];
    }

    public function kuaixun(Request $request) {
        $kuaixun = new Kuaixun();
        $date = $request->input('st', null);
        $ret = $kuaixun->getKuaixun($request->input('page', 1), $request->input('num', 20), $date);

        return view("index.kuaixun", ['kx'=>$ret]);
    }
}
