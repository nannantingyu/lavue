<?php
namespace App\Support;

/**
 * 二叉搜索树，又叫二叉排序树，即所有节点均比自己的左节点大，比所有的右节点小
 * 所有的“尾递归”都可以转为迭代函数
 * Class SearchTree
 * @package App\Support
 */
class SearchTree {
    private $root = null;

    /**
     * 插入节点
     * @param $value
     */
    public function insert($value) {
        if(is_null($this->root)) {
            $this->root = new TreeNode($value);
        }
        else {
            $p = $this->root;
            $new_node = new TreeNode($value);
            while(!is_null($p)) {
                if($p->data > $value) {
                    if(!is_null($p->left))
                    {
                        $p = $p->left;
                    }
                    else {
                        $p->left = $new_node;
                        break;
                    }
                }
                else {
                    if(!is_null($p->right)) {
                        $p = $p->right;
                    }
                    else {
                        $p->right = $new_node;
                        break;
                    }
                }
            }
        }
    }

    /**
     * 递归方式插入节点
     * @param $value
     */
    public function insertDg($value, $root) {
        if(is_null($root)) {
            $this->root = new TreeNode($value);
        }
        else {
            if($value < $root->data) {
                if(is_null($root->left)) {
                    $root->left = new TreeNode($value);
                }
                else {
                    $this->insertDg($value, $root->left);
                }
            }
            if($value < $root->data) {
                if(is_null($root->right)) {
                    $root->right = new TreeNode($value);
                }
                else {
                    $this->insertDg($value, $root->right);
                }
            }
        }
    }

    /**
     * 中序遍历
     */
    public function middleTravel($root=null) {
        $root = $root??$this->root;
        if(is_null($root)) {
            return;
        }

        if(!is_null($root->left)) {
            $this->middleTravel($root->left);
        }

        print($root->data."->");

        if(!is_null($root->right)) {
            $this->middleTravel($root->right);
        }
    }

    /**
     * 前序遍历
     * @param null $root
     */
    public function preTravel($root=null) {
        $root = $root??$this->root;
        if(is_null($root)) {
            return;
        }

        print($root->data."->");
        if(!is_null($root->left)) {
            $this->preTravel($root->left);
        }

        if(!is_null($root->right)) {
            $this->preTravel($root->right);
        }
    }

    /**
     * 后序遍历
     * @param null $root
     */
    public function afterTravel($root=null) {
        $root = $root??$this->root;
        if(is_null($root)) {
            return;
        }

        if(!is_null($root->left)) {
            $this->preTravel($root->left);
        }

        if(!is_null($root->right)) {
            $this->preTravel($root->right);
        }

        print($root->data."->");
    }

    /**
     * 广度优先遍历
     */
    public function travel() {
        $queue = new Queue();
        $queue->push($this->root);

        while(!$queue->is_empty()) {
            $data = $queue->pop();
            if(!is_null($data)) {
                print $data->data."->";
                if(!is_null($data->left)) {
                    $queue->push($data->left);
                }

                if(!is_null($data->right)) {
                    $queue->push($data->right);
                }
            }
        }
    }

    /**
     * 获取根节点
     * @return null
     */
    public function getRoot() {
        return $this->root;
    }
}
