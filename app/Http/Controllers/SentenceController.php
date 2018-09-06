<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sentence;

class SentenceController extends Controller
{
    public function index(Request $request) {
        $maxId = Sentence::max("id");
        $id = rand(1, $maxId);
        return ["success"=>1, "data"=>Sentence::find($id)];
    }
}
