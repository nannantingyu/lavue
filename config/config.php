<?php

return [
    'static_topic' => env('STATIC_PAGE_TOPIC', 'jujin8_template'),
    'static_topic_delete' => env('STATIC_PAGE_TOPIC_DELETE', 'jujin8_template_delete'),
    'static_topic_api' => env('STATIC_PAGE_TOPIC_API', 'jujin8_template_api'),
    'send_to_kafka' => env('SEND_TO_KAFKA', false) == 'true'
];