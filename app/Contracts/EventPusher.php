<?php
namespace App\Contracts;

interface EventPusher{
    public function say($name);
    public function getName();
}