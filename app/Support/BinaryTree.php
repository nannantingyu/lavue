<?php
namespace App\Support;

/**
 * 二叉树
 * 性质：
 *  1. 叶子节点的个数 = 度为2的个数 + 1
 *  2. 完全二叉树：除了叶子节点外，其他层的节点均为满的，最后一层，只有右侧可以有空节点
 *  3. 满二叉树：节点全满的二叉树
 *
 * 遍历：
 *  先序遍历：跟
 *  中序遍历：
 *  后序遍历：
 *      这里的序，指的是访问根节点的顺序
 *
 * Class BinaryTree
 * @package App\Support
 */
class BinaryTree {
    private $root = null;

    /**
     * BinaryTree constructor.
     */
    public function __construct() {
    }
}