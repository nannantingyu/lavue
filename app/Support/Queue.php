<?php
namespace App\Support;

class Queue {
    private $list;

    public function __construct($array=null)
    {
        $this->list = array();
        if(is_array($array)) {
            foreach ($array as $val) {
                $this->push($val);
            }
        }
    }

    /**
     * 入队
     * @param $val
     */
    public function push($val) {
        array_push($this->list, $val);
    }

    /**
     * 出队
     * @return mixed|null
     */
    public function pop() {
        if($this->is_empty()) {
            return null;
        }
        return array_shift($this->list);
    }

    /**
     * 获取队列长度
     * @return int
     */
    public function length() {
        return count($this->list);
    }

    /**
     * 队列是否为空
     * @return bool
     */
    public function is_empty() {
        return $this->length() === 0;
    }

    /**
     * 查看队列头元素，不出队
     * @return null
     */
    public function seek() {
        if($this->is_empty()) {
            return null;
        }

        return $this->list[0];
    }

    /**
     * 便利队列
     */
    public function travel() {
        foreach ($this->list as $val) {
            echo $val."->";
        }

        echo "<br/>";
    }

    /**
     * 获取队列$index元素
     * @param $index
     * @return null
     */
    public function indexOf($index) {
        if($index < 0 or $index > $this->length() - 1) {
            return null;
        }

        return $this->list[$index];
    }
}