<?php

namespace Ws\Messages;

use GatewayWorker\Lib\Gateway;

class CountMessage implements Message
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
        $body = [
            'type' => Message::NUMBER_MESSAGE,
            'client' => Message::CLIENT_SYSTEM,
            'message' => Gateway::getAllClientCount(),
        ];
        $this->body = $body;
        return $body;
    }

    public function send()
    {
        Gateway::sendToCurrentClient(json_encode($this->body));
    }
}
