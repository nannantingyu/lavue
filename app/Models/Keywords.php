<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Keywords extends Model
{
    public function hotkeywords($starttime, $skip=0, $num=10) {
        $hotkeywords = DB::table("keywords_map")
            ->where("created_time", ">=", $starttime)
            ->groupBy("keyword")
            ->select(DB::Raw("count(*) as cou, keyword"))
            ->orderBy("cou", "desc")
            ->skip($skip)
            ->take($num)
            ->get();

        return $hotkeywords;
    }
}