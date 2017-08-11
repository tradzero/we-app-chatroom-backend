<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use Ws\Filter;
use Ws\Messages\BroadcastMessage;
use Ws\Messages\CountMessage;
use Ws\Messages\HeartbeatMessage;
use Ws\Messages\LoginMessage;
use Ws\Messages\LogoutMessage;
use Ws\Messages\CloseClientMessage;
use Ws\Messages\Message;

class Events
{
    // 接收到的信息类型
    const RECEIVE_TYPE_MESSAGE   = 'message'; // 发送消息
    const RECEIVE_TYPE_NUMBER    = 'number'; // 人数统计
    const RECEIVE_TYPE_HEARTBEAT = 'pong'; // 心跳回复
    const RECEIVE_TYPE_CLOSE     = 'close'; // 请求关闭连接

    /**
     * work初始化执行的方法
     *
     * @param [Worker] $businessWorker
     * @return void
     */
    public static function onWorkerStart($businessWorker)
    {
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        $messager = new LoginMessage($client_id);
        $messager->send();
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
        $messager = new LogoutMessage($client_id);
        $messager->send();
    }

    protected static function decodeMessage($client_id, $message)
    {
        $message = json_decode($message);

        switch ($message->type) {
            case self::RECEIVE_TYPE_MESSAGE:
                $messsager = new BroadcastMessage($client_id, $message);
                break;
            case self::RECEIVE_TYPE_NUMBER:
                $messsager = new CountMessage($client_id, $message);
                break;
            case self::RECEIVE_TYPE_HEARTBEAT:
                $messsager = new HeartbeatMessage($client_id, $message);
                break;
            case self::RECEIVE_TYPE_CLOSE:
                $messsager = new CloseClientMessage($client_id, $message);
                break;
            default:
                break;
        }
        $messsager->send();
    }
}
