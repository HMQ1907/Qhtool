<?php

namespace App\Actions\RabbitMQ;

class ConsumerAction extends RabbitMQAction
{
    public function consumeMessages(
        callable $callback,
        string $queue = 'eg-zms'
    ): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);
        $this->channel->basic_qos(0, 1, null);
        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}
