<?php

namespace Ws\Messages;

interface Message
{
    // 发送给客户端的类型
    const SEND_MESSAGE = 'message'; // 发送消息
    const LOGIN_MESSAGE = 'login'; // 登录
    const LOGOUT_MESSAGE = 'logout'; // 登出
    const NUMBER_MESSAGE = 'number'; // 人数统计

    const CLIENT_SYSTEM = 'system'; // 客户端类型 系统信息
    
    public function buildBody();

    public function send();
}
