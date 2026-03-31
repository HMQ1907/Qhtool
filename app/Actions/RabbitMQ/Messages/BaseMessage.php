<?php

namespace App\Actions\RabbitMQ\Messages;

class BaseMessage
{
    protected $message;
    protected $source = 'eg-zero';

    public function __construct()
    {
        $this->createMessage();
    }

    protected function createMessage()
    {
        $this->message = [];
    }

    public function toJson()
    {
        return json_encode((object) $this->message);
    }
}
