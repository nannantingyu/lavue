<?php
namespace App\Support;

/**
 * 二叉树节点
 * 一个二叉树节点有左右两个子节点，以及数据域
 * Class TreeNode
 * @package App\Support
 */
class TreeNode {
    private $left;
    private $right;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->left = null;
        $this->right = null;
    }

    public function __get($key) {
        if(isset($this->$key)) {
            return $this->$key;
        }

        return null;
    }

    public function __set($key, $val) {
        $this->$key = $val;
    }
}