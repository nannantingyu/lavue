<?php
namespace App\Services;
use App\Contracts\EventPusher;

class RedisEventPusher implements EventPusher {
    private $name;

    public function __constrcut($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function say($name) {
        $this->name = $name;
        dd("I am saying my name, ". $this->getName());
    }
}