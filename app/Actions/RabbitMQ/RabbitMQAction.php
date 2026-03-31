<?php

namespace App\Actions\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQAction
{
    protected $connection;
    protected $channel;
    protected $config;

    public function __construct()
    {
        $this->config = config('rabbitmq');
    }

    public function open(): void
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'],
            $this->config['port'],
            $this->config['user'],
            $this->config['password'],
            $this->config['virtual_host']
        );
        $this->channel = $this->connection->channel();
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
