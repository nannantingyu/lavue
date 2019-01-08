<?php
namespace App\Support;

class LinkList {
    private $head;

    public function __construct()
    {
        $this->head = new Node();
    }

    /**
     * 头插法创建链表
     */
    public function createListHead(array $list) {
        foreach ($list as $val) {
            $node = new Node($val);
            $node->next = $this->head->next;
            $this->head->next = $node;
        }
    }

    /**
     *
     * @param array $list
     */
    public function createListRear(array $list) {
        $p = $this->head;

        while(!is_null($p->next)) {
            $p = $p->next;
        }

        foreach ($list as $val) {
            $node = new Node($val);
            $p->next = $node;
            $p = $p->next;
        }
    }

    /**
     * 遍历
     */
    public function travel() {
        $p = $this->head->next;
        while(!is_null($p)) {
            echo $p->data."->";
            $p = $p->next;
        }

        echo "<br/>";
    }

    /**
     * 获取链表长度
     * @return int
     */
    public function length() {
        $p = $this->head->next;
        $index = 0;
        while(!is_null($p)) {
            $p = $p->next;
            $index ++;
        }

        return $index;
    }

    /**
     * 获取指定位置的节点
     * @param $index 位置
     * @return Node|null
     */
    public function getNode($index) {
        if(!is_numeric($index) or $index < 0) {
            return null;
        }

        $j = 0;
        $p = $this->head;
        while(!is_null($p) && $j < $index) {
            $p = $p->next;
            $j ++;
        }

        return $p;
    }

    /**
     * 在指定位置前插入节点
     * @param $index
     * @param $data
     * @return bool
     */
    public function insertListPre($index, $data) {
        $node = $this->getNode($index - 1);
        if(is_null($node)) {
            return false;
        }

        $new_node = new Node($data);
        $new_node->next = $node->next;
        $node->next = $new_node;

        return true;
    }

    /**
     * 在指定位置后插入节点
     * @param $index
     * @param $data
     * @return bool
     */
    public function insertListAfter($index, $data) {
        $node = $this->getNode($index);
        if(is_null($node)) {
            return false;
        }

        $new_node = new Node($data);
        $new_node->next = $node->next;
        $node->next = $new_node;

        return true;
    }

    /**
     * 追加节点
     * @param $data
     */
    public function append($data) {
        $p = $this->head;
        while(!is_null($p->next)) {
            $p = $p->next;
        }

        $node = new Node($data);
        $p->next = $node;
    }

    /**
     * 删除节点
     * @param $index
     * @return bool
     */
    public function delete($index) {
        $j = 0;
        $p = $this->head;
        while($index - 1 > $j && !is_null($p->next)) {
            $j ++;
            $p = $p->next;
        }

        if(!is_null($p) && !is_null($p->next)) {
            $q = $p->next;
            $p->next = $q->next;

            return true;
        }

        return false;
    }
}