<?php
namespace App\ViewComposers;

class ProfileComposer{
    private $name;
    public function __construct($name) {
        $this->name = $name;
    }

    public function compose(View $view) {
        $view->with(['name'=>$this->name]);
    }
}