<?php

namespace Ws\Messages;

use GatewayWorker\Lib\Gateway;

class CloseClientMessage implements Message
{
    public $body;

    protected $client_id;
    protected $message;

    public function __construct($client_id, $message)
    {
        $this->client_id = $client_id;
        $this->message = $message;
        $this->buildBody();
    }

    public function buildBody()
    {
    }

    public function send()
    {
        Gateway::closeClient($this->client_id);
    }
}
