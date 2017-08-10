<?php

namespace Ws\Messages;

use Ws\Filter;
use GatewayWorker\Lib\Gateway;

class BroadcastMessage implements Message
{
    protected $filter;
    public $body;

    protected $client_id;
    protected $message;
    public function __construct($client_id, $message)
    {
        $this->client_id = $client_id;
        $this->message = $message;

        $filter = new Filter();
        $this->filter = $filter;

        $this->buildBody();
    }

    public function buildBody()
    {
        $message = $this->message;
        $text = $message->message;
        $text = $this->filter->filterText($text);

        $body = [
            'type'    => Message::SEND_MESSAGE,
            'client'  => $this->client_id,
            'message' => $text,
            'userInfo' => $message->userInfo,
        ];
        $this->body = $body;
        return $body;
    }

    public function send()
    {
        Gateway::sendToAll(json_encode($this->body), null, $this->client_id);
    }
}
