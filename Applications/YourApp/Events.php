<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use Ws\Filter;

class Events
{
    // 发送给客户端的类型
    const SEND_MESSAGE = 'message'; // 发送消息
    const LOGIN_MESSAGE = 'login'; // 登录
    const LOGOUT_MESSAGE = 'logout'; // 登出
    const NUMBER_MESSAGE = 'number'; // 人数统计

    // 接收到的信息类型
    const RECEIVE_TYPE_MESSAGE = 'message'; // 发送消息
    const RECEIVE_TYPE_NUMBER = 'number'; // 人数统计
    
    const CLIENT_SYSTEM = 'system'; // 客户端类型 系统信息

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        $body = [
            'type' => self::LOGIN_MESSAGE,
            'client' => $client_id
        ];
        // 向所有人发送
        Gateway::sendToAll(json_encode($body), null, $client_id);
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
    public static function onMessage($client_id, $message)
    {
        // 处理不同类型的数据
        self::decodeMessage($client_id, $message);
    }
   
    /**
        * 当用户断开连接时触发
        * @param int $client_id 连接id
        */
    public static function onClose($client_id)
    {
        $body = [
            'type' => self::LOGOUT_MESSAGE,
            'client' => $client_id
        ];
        // 向所有人发送
        Gateway::sendToAll(json_encode($body), null, $client_id);
    }

    protected static function decodeMessage($client_id, $message)
    {
        $message = json_decode($message);
        switch ($message->type) {
            case self::RECEIVE_TYPE_MESSAGE:
                self::broadcastMessage($client_id, $message);
                break;
            case self::RECEIVE_TYPE_NUMBER:
                self::sendNumberMessage();
                break;
            default:
                break;
        }
    }

    protected static function broadcastMessage($client_id, $message)
    {
        $filter = new Filter();
        $text = $message->message;
        $text = $filter->filterText($text);

        $body = [
            'type'    => self::SEND_MESSAGE,
            'client'  => $client_id,
            'message' => $text,
            'userInfo' => $message->userInfo,
        ];

         // 向所有人发送
        Gateway::sendToAll(json_encode($body), null, $client_id);
    }

    protected static function sendNumberMessage()
    {
        $body = [
            'type' => self::NUMBER_MESSAGE,
            'client' => self::CLIENT_SYSTEM,
            'message' => Gateway::getAllClientCount(),
        ];
        Gateway::sendToCurrentClient(json_encode($body));
    }
}
