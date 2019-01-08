<?php
namespace App\Support;

class Node {
    private $next;
    private $data;

    public function __construct($data=null) {
        $this->data = $data;
        $this->next = null;
    }

    public function __get($key) {
        if(isset($this->$key)) {
            return $this->$key;
        }

        return null;
    }

    public function __set($key, $value) {
        $this->$key = $value;
    }
}