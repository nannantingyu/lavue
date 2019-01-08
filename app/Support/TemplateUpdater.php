<?php
namespace App\Support;

class TemplateUpdater {
    private $producer;
    private $topic_list;
    public function __construct(Kafka $kafka)
    {
        $this->kafka = $kafka;
        $this->static_topic = config('config.static_topic');
        $this->static_topic_delete = config('config.static_topic_delete');
        $this->static_topic_api = config('config.static_topic_api');
        $this->send_to_kafka = config('config.send_to_kafka');
    }

    public function produce($topic, $page) {
        if($this->send_to_kafka) {
            $this->kafka->produce($topic, $page);
            return true;
        }

        return false;
    }

    /**
     * 更新某一页的数据
     * @param $page 更新的url
     */
    public function update_page($page) {
        return $this->produce($this->static_topic, $page);
    }

    /**
     * 删除某一页
     * @param $page 删除的url
     */
    public function delete_page($page) {
        return $this->produce($this->static_topic_delete, $page);
    }

    /**
     * 调用api
     * @param $page api地址
     */
    public function call_api($page) {
        return $this->produce($this->static_topic_api, $page);
    }

    /**
     * 统一入口
     * @param $page
     * @param $type
     */
    public function update($page, $type='update') {
        $result = false;

        switch ($type) {
            case 'update':
                $result = $this->update_page($page);
                break;
            case 'delete':
                $result = $this->delete_page($page);
                break;
            case 'api':
                $result = $this->delete_page($page);
                break;
        }

        return $result;
    }
}