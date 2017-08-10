<?php

namespace Ws\Messages;

use GatewayWorker\Lib\Gateway;

class LogoutMessage implements Message
{
    public $body;

    protected $client_id;
    protected $message;

    public function __construct($client_id, $message = null)
    {
        $this->client_id = $client_id;
        $this->message = $message;
        $this->buildBody();
    }

    public function buildBody()
    {
        $body = [
            'type' => Message::LOGOUT_MESSAGE,
            'client' => $this->client_id
        ];
        $this->body = $body;
        return $body;
    }

    public function send()
    {
        Gateway::sendToAll(json_encode($this->body), null, $this->client_id);
    }
}
