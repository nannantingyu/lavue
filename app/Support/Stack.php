<?php
namespace App\Support;

class Stack {
    private $list = [];

    public function __construct($array = null)
    {
        if(is_array($array)) {
            $this->list = array_merge($this->list, $array);
        }
    }

    /**
     * 入栈
     * @param $value 入栈元素
     */
    public function push($value) {
        array_push($this->list, $value);
    }

    /**
     * 出栈
     * @return mixed|null 出栈元素，如果栈为空，返回null
     */
    public function pop() {
        if($this->is_empty()) {
            return null;
        }

        return array_pop($this->list);
    }

    /**
     * 判断栈是否为空
     * @return bool 为空返回true, 否则返回false
     */
    public function is_empty() {
        return empty($this->list);
    }

    /**
     * 获取当前栈的长度
     * @return mixed
     */
    public function length() {
        return count($this->list);
    }

    /**
     * 获取栈顶元素，但是不出栈
     * @return mixed|null
     */
    public function seek() {
        if($this->is_empty()) {
            return null;
        }

        return $this->list[$this->length() - 1];
    }

    /**
     * 遍历栈
     */
    public function travel() {
        dump(array_reverse($this->list));
    }

    /**
     * 获取栈的第index个元素
     * @param $index
     * @return mixed|null
     */
    public function indexOf($index) {
        if(is_null($index) or $index >= $this->length() or $index < 0) {
            return null;
        }

        return $this->list[$index];
    }
}