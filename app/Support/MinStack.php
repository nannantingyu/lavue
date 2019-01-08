<?php
namespace App\Support;

class MinStack {
    private $stack;
    private $stack_min;

    public function __construct($array=null)
    {
        $this->stack = new Stack();
        $this->stack_min = new Stack();
        if(is_array($array)) {
            foreach ($array as $val) {
                $this->push($val);
            }
        }
    }

    public function push($value) {
        $this->stack->push($value);
        if($this->stack_min->is_empty() or $this->get_min() >= $value) {
            $this->stack_min->push($this->stack->length()-1);
        }
    }

    public function pop() {
        $value = $this->stack->seek();
        if($this->get_min() == $value) {
            $this->stack_min->pop();
        }

        return $this->stack->pop();
    }

    public function indexOf($index) {
        return $this->stack->indexOf($index);
    }

    public function length() {
        return $this->stack->length();
    }

    public function is_empty() {
        return $this->stack->is_empty();
    }

    public function get_min() {
        return $this->stack->indexOf($this->stack_min->seek());
    }

    public function travel() {
        $this->stack->travel();
    }

    public function get_min_stack() {
        return $this->stack_min;
    }
}