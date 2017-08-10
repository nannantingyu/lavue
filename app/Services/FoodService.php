<?php
namespace App\Services;

class FoodService {
    private $food;
    public function __construct($food){
        $this->food = $food;
    }

    public function start() {
        dump("Today's food is ".$this->food);
    }
}
