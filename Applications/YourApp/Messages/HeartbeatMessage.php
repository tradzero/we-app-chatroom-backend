<?php

namespace Ws\Messages;

class HeartbeatMessage implements Message
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
    }
}
