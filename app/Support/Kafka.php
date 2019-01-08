<?php
namespace App\Support;

class Kafka {
    private $producer;
    private $topic_list;
    public function __construct()
    {
        $this->topic_list = [];
        $kafka_config = config('database.connections.kafka');
        $this->producer = new \RdKafka\Producer();
        $this->producer->setLogLevel(LOG_DEBUG);
        $this->producer->addBrokers($kafka_config['host']);
    }

    /**
     * 生产数据
     * @param $topic
     * @param $message
     */
    public function produce($topic, $message) {
        try{
            $t = $this->get_topic($topic);
            $t->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        }
        catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * 获取topic
     * @param $topic topic名称
     * @return mixed|null
     */
    public function get_topic($topic) {
        if(!in_array($topic, $this->topic_list)) {
            $this->create_topic($topic);
        }

        return $this->topic_list[$topic];
    }

    /**
     * 创建topic
     * @param $topic topic名称
     */
    private function create_topic($topic) {
        $t = $this->producer->newTopic($topic);
        $this->topic_list[$topic] = $t;
    }
}