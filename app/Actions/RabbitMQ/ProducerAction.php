<?php
// Cspell:ignore microzero
namespace App\Actions\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class ProducerAction extends RabbitMQAction
{
    public function publishMessage(
        string $message,
        string $routingKey = 'order.backend.strategy',
        string $exchange = 'microzero-topic'
    ): void {
        $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }
}
